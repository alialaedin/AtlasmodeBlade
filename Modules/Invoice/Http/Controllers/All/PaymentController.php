<?php


namespace Modules\Invoice\Http\Controllers\All;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Invoice\Entities\Payment;

class PaymentController extends BaseController
{
  public function verify($gatewayName)
  {
    $driver = config('invoice.drivers')[$gatewayName];
    if (!Payment::isDriverEnabled($gatewayName)) {
      return false;
    }
    $payDriver = new $driver['model']($driver['options'], $gatewayName);
    $transactionId = $payDriver->getTransactionId();
    if (!$transactionId) {
      return response()->error('Transaction Id not found');
    }
    /** @var Payment $payment */
    $payment = Payment::where('gateway', $payDriver->getname())->where('transaction_id', $transactionId)->first();

    if (!$payment) {
      return response()->error('Wrong transaction id');
    }

    return $payment->verify();
  }
}
