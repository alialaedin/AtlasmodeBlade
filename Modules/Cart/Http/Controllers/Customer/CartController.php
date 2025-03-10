<?php

namespace Modules\Cart\Http\Controllers\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Area\Entities\Province;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Http\Requests\Admin\CartStoreRequest;
use Modules\Cart\Http\Requests\Admin\CartUpdateRequest;
use Modules\Cart\Services\WarningMessageCartService;
use Modules\Invoice\Entities\Payment;
use Modules\Shipping\Entities\Shipping;

class CartController extends Controller
{
  public function index()
  {
    $customer = auth('customer')->user();
    $customer->load(['addresses' => fn($q) => $q->with('city')]);

    $carts = $customer->carts;
    $cartsWarnings = (new WarningMessageCartService($carts))->checkAll();
    $hasFreeShippingProduct = Cart::hasfreeShippingProduct($carts);

    $shippings = Shipping::getActiveShippings();
    $gateways = Payment::getAvailableDriversForFront();
    $provinces = Province::getAllProvinces(true);

    return view('cart::front.index', compact([
      'carts',
      'hasFreeShippingProduct',
      'cartsWarnings',
      'customer',
      'shippings',
      'gateways',
      'provinces',
    ]));
  }

  public function add(CartStoreRequest $request, $varietyId): JsonResponse
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
        ? 'محصول با موفقیت به سبد خرید اضافه شد'
        : 'محصول با موفقیت از سبد خرید کم شد',
      compact('cart')
    );
  }

  public function remove(Cart $cart)
  {
    $cart->delete();
    return response()->success('محصول با موفقیت از سبد حذف شد');
  }
}
