<?php

namespace Modules\Order\Http\Controllers\Customer;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Core\Helpers\Helpers;
use Modules\Customer\Entities\Customer;
use Modules\Order\Http\Requests\User\OrderStoreRequest;


class OrderController extends Controller
{
  public function index(): JsonResponse
  {
    /**
     * @var $user Customer
     */
    $status = request('status', false);

    $orders = Order::query()->when($status && $status != 'all', function ($query) use ($status) {
      $query->where('status', $status);
    })->myOrders()
      ->with(['statusLogs', 'invoices.payments', 'gift_package'])
      ->parents()
      ->filters()
      ->latest('id')
      ->paginateOrAll();
    $statistics = Order::getOrderStatisticsForCustomer(Auth::user());

    return response()->success('Get all orders list', compact('orders', 'statistics'));
  }

  public function store(OrderStoreRequest $request)
  {
    try {
      /** @var Customer $customer */
      $customer = auth()->user();
      /** @var Order $order */
      $order = Order::store($customer, $request);
      //            NewOrderForAdminNotificationJob::dispatch($order);
      //نوتیفیکیشن برای مشتری در قسمت on success
      // اگر کاربر بخواهد با کیف پرداخت کند
      if ($request->input('pay_wallet')) {
        return $order->payWithWallet($customer);
      } else {
        return $order->pay();
      }
    } catch (Exception $exception) {
      Log::error($exception->getTraceAsString());
      return response()->error('مشکلی در برنامه رخ داده است:' . $exception->getMessage(), $exception->getTrace(), 500);
    }
  }

  public function exitTheReservationMode(Order $order)
  {
    /** @var Order $order */
    if ($order->status != Order::STATUS_RESERVED) {
      throw Helpers::makeValidationException('سفارش در حالت رزو نمی باشد');
    }
    $order->update(['status' => Order::STATUS_NEW]);

    return response()->success('سفارش از حالت رزو خارج شد', compact('order'));
  }

  public function show($id): JsonResponse
  {
    $order = Order::query()->withCommonRelations()->with('reservations')->findOrFail($id);
    //Authorization
    if ($order->customer_id !== auth()->user()->id) {
      return response()->error('درخواست غیرمجاز است.', [], 403);
    }

    return response()->success('Get order detail', compact('order'));
  }
}
