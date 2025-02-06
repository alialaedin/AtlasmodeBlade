<?php

namespace Modules\Cart\Http\Controllers\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Http\Requests\Admin\CartStoreRequest;
use Modules\Cart\Http\Requests\Admin\CartUpdateRequest;
use Modules\Cart\Services\WarningMessageCartService;
use Modules\Core\Entities\User;
use Modules\Customer\Entities\Customer;
use Modules\Order\Entities\Order;
use Modules\Product\Entities\Variety;

class CartController extends Controller
{
  public function index(): JsonResponse
  {
    $messages = [];
    $user = \Auth::user();
    /**
     * @var $user Customer
     */
    $carts = $user->carts()->withCommonRelations()->get();

    $showWarning = request('show_warning', 'false');
    if ($showWarning == 1) {
      $messages = (new WarningMessageCartService($carts))->checkAll();
    }
    // گرفتن رزور های فعال
    $reservations = Order::isActiveReserved()->where('customer_id', $user->id)->get()->map(function ($order) {
      $order->setAttribute('total_total_quantity', $order->getTotalTotalQuantity());
      return $order;
    });

    return response()->success('سبد خرید شما', compact('carts', 'messages', 'reservations'));
  }

  /**
   * @param CartStoreRequest $request
   * @param Cart $cart
   * @return jsonResponse
   */
  public function add(CartStoreRequest $request, $varietyId): JsonResponse
  {
    $varietyInCart = \Auth::user()->carts()->where('variety_id', $request->variety->id)->first();
    if ($varietyInCart) {
      $varietyInCart->quantity += $request->input('quantity');
      $varietyInCart->save();
      return response()->success('تعداد محصول با موفقیت افزایش یافت', [
        'cart' => $varietyInCart
      ]);
    }
    $cart = Cart::addToCart($request->input('quantity'), $request->variety, \Auth::user());


    return response()->success('محصول موفقیت به سبد خرید اضافه شد', compact('cart'));
  }


  public function update(CartUpdateRequest $request, $id)
  {
    //cart set in request
    $isIncrement = $request->cart->quantity < $request->quantity;
    $request->cart->quantity = $request->quantity;
    $request->cart->save();
    $cart =  $request->cart->loadCommonRelations();

    return response()->success(
      $isIncrement
        ? 'محصول موفقیت به سبد خرید اضافه شد'
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
