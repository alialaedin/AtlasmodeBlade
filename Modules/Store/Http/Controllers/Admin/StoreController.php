<?php

namespace Modules\Store\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;
use Modules\Store\Entities\Store;
use Modules\Store\Entities\StoreTransaction;
use Modules\Store\Exports\StoreBalanceReport;
use Modules\Store\Http\Requests\Admin\StoreRequest;

class StoreController extends Controller
{

    public function index()
    {
        $stores = Store::query()->with('variety.attributes')->latest('id')->filters()->paginate();

        return view('store::admin.index', compact('stores'));
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $store = Store::insertModel($request);
            $store->transactions()->latest()->first();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            return redirect()->back()->with('error', 'مشکلی در برنامه رخ داده است:' . $exception->getMessage());
        }

        return redirect()->back()->with('success', 'موجودی محصول با موفقیت بروزرسانی شد');
    }

    public function transactions()
    {
        $storeTransactions = StoreTransaction::query()
            ->orderByDesc('id')
            ->filters()
            ->paginate(50);

        return view('store::admin.transactions', compact(['storeTransactions']));
    }

    public function storeWealthReport()
    {
        $sumAmounts = 0;
        $stores = Store::query()->where('balance', '!=', '0')->get();
        $sumStoreBalance = $stores->sum('balance');

        foreach ($stores as $store) {
            if (isset($store->variety)) {
                $sumAmounts += $store->balance * $store->variety->price;
            }
        }

        return response()->success('success', compact('sumStoreBalance', 'sumAmounts'));
    }

    public function storeBalanceReport()
    {
        $sortStatus = [
            "'available'",
            "'soon'",
            "'out_of_stock'",
            "'draft'"
        ];

        $requestProductStatus = \request('status') ?? Product::STATUS_AVAILABLE;


        $products = Product::query()
            ->select(['id', 'title', 'status', 'unit_price'])
            ->when(\request('product_id'), function ($query) {
                $query->where('id', \request('product_id'));
            })
            ->withoutGlobalScopes()
            ->where('status', $requestProductStatus)
            //            ->orderByRaw('FIELD(`status`, '.implode(", " , $sortStatus).')')
            ->get();


        $varieties = Variety::query()
            ->select(['id', 'product_id', 'color_id', 'barcode', 'SKU', 'deleted_at'])
            ->whereIn('product_id', $products->pluck('id')->toArray())
            ->whereNull('deleted_at')
            ->with('attributes')
            ->get();


        $stores = Store::query()
            ->select(['id', 'balance', 'variety_id'])
            ->whereIn('variety_id', $varieties->pluck('id')->toArray())
            ->get();

        foreach ($products as $product) {
            $productVarieties = $varieties->where('product_id', $product->id)->values();
            $appendedVarieties = [];
            $productTotalBalance = 0;
            foreach ($productVarieties as $productVariety) {
                $varietyBalance = $stores->where('variety_id', $productVariety->id)->first()->balance;
                if ($varietyBalance == 0) continue;
                $productTotalBalance += $varietyBalance;
                //                $title = $product->title . '|';
                $title = $productVariety->color->name ?? '';
                //                $title .= $productVariety->name ?? '';
                foreach ($productVariety->attributes ?? [] as $attribute) {
                    $title .= ' | ' . $attribute->label . ': ' . $attribute->pivot->value;
                }

                $appendedVarieties[] = [
                    'id' => $productVariety->id,
                    'title' => $title,
                    'barcode' => $productVariety->barcode,
                    'SKU' => $productVariety->SKU,
                    'balance' => $varietyBalance,
                ];
            }
            $product->total_balance += $productTotalBalance;
            $product->varieties = $appendedVarieties;
        }

        $products = $products->sortByDesc(function ($product) {
            return $product->total_balance;
        })->values();

        if (\request()->header('accept') == 'x-xlsx') {
            return Excel::download(
                new StoreBalanceReport($products),
                __FUNCTION__ . '-' . now()->toDateString() . '.xlsx'
            );
        }

        return response()->success('گزارش موجودی محصولات', compact('products'));
    }
}
