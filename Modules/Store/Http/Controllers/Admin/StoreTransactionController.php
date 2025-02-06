<?php

namespace Modules\Store\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;
use Modules\Store\Entities\StoreTransaction;
use Modules\Store\Exports\StoreTransactionExport;
use Shetabit\Shopit\Modules\Store\Http\Controllers\Admin\StoreTransactionController as BaseStoreTransactionController;

class StoreTransactionController extends BaseStoreTransactionController
{
    public function pending_list(Request $request): JsonResponse
    {
        if($request->has('is_done')){
            $request->merge([
                'is_done' => ($request->is_done === 'true') ? 1 : 0
            ]);
        }

           $varieties = Variety::query()
           ->withCommonRelations();
           if($request->input('is_done')){
                $varieties->whereHas('doneStoreTransactions')->with(['doneStoreTransactions']);
           }
           else {
                $varieties->whereHas('pendingStoreTransactions')->with(['pendingStoreTransactions']);
           }

           $varieties = $varieties->paginateOrAll(50);
        // $storeTransactions = $storeTransactions->latest()->filters()->groupBy('store_id')->select('*')->addSelect(DB::raw('sum(quantity) as variety_count'))->paginateOrAll(50);


        return response()->success('لیست تراکنش های انبار', compact('varieties'));
    }

    public function markAsDone(Request $request, StoreTransaction $transaction)
    {
        $transaction->update([
            'is_done' => true
        ]);
        $transaction->refresh();

        return response()->success('status changed successfully!', [
            'data' => $transaction
        ]);
    }

    public function markAsDoneBatch(Request $request)
    {
        StoreTransaction::whereIn('id', $request->input('transactions'))
            ->update([
                'is_done' => true
            ]);

        return response()->success('statuses changed successfully!', [
            'data' => StoreTransaction::whereIn('id', $request->input('transactions'))->get()
        ]);
    }

    public function storeExcel()
    {

        $storeTransactions = StoreTransaction::query()->select('id','description','quantity','type','created_at','creatorable_type','creatorable_id','store_id')
            ->with(['store' => function($query) {
            $query->select('id','variety_id')->with(['variety' => function($query) {
                $query->select('id','product_id')->with(['product' => function($query) {
                    $query->select('id','title');
                }]) ;
                $query->without('media');
            }]);
        }]);
        $storeTransactions = $storeTransactions->latest()->filters()->get();
        if (\request()->header('accept') == 'x-xlsx') {
            foreach ($storeTransactions as $transaction){
                $finalModels[] = [
                    $transaction->id,
                    $transaction->store->variety->product->title??null,
                    $transaction->creatorable->name ?? null,
                    $transaction->description,
                    $transaction->quantity,
                    $transaction->type = 'increment'?'افزایش':'کاهش',
                    verta($transaction->created_at)->format('Y-m-d H:i'),
                ];
            }
            return Excel::download(new StoreTransactionExport($finalModels),
                __FUNCTION__.'-' . now()->toDateString() . '.xlsx');

        }

    }
}
