<?php

namespace Modules\Customer\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Customer\Entities\Withdraw;
use Modules\Customer\Http\Requests\Admin\WithdrawStoreRequest;

class   WithdrawController extends Controller
{
  public function index()
  {
    $withdraws = Withdraw::query()->filters()->latest('id')->paginate();
    $withdrawsCount = $withdraws->total();

    return view('customer::admin.wallet.withdraws', compact('withdraws', 'withdrawsCount'));
  }

  public function update(Withdraw $withdraw, WithdrawStoreRequest $request)
  {
    $withdraw->fill($request->all());
    $withdraw->status = $request->status;
    $withdraw->save();

    if ($request->status == 'paid') {
      $gift_balance = DB::table('wallets')
        ->where('holder_type', 'Modules\Customer\Entities\Customer')
        ->where('holder_id', $withdraw->customer->id)
        ->latest('id')
        ->first()
        ->gift_balance;

      if ($gift_balance != 0) {
        if ($gift_balance > $withdraw->amount) {
          DB::table('wallets')
            ->where('holder_type', 'Modules\Customer\Entities\Customer')
            ->where('holder_id', $withdraw->customer->id)
            ->update([
              'gift_balance' => $gift_balance - $withdraw->amount,
            ]);
        } elseif ($gift_balance <= $withdraw->amount) {
          DB::table('wallets')
            ->where('holder_type', 'Modules\Customer\Entities\Customer')
            ->where('holder_id', $withdraw->customer->id)
            ->update([
              'gift_balance' => 0,
            ]);
        }
      }
    }

    ActivityLogHelper::deletedModel(' برداشت ویرایش شد', $withdraw);

    return redirect()->back()->with('success', 'برداشت با موفقیت ویرایش شد');
  }
}
