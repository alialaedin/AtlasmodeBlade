<?php

namespace Modules\Order\Services\Validations\Customer;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Admin;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Variety;
use Modules\Shipping\Entities\Shipping;
use Modules\Cart\Entities\Cart;
use Modules\Core\Classes\SafeArray;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Services\CalculateCouponDiscountService;
use Modules\Order\Classes\OrderStoreProperties;
use Modules\Product\Entities\Product;
use Modules\Customer\Entities\Customer;

class OrderValidationService
{

	protected SafeArray $request;
	protected Customer $customer;
	protected $sumCartsPrice;
	protected $sumCartsPriceWithoutShipping;
	protected $discountAmount;
	protected $shippingAmount;
	protected $customerAddress;

	protected $discountOnOrder;
  protected $discountOnCoupon;
  protected $discountOnItems;
  protected $totalAmount;
  protected $totalItemsAmount;
  protected $totalItemsAmountWithoutDiscount;
  protected $itemsCount;
  protected $itemsQuantity;
	public OrderStoreProperties $properties;

	public mixed $totalQuantity = 0;

	public function __construct(array $request, Customer $customer, public $byAdmin = false, public $varieties = null)
	{
		$this->properties = new OrderStoreProperties();
		$this->request = new SafeArray($request);
		$this->customer = $customer;
		$this->checkAll();
	}

	public function checkAll()
	{
		$carts = $this->customer->carts;
		if (!$this->byAdmin && $carts->count() < 1) {
			throw Helpers::makeValidationException('سبد خرید شما خالی است!');
		}

		if ($this->byAdmin) {
			$carts = [];
			$this->checkAdminVarieties();
			$this->checkAvailableVariety($this->varieties);
		} else {
			foreach ($carts as $cart) {
				$this->checkMaxQuantityVariety($cart);
				$this->checkAvailableVariety($cart->variety);
				$this->checkPrice($cart);
			}
		}
		$this->properties->carts = $carts;

		$this->checkShipping();
		$this->checkWallet();
		//        if (!$this->byAdmin) {
		//            if ($this->request['pay_wallet']) {
		//                $this->checkWallet();
		//            }
		//        }
	}

	public function checkAdminVarieties()
	{
		$varietyArray = $this->varieties ?: false;
		$varietyIds = collect($varietyArray)->pluck('id');
		$varieties = Variety::query()->whereIn('id', $varietyIds->toArray())->get();
		if (count($varietyIds) != count($varieties)) {
			throw Helpers::makeValidationException('شناسه تنوع های ارسال شده نامعتبر است');
		}
		foreach ($varietyArray as $cartItem) {
			foreach ($varieties as $index => $v) {
				if ($v['id'] == $cartItem['id']) {
					// Log::info($cartItem['id'] . ' -> ' . $cartItem['quantity'] . " -> " . $varieties[$index]['quantity']);
					if ($cartItem['quantity'] > $varieties[$index]['quantity']) {
						throw Helpers::makeValidationException(
							'تعداد انتخاب شده محصول ' . $varieties[$index]->product->title . ' بیشتر از موجودی انبار است.' . "(تنوع: " . $varieties[$index]->store->variety_id . ")",
							'variety_quantity'
						);
					}
				}
			}
			$this->totalQuantity += $cartItem['quantity'];
			//            Log::info("CHECK QUANTITY -> " . $this->totalQuantity);
		}
		//         foreach ($varietyArray as $key => $variety) {
		//             $baseVariety = $varieties[$key];
		//             if ($baseVariety->store->balance < $variety['quantity']) {
		//                 throw Helpers::makeValidationException(
		//                     'تعداد انتخاب شده محصول ' . $baseVariety->product->title . ' بیشتر از موجودی انبار است.' . "(تنوع: " . $baseVariety->store->variety_id . ")" . "(" . $variety['quantity'] . ")",
		//                     'variety_quantity'
		//                 );
		//             }
		//             $this->totalQuantity += $variety['quantity'];
		//         }
	}

	public function checkWallet()
	{
		$payHalfActive = app(CoreSettings::class)->get('invoice.pay_half.active');
		$finalPrice = $this->getSumCartsPrice() - $this->discountAmount;

		if (($payHalfActive || auth()->user() instanceof Admin) && ($this->customer->balance < $finalPrice)) {
			throw Helpers::makeValidationException('موجودی کیف پول شما کافی نیست');
		}
	}

	public function getSumCartsPrice()
	{
		if ($this->sumCartsPrice) {
			return $this->sumCartsPrice;
		}
		if (Auth::user() instanceof Admin) {
			$sumCartsPrice = 0;
			foreach ($this->request['varieties'] as $v) {
				$variety = Variety::query()->withCommonRelations()->findOrFail($v['id']);
				$sumCartsPrice += $variety->final_price['amount'] * $v['quantity'];
			}

			return $sumCartsPrice;
		}
		$carts = $this->customer->carts;

		$sumCartsPrice = 0;
		foreach ($carts as $cart) {
			$sumCartsPrice += ($cart->price * $cart->quantity);
		}

		return $this->sumCartsPrice = ($sumCartsPrice - $this->getDiscountAmount() + $this->getShippingAmount());
	}

	public function checkSumPriceWhenAdmin(): Collection
	{
		$carts = collect();
		foreach ($this->varieties as $variety) {
			$findVariety = Variety::query()->with(['product'])->find($variety['id']);
			$carts->push((object)[
				'price' => $findVariety->final_price['amount'],
				'quantity' => $variety['quantity']
			]);
		}

		return $carts;
	}
	public function getSumCartsPriceWithoutShipping(): float|int
	{

		$carts = ($this->byAdmin) ? $this->checkSumPriceWhenAdmin() :  $this->customer->carts;

		if ($this->sumCartsPriceWithoutShipping) {
			return $this->sumCartsPriceWithoutShipping;
		}

		$sumCartsPriceWithoutShipping = 0;
		foreach ($carts as $cart) {
			$sumCartsPriceWithoutShipping += ($cart->price * $cart->quantity);
		}
		return $this->sumCartsPriceWithoutShipping = ($sumCartsPriceWithoutShipping - $this->getDiscountAmount());
	}

	public function getRawSumCartsPrice(): float|int
	{
		$carts = ($this->byAdmin) ? $this->checkSumPriceWhenAdmin() :  $this->customer->carts;

		$sumCartsPrice = 0;
		foreach ($carts as $cart) {
			$sumCartsPrice += ($cart->price * $cart->quantity);
		}

		return $sumCartsPrice;
	}

	public function checkMaxQuantityVariety($cart)
	{
		if ($cart->variety->store->balance < $cart->quantity) {
			throw Helpers::makeValidationException(
				'تعداد انتخاب شده محصول ' . $cart->variety->product->title . ' بیشتر از موجودی انبار است.',
				'variety_quantity'
			);
		}
		if ($cart->variety->max_number_purchases < $cart->quantity) {
			throw Helpers::makeValidationException(
				"شما از تنوع {$cart->variety->product->title} فقط میتوانید {$cart->variety->max_number_purchases} خرید کنید",
				'variety_quantity'
			);
		}

		$this->totalQuantity += $cart->quantity;
	}

	public function checkAvailableVariety($variety)
	{
		if ($this->byAdmin) {
			$varietyArray = $variety ?: false;
			$varietyIds = collect($varietyArray)->pluck('id');
			foreach ($varietyIds as $id) {
				$variety = Variety::findOrFail($id);
				if (!in_array(
					$variety->product->status,
					[Product::STATUS_AVAILABLE, Product::STATUS_AVAILABLE_OFFLINE]
				)) {
					throw Helpers::makeValidationException(
						' محصول ' . $variety->product->title . ' در وضعیت قابل فروش نیست است. ',
						'status'
					);
				}
			}
		} else {
			if ($variety->product->status !== Product::STATUS_AVAILABLE) {
				throw Helpers::makeValidationException(
					' محصول ' . $variety->product->title . ' ناموجود است. ',
					'status'
				);
			}
		}
	}

	public function checkPrice(Cart $cart)
	{
		$variety = $cart->variety;
		if ($cart->price != $variety->final_price['amount']) {
			throw Helpers::makeValidationException('قیمت محصول تغییر کرد');
		}
		if ($cart->discount_price != $variety->final_price['discount_price']) {
			throw Helpers::makeValidationException('قیمت محصول تغییر کرد');
		}
	}

	public function checkShipping()
	{
		$address = $this->getAddress();
		if (!$address) {
			throw Helpers::makeValidationException('آدرس انتخاب شده نامعتبر است!', 'address_id');
		}
		$shipping = Shipping::find($this->request['shipping_id']);
		if (! $shipping->checkShippableAddress($address->city)) {
			throw Helpers::makeValidationException('شیوه ارسال انتخاب شده نامعتبر است.', 'shipping_id');
		}

		$deliveringDiffDays = Carbon::now()->diff($this->request['delivered_at'])->days;

		if ($deliveringDiffDays < $shipping->minimum_delay || $deliveringDiffDays > 10) {
			#TODO read max_delay in settings
			throw Helpers::makeValidationException('تاریخ تحویل انتخاب شده نامعتبر است.', 'shipping_id');
		}
		$this->properties->discount_amount = $this->getDiscountAmount() ?? 0;
		$this->properties->discountOnItems = $this->getDiscountOnItems() ?? 0;
		$this->properties->totalItemsAmount = $this->getTotalItemsAmount();
		$this->properties->totalItemsAmountWithoutDiscount = $this->getTotalItemsAmount() - $this->properties->discountOnItems;
		$this->properties->itemsCount = $this->byAdmin ? count($this->varieties) : $this->customer->carts->count();
		$this->properties->itemsQuantity = $this->byAdmin ? collect($this->varieties)->sum('quantity') : $this->customer->carts->sum('quantity');
		$this->properties->totalAmount = $this->getTotalAmount();
		$this->properties->discountAmount = $this->properties->discountOnItems + $this->properties->discountOnOrder + $this->properties->discountOnCoupon;
		$this->properties->address = $address;
		$this->properties->shipping_amount = $this->getShippingAmount() ?? 0;
	}

	public function getDiscountAmount()
	{
		// if ($this->discountAmount) {
		// 	return $this->discountAmount;
		// }
		$sumCartsPrice = $this->getRawSumCartsPrice();
		$discount = 0;
		if ($this->request['coupon_code']) {
			$coupon = Coupon::where('code', $this->request['coupon_code'])->first();
			if (! $coupon) {
				throw Helpers::makeValidationException('کوپن انتخاب شده موجود نیست!', 'shipping_id');
			}
			$discount = (new CalculateCouponDiscountService($coupon->code, $sumCartsPrice))->calculate()['discount'];
			$this->properties->coupon = $coupon;

			Coupon::dontAllowCouponAndDiscountTogether();
		}
		if (auth()->user() instanceof Admin) {
			if ($this->discountOnOrder) {
				$this->properties->discountOnOrder = $this->discountOnOrder;
			} else {
				$this->properties->discountOnOrder = $this->request['discount_amount'] ?? 0;
			}
			return $this->discountAmount = $this->request['discount_amount'];
		}

		if ($this->discountOnCoupon) {
			$this->properties->discountOnCoupon = $this->discountOnCoupon;
		} else {
			$this->properties->discountOnCoupon = $discount;
		}
		
		return $this->discountAmount = $discount;
	}

	public function getAddress()
	{
		if ($this->customerAddress) {
			return $this->customerAddress;
		}
		return $this->customerAddress = $this->customer->addresses->where('id', $this->request['address_id'])->first();
	}

	public function getShippingAmount()
	{
		if ($this->shippingAmount) {
			return $this->shippingAmount;
		}

		$city = $this->getAddress()->city;
		$shipping = Shipping::find($this->request['shipping_id']);
		$this->properties->shipping = $shipping;
		$this->properties->shipping_packet_amount = $shipping->getAreaPrice($city, $this->getSumCartsPriceWithoutShipping());
		if (isset($this->request['reserved']) && $this->request['reserved'] == true) {
			return $this->shippingAmount = $shipping->getPriceByReservation($city, $this->getSumCartsPriceWithoutShipping(), $this->totalQuantity, $this->customer, $this->customerAddress->id);
		}

		#todo $this->getSumCartsPrice() || $this->getRowSumCartsPrice() check in settings
		return $this->shippingAmount = $shipping->getPrice($city, $this->getSumCartsPriceWithoutShipping(), $this->totalQuantity);
	}

	public function getDiscountOnItems()
	{
		if($this->discountOnItems) {
			return $this->discountOnItems;
		}

		$discountOnItems = 0;

		if (Auth::user() instanceof Admin) {
			foreach ($this->request['varieties'] as $v) {
				$variety = Variety::query()->withCommonRelations()->findOrFail($v['id']);
				$discountOnItems += $variety->final_price['discount_price'] * $v['quantity'];
			}
		}

		$carts = $this->customer->carts;
		foreach ($carts as $cart) {
			$discountOnItems += ($cart->discount_price * $cart->quantity);
		}

		return $this->discountOnItems = $discountOnItems;
	}

	public function getTotalItemsAmount()
	{
		if($this->totalItemsAmount) {
			return $this->totalItemsAmount;
		}

		$totalAmount = 0;
		if ($this->byAdmin) {
			foreach ($this->varieties as $variety) {
				$findVariety = Variety::query()->with(['product'])->find($variety['id']);
				$totalAmount += $findVariety->final_price['amount'] * $variety['quantity'];
			}
		} else {
			foreach ($this->customer->carts as $cart) {
				$findVariety = Variety::query()->with(['product'])->find($cart->variety_id);
				$totalAmount += $findVariety->final_price['amount'] * $cart->quantity;
			}
		}
		
		return $this->totalItemsAmount = $totalAmount;
	}

	public function getTotalAmount() 
	{
		if($this->totalAmount) {
			return $this->totalAmount;
		}

		$shippingAmount = $this->getShippingAmount();

		return $this->totalAmount = $this->totalItemsAmount + $shippingAmount - ($this->discountOnCoupon ?? 0) - ($this->discountOnOrder ?? 0);
	}

}
