<?php

namespace Modules\Store\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Invoice\Entities\Payment;
use Modules\Product\Entities\Variety;
use Modules\Store\Entities\VarietyTransfer;
use Modules\Store\Entities\VarietyTransferItem;
use Modules\Store\Entities\VarietyTransferLocation;
use Modules\Store\Http\Requests\Admin\VarietyTransferStoreRequest;

class VarietyTransferController extends Controller
{
    public function index()
    {
        $request = \Request();
        $varietyTransfers = VarietyTransfer::query()
            ->when($request->filled('variety_ids') && is_array($request->variety_ids),function($q) use ($request){
                $q->whereHas('items', function ($query) use ($request) {
                    // this invoice has many payments that payment.status must be success
                    $query->whereIn('variety_id', $request->variety_ids);
                });
            })
            ->when($request->filled('start_date'),function(Builder $q) use ($request){
                $q->where('created_at', '>', $request->start_date);
            })
            ->when($request->filled('end_date'),function(Builder $q) use ($request){
                $q->where('created_at', '<', $request->end_date);
            })
            ->with([
                'from_location',
                'to_location',
                'items.variety' => function ($q) { $q->select(['id', 'product_id','barcode', 'color_id']); },
                'items.variety.product' => function ($q) { $q->select(['id', 'title']); },
                'items.variety.color',
                'creator' => function ($q) { $q->select(['id', 'name', 'username']); },
//                'creator.role' => function ($q) { $q->select(['id', 'name', 'label']); }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(50);


//        foreach ($varietyTransfers as $varietyTransfer) {
//            foreach ($varietyTransfer->items as $item) {
//                $variety = $item->variety;
//                $variety->makeHidden(['user_images', 'quantity', 'images', 'final_price']);
//                $product = $variety->product;
//                $product->makeHidden(['images', 'user_images', 'rate', 'views_count', 'active_flash']);
//                $color = $variety->color;
//                $color->makeHidden(['creator_id', 'updater_id', 'created_at', 'updated_at']);
//            }
//            $varietyTransfer->creator->role->makeHidden(['pivot', 'created_at', 'updated_at', 'name']);
//        }

        return response()->success('جا به جایی ها', compact('varietyTransfers'));
    }

    public function show($id)
    {
        $varietyTransfer = VarietyTransfer::findOrFail($id);
        $varietyTransfer->load(['items.variety.product', 'items.variety.attributes', 'items.variety.color', 'from_location', 'to_location']);
//        foreach ($varietyTransfer->items as $item) {
//            $variety = $item->variety;
//            $variety->makeHidden(['user_images', 'quantity', 'images', 'final_price']);
//            $product = $variety->product;
//            $product->makeHidden(['images', 'user_images', 'rate', 'views_count', 'active_flash']);
//            $color = $variety->color;
//            $color->makeHidden(['creator_id', 'updater_id', 'created_at', 'updated_at']);
//        }
        return response()->success('جزئیات حواله', compact('varietyTransfer'));
    }


    public function store(VarietyTransferStoreRequest $request)
    {
        $varietyTransfer = new VarietyTransfer($request->all());
        $varietyTransfer->creator_id = \Auth::guard('admin-api')->user()->id;
        $varietyTransfer->save();

        foreach ($request->varieties as $variety) {
            $varietyTransfer->items()->create([
                'variety_transfer_id' => $varietyTransfer->id,
                'variety_id' => $variety['id'],
                'quantity' => $variety['quantity']
            ]);
        }

        $varietyTransfer->load(['items.variety', 'from_location', 'to_location']);
        return response()->success('جا به جایی جدید با موفقیت ثبت شد', compact('varietyTransfer'));
    }

    public function destroy($id)
    {
        $varietyTransfer = VarietyTransfer::findOrFail($id);
        $varietyTransfer->is_delete = true;
        $varietyTransfer->save();

        return response()->success('جا به جایی با موفقیت حذف شد', compact('varietyTransfer'));
    }


    public function create()
    {
        $locations = VarietyTransferLocation::query()
            ->where('is_delete', '=', false)
            ->get();

        return response()->success('ایجاد جا به جایی', compact('locations'));
    }


    public function report()
    {
        $request = \Request();
        $request->variety_ids = explode(',', $request->variety_ids);

        if (!$request->start_date || !$request->end_date) {
            return response()->error('لطفا بازه زمانی را انتخاب کنید');
        }

        $varietyTransferItems = VarietyTransferItem::query()
            ->when($request->filled('variety_ids') && $request->variety_ids,function($q) use ($request){
                $q->whereIn('variety_id', $request->variety_ids);
            })
            ->whereHas('variety_transfer', function ($query) use ($request) {
                $query
                    ->where('is_delete', '=', false)
                    ->when($request->filled('receiver') && $request->receiver,function(Builder $q) use ($request){
                        $q->where('receiver', $request->receiver);
                    })
                    ->when($request->filled('mover') && $request->mover,function(Builder $q) use ($request){
                        $q->where('mover', $request->mover);
                    })
                    ->when($request->filled('from_location_id') && $request->from_location_id,function(Builder $q) use ($request){
                        $q->where('from_location_id', $request->from_location_id);
                    })
                    ->when($request->filled('to_location_id') && $request->to_location_id,function(Builder $q) use ($request){
                        $q->where('to_location_id', $request->to_location_id);
                    })
                    ->when($request->filled('start_date'),function(Builder $q) use ($request){
                        $q->where('created_at', '>', $request->start_date);
                    })
                    ->when($request->filled('end_date'),function(Builder $q) use ($request){
                        $q->where('created_at', '<', $request->end_date);
                    });
            })
            ->with(['variety_transfer', 'variety_transfer.from_location', 'variety_transfer.to_location'])
            ->get();



        $product_lists = [];
        foreach ($varietyTransferItems as $foreachItem) {
            $exists = array_filter($product_lists, function ($item) use ($foreachItem) {
                return $item['variety_id'] == $foreachItem->variety_id;
            });
            $indexes = array_keys($exists);
            if ($exists) {
                $product_lists[$indexes[0]]['quantity'] += $foreachItem->quantity;
//                    $product_lists[$indexes[0]]['discount_amount'] += $foreachItem->discount_amount * $foreachItem->quantity;
//                    $product_lists[$indexes[0]]['amount'] += $foreachItem->amount * $foreachItem->quantity;
//                    $product_lists[$indexes[0]]['diff_amount_from_real'] += $foreachItem->diff_amount_from_real * $foreachItem->quantity;
            } else {
                $variety = $foreachItem->variety;
                $variety->load('product');

                $varName = '';
                foreach ($variety->attributes as $attribute) {
                    if ($attribute->name === 'tarh') {
                        $varName .= $attribute->pivot->value;
                    }
                }
                foreach ($variety->attributes as $attribute) {
                    if (str_split($attribute->name, 4)[0] === 'size') {
                        $varName .= '-' . $attribute->pivot->value;
                    }
                }
                $varName .= ' '. ($variety->color ? $variety->color->name : '');

                $product_lists[] = [
                    'product_title' => $foreachItem->variety->product->title . '-' . $varName,
//                        'sell_price' => $variety->final_price['sell_price_discount'] ?? $variety->final_price['sell_price'],
                    'quantity' => $foreachItem->quantity,
//                        'discount_amount' => $foreachItem->discount_amount * $foreachItem->quantity,
//                        'amount' => $foreachItem->amount * $foreachItem->quantity,
//                        'diff_amount_from_real' => $foreachItem->diff_amount_from_real * $foreachItem->quantity,
                    'variety_id' => $foreachItem->variety_id
                ];
            }
        }



        return response()->success('گزارش جا به جایی ها', compact('product_lists', 'varietyTransferItems'));



    }



}
