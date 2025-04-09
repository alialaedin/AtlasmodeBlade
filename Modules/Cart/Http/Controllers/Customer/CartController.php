<?php

namespace Modules\Cart\Http\Controllers\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Area\Entities\Province;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Http\Requests\Admin\CartStoreRequest;
use Modules\Cart\Http\Requests\Admin\CartUpdateRequest;
use Modules\Customer\Entities\Customer;
use Modules\Invoice\Entities\Payment;
use Modules\Product\Entities\Variety;
use Modules\Shipping\Entities\Shipping;
use Modules\Shipping\Services\ShippingCalculatorService;

class CartController extends Controller
{
  private Customer $customer;
  protected function __construct()
  {
    /**
     * @var \Modules\Customer\Entities\Customer $customer
     */
    $customer = auth('customer')->user();
    abort_if(!$customer, 401);
    $this->customer = $customer;
  }

  public function index()
  {
    $this->customer->load(['addresses' => function ($q) {
      $q->select(['id', 'city_id', 'address', 'customer_id', 'first_name', 'last_name', 'mobile', 'postal_code', 'telephone']);
      $q->with('city');
    }]);

    $carts = $this->customer->carts()
      ->with('variety', function ($vQuery) {
        $vQuery->select(['id', 'product_id']);
        $vQuery->with('attributes');
        $vQuery->with('product', function ($pQuery) {
          $pQuery->select(['id', 'title']);
        });
      })->get();

    // $cartsWarnings = (new WarningMessageCartService($carts))->checkAll();
    $hasFreeShippingProduct = Cart::hasfreeShippingProduct($carts);

    $shippings = Shipping::getActiveShippings();
    $gateways = Payment::getAvailableDriversForFront();
    $provinces = Province::getAllProvinces(true);

    return view('cart::front.index', compact([
      'carts',
      'hasFreeShippingProduct',
      // 'cartsWarnings',
      'customer',
      'shippings',
      'gateways',
      'provinces',
    ]));
  }

  public function add(CartStoreRequest $request): JsonResponse
  {
    $cart = Cart::addOrUpdateQuantity($request->variety, $request->quantity);
    return response()->success('محصول با موفقیت به سبد خرید اضافه شد', compact('cart'));
  }

  public function update(CartUpdateRequest $request, $id)
  {
    $cart = $request->cart;
    $isIncrement = $cart->quantity < $request->quantity;
    $cart->quantity = $request->quantity;
    $cart->save();
    $cart =  $cart->loadNecessaryRelations();

    return response()->success(
      $isIncrement
        ? 'تعداد محصول با موفقیت افزایش یافت'
        : 'تعداد محصول با موفقیت کاهش یافت',
      compact('cart')
    );
  }

  public function remove(Cart $cart)
  {
    $cart->delete();
    return response()->success('محصول با موفقیت از سبد حذف شد');
  }

  public function removeAllCarts() 
  {
    $this->customer->carts()->each(fn($cart) => $cart->delete());
  }

  public function getCartsCount()
  {
    $count = $this->customer->carts()->count();
    return response()->success('تعداد محصولات سبد خرید کاربر', compact('count'));
  }

  public function getShippableShippings(Request $request)
  {
    $request->validate([
      'varieties' => 'nullable|array',
      'varieties.*.variety_id' => 'nullable|exists:varieties,id',
      'varieties.*.quantity' => 'nullable|integer|min:1',
      'shipping_id' => ['nullable', 'exists:shippings,id'],
      'address_id' => ['nullable', 'exists:addresses,id'],
    ]);
    $carts = [];
    foreach ($request->varieties ?? [] as $requestCart) {
      $variety = Variety::query()->find($requestCart['variety_id']);
      $carts[] = Cart::fakeCartMaker(
        $variety->id, 
        $requestCart['quantity'], 
        $variety->final_price['discount_price'], 
        $variety->final_price['amount']
      );
    }
    $carts = collect($carts);
    $hasFreeShippingProduct = Cart::hasfreeShippingProduct($carts);
    $shippings = ShippingCalculatorService::getShippableShippingsForFront(
      customer: $this->customer,
      carts: $carts,
      chosenAddress: ($request->has('address_id')) ? $this->customer->addresses()->where('id', $request->address_id)->first() : null,
    );

    return response()->success('سبد خرید شما', compact(
      'carts',
      'hasFreeShippingProduct',
      'shippings'
    ));
  }
}
