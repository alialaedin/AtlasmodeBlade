<?php

namespace Modules\Shipping\Services;

//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection;
use Modules\Cart\Entities\Cart;
use Modules\Core\Helpers\Helpers;
use Modules\Customer\Entities\Address;
use Modules\Customer\Entities\Customer;
use Modules\CustomersClub\Entities\CustomersClubLevel;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;
use Modules\Product\Services\ProductsCollectionService;
use Modules\Setting\Entities\Setting;
use Modules\Shipping\Entities\Shipping;

/* Service Description.
 | hi. this is a service for calculating shipping. we use it all over the project, and we don't calculate it other places
 | for use of this class you must use of carts. if you want to update an order (for example you want to add an item to the order) you must to create a fakeCart from order_items
 |
 | how it works?
 | at first you should create an object of this class. pass its inputs in __construct (i.e. $address, $shipping, $customer they are required) and then add your carts with addItem method and
 | and addItemsByCarts method to add a carts collection.
 | and finally you should call calculate method.
 | attention: response of calculate method is an array. because there exists 4 fields for shipping. we calculate all of them.
 |
 | this is example of code to use of this class:

        $shippingCalculatorService = new ShippingCalculatorService($address,$shipping, false);
        foreach ($carts as $cart) {
            $shippingCalculatorService->addItem($cart->variety, $cart->quantity);
        }
        $shippingCalculatorServiceResponse = $shippingCalculatorService->calculate();


 | and this is example of shippingCalculatorServiceResponse:

[
  "shipping_amount" => 39000
  "shipping_packet_amount" => 0
  "shipping_more_packet_price" => 0
  "shipping_first_packet_size" => 0
]

 | attention. you might want to sell at a different price. for example, you want to sell with co-workers price .
 | This topic is none of this class's business. i.e. this topic that you want to sell products with what price is not related to the ShippingCalculatorService
 | and you should set the price where you want to use of this class.
 * */
class ShippingCalculatorService
{
//    public array $items;

    public function __construct(
        public Address $address,
        public Shipping $shipping,
        public Customer $customer,
        public Collection $items,
    ) {
        if (! $shipping->checkShippableAddress($address->city)) {
            throw Helpers::makeValidationException('شیوه ارسال انتخاب شده نامعتبر است.', 'shipping_id');
        }
    }


    public function addItem(Cart $cart) {
        $this->items[] = $cart;
    }

    public function addItemsByCarts($carts){
        foreach($carts as $cart){
            $this->addItem($cart);
        }
    }


    public function calculate()
    {
        // maybe chosen shipping is free_shipping itself
        if ($this->shipping->free_shipping) return $this->responseCreator(0);

        // free_shipping_products. if product.free_shipping column be true, and if there was a variety of this product in carts (or order) shipping is free.
        $varietyIds = $this->items->pluck('variety_id')->toArray();
        $varieties = Variety::query()
            ->select(['id', 'product_id'])
            ->whereIn('id', $varietyIds)
            ->with('product:id,free_shipping')
            ->get();

        foreach ($varieties as $variety) {
            if ($variety->product->free_shipping) return $this->responseCreator(0);
        }

        $sumOrderItemsWithoutShipping = $this->sumOrderItemsCalculator();

        // we have to attention to the threshold
        if ($this->shipping->free_threshold < $sumOrderItemsWithoutShipping) return $this->responseCreator(0);

        // shipping amount for gorgan shipping
        if($this->address->city_id == 264 && $sumOrderItemsWithoutShipping >= Setting::getFromName('free_shipping_amount_for_gorgan'))
            return $this->responseCreator(0);

        // if sum carts be more than a value, shipping is free.
        if ($sumOrderItemsWithoutShipping >= Setting::getFromName('free_shipping_amount_for_other_cities'))
            return $this->responseCreator(0);

        $final_shipping_amount = $this->shipping->default_price;

        return $this->responseCreator($final_shipping_amount);
    }

    private function sumOrderItemsCalculator() : int
    {
        if (\request()->has('sumOrderItems')) return request()->sumOrderItems;

        $total = 0;
        foreach ($this->items as $item) {
            $total += ($item->quantity * $item->price);
        }
        return $total;
    }

    private function responseCreator($shipping_amount, $shipping_packet_amount = 0, $shipping_more_packet_price = 0, $shipping_first_packet_size = 0)
    {
        return [
            'shipping_amount' => $shipping_amount,
            'shipping_packet_amount' => $shipping_packet_amount,
            'shipping_more_packet_price' => $shipping_more_packet_price,
            'shipping_first_packet_size' => $shipping_first_packet_size,
        ];
    }

    public static function getCartsWeight($carts):array
    {
        $weight = 0;

        $over =0;
        $overweight_shipping_amount = Setting::getFromName('overweight_shipping_amount') ? : 999;
        foreach ($carts as $item) {
            $variety = $item->variety;
            $iweight = Setting::getFromName('defualt_product_weight') ? Setting::getFromName('defualt_product_weight') : 120;
            if($variety?->weight){
                $iweight = $variety->weight;
            }elseif($variety->product?->weight){
                $iweight = $variety->product->weight;
            }
            $weight = $weight + ($item->quantity * $iweight);


            \request()->merge(['orderWeight' => $weight]);


            // calculate over weight
            if($variety?->weight > $overweight_shipping_amount){
                $over++;
            }elseif($variety->product?->weight > $overweight_shipping_amount){
                $over++;
            }
        }

        return [$over, $weight];
    }

    private function getItemsWeight():array
    {
        return static::getCartsWeight($this->items);
    }



    public static function getShippableShippingsForFront(Customer $customer, $carts, Address|null $chosenAddress = null,)
    {
        if ($chosenAddress) {
            $shippings = Shipping::getShippableShippingsForAddress($chosenAddress);
            foreach ($shippings as $shipping) {
                $shipping->amount_showcase = (new ShippingCalculatorService($chosenAddress, $shipping, $customer, $carts))->calculate()['shipping_amount'];
            }
        } else {
            $shippings = Shipping::query()->active()->get();
            foreach ($shippings as $shipping)
                $shipping->amount_showcase = $shipping->default_price;
        }
        return $shippings;

    }


}
