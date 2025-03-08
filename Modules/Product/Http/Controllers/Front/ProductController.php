<?php

namespace Modules\Product\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\Entities\Attribute;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Product;
use Modules\Product\Services\NewProductService;
use Modules\Product\Services\ProductService;

class ProductController extends Controller
{
	public function index(): View
	{
		$priceFilter = (new ProductService())->maxAndMinPrice();
		$products = (new NewProductService())->getProducts();
		$sizeValues = Attribute::getSizeValues();
		$categories = Category::query()->orderBy('priority')->with('children')->parents()->active()->get();

		return view('product::front.product.index', compact(['products', 'priceFilter', 'sizeValues', 'categories']));
	}

	public function show($id): JsonResponse
	{
		$startTime = microtime(true);

		if (!is_numeric($id)) {
			abort(500);
		}
		/**
		 * @var Product $product 
		 */
		$product = Product::query()->active()->with(['imagesNew', 'varieties.store'])->withCommonRelations()->findOrFail($id);



		views($product)->collection('product')->record();

		$relatedProducts = Product::query()->Available()->active()->with([/*'categories', 'activeFlash',*/'varieties'])
			->whereHas('categories', function ($query) use ($product) {
				return $query->whereIn(
					'id',
					$product->categories->whereNotNull('parent_id')->pluck('id')->toArray() ?? $product->categories->pluck('id')->toArray()
				);
			})
			->whereKeyNot($product->id)
			->inRandomOrder()
			->select(
				'id',
				'title',
				'slug',
				'status',
				//                'rate',
				//                'major_final_price',
				//                'major_image',
				//                'images',
			)
			->take(6)
			->get();

		$relatedProducts = Helpers::removeVarieties($relatedProducts->toArray());

		// $relatedProducts = collect();
		$settedProducts = $product->setted_products;
		//        return response()->json($relatedProducts);


		$endTime = microtime(true);
		$elapsedTime = $endTime - $startTime;
		Log::info('Show Product Normal: ', ['elapsed_time' => $elapsedTime]);

		return response()->success('', compact('product', 'relatedProducts', 'settedProducts'));
	}

	public function search()
	{

		$searchKey = request('q');
		$serach = '%' . $searchKey . '%';

		$products = Product::query()
			->select(['id', 'title', 'short_description', 'status', 'slug', 'approved_at'])
			->where('title', 'LIKE', $serach)
			->orWhere('short_description', 'LIKE', $serach)
			->orWhereHas('categories', fn($q) => $q->where('id', $serach)->oreWhere('parent_id', $serach))
			->with([
				'varieties' => function ($varietyQuery) {
					$varietyQuery->select(['id', 'product_id', 'discount', 'discount_until', 'discount_type', 'purchase_price', 'price']);
				},
				'varieties.store' => function ($storeQuery) {
					$storeQuery->select(['id', 'variety_id', 'balance']);
				}, 
			])
			->latest('id')
			->active()
			->take(8)
			->get();

		foreach ($products as $product) {
			$product->setAppends(['slug', 'main_image', 'final_price', 'rate']);
		}

		$products->makeHidden('varieties');
		$products->makeHidden('activeFlash');
		$products->makeHidden('active_flash');

		return response()->success('', compact('products'));
	}

	public function show_light($id): JsonResponse
	{
		$startTime = microtime(true);

		if (!is_numeric($id)) {
			abort(500);
		}
		/**
		 * @var Product $product 
		 */

		$product = DB::table('products')
			->whereIn('status', ['available', 'out_of_stock'])
			->whereNotNull('approved_at')
			->where('published_at', '<=', now())
			->where('id', $id)
			->select(
				"id",
				"title",
				//            "slug",
				//            "short_description",
				"description",
				"unit_price",
				//            "purchase_price",
				"discount_type",
				"discount",
				//            "SKU",
				//            "barcode",
				"chargeable",
				//            "brand_id",
				//            "unit_id",
				"meta_description",
				"meta_title",
				"low_stock_quantity_warning",
				"show_quantity",
				"status",
				//            "approved_at",
				//            "published_at",
				//            "created_at",
				//            "updated_at",
				"discount_until",
				"unit_price as price",
			)
			->first();


		$product->images = Helpers::getImages('Product', $id);
		$product->rate = Helpers::getProductRate($id);
		//        $product->views_count = Helpers::getProductViewsCount($id);
		$product->major_final_price = Helpers::getMajorFinalPrice($id);
		$product->major_discount_amount = $product->major_final_price->discount;
		$product->major_discount_type = $product->major_final_price->discount_type;
		$product->varieties = Helpers::getProductVarieties($id);


		$relatedProducts = Helpers::getRelatedProducts($product);


		$endTime = microtime(true);
		$elapsedTime = $endTime - $startTime;
		Log::info('Show Product Light: ', ['elapsed_time' => $elapsedTime]);

		$prd = Product::query()->findOrFail($id);
		$settedProducts = $prd->setted_products;

		$product->video_cover = $prd->getVideoCoverAttribute();
		$product->video = $prd->getVideoAttribute();

		return response()->success('', compact('product', 'relatedProducts', 'settedProducts'));



		//        return response()->success('', compact('product', 'relatedProducts','settedProducts'));
	}
}
