<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Product\Exports\ProductExport;
use Modules\Product\Exports\ProductsExport;
use Shetabit\Shopit\Modules\Product\Http\Controllers\Admin\ProductController as BaseProductController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Attribute\Entities\Attribute;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Core\Classes\Tag;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Requests\Admin\ProductStoreRequest;
use Modules\Product\Http\Requests\Admin\ProductUpdateRequest;
use Modules\Specification\Entities\Specification;
use Modules\Unit\Entities\Unit;
use Throwable;
use Modules\Product\Entities\Variety;
use Modules\SizeChart\Entities\SizeChartType;

class ProductController extends Controller
{

    public function search(Request $request)
    {
        $q = \request('q');
        if (empty($q)) {
            return response()->error('ورودی نامعتبر است');
        }
        $products = Product::query()->select('id', 'title');

        if (is_numeric($q)) $products->orWhere('id', $q);
        $products->orWhere('title', 'LIKE', '%' . $q . '%');
        $products = $products->take(15)->get()->each(function ($product) {
            $product->setAppends(['main_image']);
        });

        return response()->success('', compact('products'));
    }

    public function excel(Request $request, $id)
    {
        $product = Product::withCommonRelations()->with('varieties.product')->findOrFail($id);
        switch ($request->type) {
            case 1:
                return Excel::download((new ProductExport($product)),
                    'product-' . $id . '.xlsx'
                );
        }
    }

    public function excels(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:products,id',
        ]);
        $products = Product::withCommonRelations()->with('varieties.product')->findOrFail($request->ids);

        $data = [
            //            ['barcode', 'name', 'price', 'count', 'var_name']
        ];

        foreach ($products as $product) {
            foreach ($product->varieties as $variety) {
                $varName = '';
                foreach ($variety->attributes as $attribute) {
                    if ($attribute->name === 'tarh') {
                        $varName .= $attribute->pivot->value;
                    }
                }
                foreach ($variety->attributes as $attribute) {
                    if ($attribute->name === 'size') {
                        $varName .= '-' . $attribute->pivot->value;
                    }
                }
                $varName .= ' ' . ($variety->color ? $variety->color->name : '');

                if ($variety->store->balance) {
                    $row = [
                        'barcode' => $variety->barcode,
                        'name' => $product->title,
                        'price' => number_format($variety->final_price['amount']) . 'تومان',
                        'count' => $variety->store->balance,
                        'var_name' => $varName,
                        'variety_id' => $variety->id
                    ];
                    $data[] = array_values($row);
                }
            }
        }

        if (\request()->header('accept') == 'x-xlsx') {
            return Excel::download((new ProductsExport($data)),
                'ProductsExport_' . now()->format('YmdHis') . '.xlsx'
            );
        }

        return response()->success('', compact('data'));
    }

    public function index()
    {
        $products = Product::query()
            ->select(['id', 'title', 'status', 'created_at', 'approved_at'])
            ->with([
                'categories' => fn ($q) => $q->select(['id', 'title']),
                'varieties' => fn ($q) => $q->select(['id', 'price', 'product_id']),
                'varieties.store' => fn ($q) => $q->select(['id', 'variety_id', 'balance'])
            ])
            // ->withCount('categories')
            ->filters()
            ->latest('id')
            ->paginate(request('per_page', 15))
            ->withQueryString();

        $statusCounts = Product::getStatusCounts();
        $categories = Category::query()->select(['id', 'title'])->latest('id')->get();
        $statuses = Product::getAvailableStatuses();
        $countAllProducts = Product::count();

        return view('product::admin.product.index', compact([
            'products', 
            'statusCounts',
            'categories',
            'statuses',
            'countAllProducts'
        ]));
    }

    /**
     * Store a newly created resource in storage.
     * @param ProductStoreRequest $request
     * @param Product $product
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(ProductStoreRequest $request, Product $product): JsonResponse
    {
        // dd($request->product["video"]);
        DB::beginTransaction();
        try {
            $product->fill($request->product);
            $product->checkStatusChanges($request->product);
            $product->unit()->associate($request->product['unit_id']);
            $product->brand()->associate($request->product['brand_id']);
            $product->save();
            if ($request->filled('product.images')) {
                $product->addImages($request->product['images']);
            }

            if ($request->filled('product.video_cover')) {
                $product->addVideoCover([$request->product['video_cover']]);
            }

            if ($request->filled('product.video')) {
                $product->addVideo([$request->product['video']]);
            }


            $product->attachTags($request->product['tags']);
            $product->assignSpecifications($request->product);
            $product->assignSizeChart($request->product);
            /**
             * Insert Product Variety
             * Varieties are created with the products
             * @see Product method storeVariety
             */
            $product->assignVariety($request->product);
            $product->assignGifts($request->product);

            if (!empty($request->product['categories'])) {
                $product->categories()->attach($request->product['categories']);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return response()->error('مشکلی در ثبت محصول به وجود آمده: ' . $e->getMessage(), $e->getTrace());
        }

        $product = $product->loadCommonRelations();
        /** بروزرسانی تاریخ  برای سایت مپ */
        $product->categories()->touch();

        return response()->success('محصول با موفقیت ایجاد شد', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();
            $oldStatus = $product->status;
            $product->fill($request->product);
            $product->checkStatusChanges($request->product);
            $product->unit()->associate($request->product['unit_id']);
            $product->brand()->associate($request->product['brand_id']);
            $product->save();
            $product->syncTags($request->product['tags']);
            $product->updateImages($request->product['images'] ?? []);


            if (!($request->product['video_cover'] ?? null) && $product->hasMedia('video_cover')) {
                $product->getFirstMedia('video_cover')->delete();
            }

            if (!($request->product['video'] ?? null) && $product->hasMedia('video')) {
                $product->getFirstMedia('video')->delete();
            }
            // new_video_cover
            if ($request->product['new_video_cover'] ?? null) {
                $product->updateVideoCover([$request->product['new_video_cover']]);
            }

            // new_video
            if ($request->product['new_video'] ?? null) {
                $product->updateVideo([$request->product['new_video']]);
            }

            // video deleted


            $product->assignSpecifications($request->product);
            $product->assignSizeChart($request->product);
            /**
             * Insert Product Variety
             * Varieties are created with the products
             * @see Product method storeVarietyf
             */
            $product->assignVariety($request->product, true);
            $product->assignGifts($request->product);

            if (!empty($request->product['categories'])) {
                $product->categories()->sync($request->product['categories']);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return response()->error(' مشکلی در بروزرسانی محصول به وجود آمده :   ' . $e->getMessage());
        }

        $product->loadCommonRelations();

        return response()->success('محصول با موفقیت بروزرسانی شد', compact('product'));
    }

    public function create()
    {
        $categories = Category::query()->select('id', 'title')->with(['attributes.values', 'specifications.values'])->get();
        $attributes = Attribute::query()->select('id', 'name','label','type')->with('values')->get();
        $brands = Brand::query()->select('id', 'name')->get();
        $tags = Tag::query()->select('id', 'name')->get();
        $units = Unit::query()->active()->select('id', 'name', 'status')->get();
        $specifications = Specification::active()->where('public', 1)->with('values')->latest('order')->get();
        $sizeChartTypes = SizeChartType::query()->select(['id', 'name'])->with('values:id,name,type_id')->latest()->get();
        $productsStatuses = Product::getAvailableStatusesWithLabel();
        $discountTypes = Product::getAvailableDiscountTypesWithLabel();
        
        return view(
            'product::admin.product.create', 
            compact([
                'categories', 
                'attributes', 
                'brands', 
                'tags', 
                'units', 
                'specifications', 
                'brands', 
                'sizeChartTypes',
                'productsStatuses',
                'discountTypes'
            ]
        ));

        // $categories = Category::query()->where('status', 1)
        //     ->with('attributes.values', 'brands', 'specifications.values')
        //     ->with(['children' => function ($query) {
        //         $query->with('attributes.values', 'brands', 'specifications.values', 'children.attributes.values')->where('status', 1);
        //     }])
        //     ->with(['specifications' => function ($query) {
        //         $query->with('values');
        //         $query->latest('order');
        //     }])->parents()->orderBy('priority')
        //     ->get();
        // $units = Unit::active()->get(['id', 'name']);
        // $tags = Tag::get(['id', 'name']);
        // $colors = Color::all();
        // $public_specifications = Specification::active()->where('public', 1)->with('values')->latest('order')->get();
        // $all_attributes = Attribute::with('values')->get();
        // if (app(CoreSettings::class)->get('size_chart.type')) {
        //     $size_chart_types = SizeChartType::query()->filters()->latest()->get();
        // } else {
        //     $size_chart_types = [];
        // }
        // $data = compact(
        //     'categories',
        //     'units',
        //     'tags',
        //     'colors',
        //     'public_specifications',
        //     'all_attributes',
        //     'size_chart_types'
        // );

        // $coreSettings = app(CoreSettings::class);
        // if ($coreSettings->get('product.gift.active')) {
        //     $data['gifts'] = Gift::all();
        // }

        // return response()->success('', $data);
    }

    public function loadVarieties()
    {
        $varieties = Variety::query()
            ->select(['id', 'product_id', 'discount', 'discount_until', 'price'])
            // ->with(['attributes', 'color', 'activeFlash'])
            ->where('product_id', request()->product_id)
            ->get();

        foreach ($varieties as $variety)
            $variety->setAppends(['title', 'quantity', 'final_price', '']);
        return response()->json(compact('varieties'));
    }
}
