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
use Modules\Product\Entities\RecommendationGroup;
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
		$sortTypes = RecommendationGroup::query()->where('show_in_filter', 1)->pluck('label', 'name')->toArray();

		return view('product::front.product.index', compact(['products', 'priceFilter', 'sizeValues', 'categories', 'sortTypes']));
	}

	public function show($id)
	{
		abort_if(!is_numeric($id), 500);

		$product = Product::query()
			->select([
				'id',
				'title',
				'unit_price',
				'discount',
				'discount_until',
				'discount_type',
				'slug',
				'image_alt',
				'status',
				'show_quantity',
				'approved_at',
				'description',
				'published_at'
			])
			->active()
			->with([
				'categories' => fn($cQuery) => $cQuery->select(['id', 'title', 'parent_id']),
				'varieties' => fn($vQuery) => $vQuery->select(['id', 'product_id', 'price', 'discount', 'discount_type', 'discount_until']),
				'varieties.store' => fn($sQuery) => $sQuery->select(['id', 'variety_id', 'balance']),
				'varieties.attributes' => fn($aQuery) => $aQuery->select(['id', 'name', 'label', 'style']),
				'specifications.pivot.specificationValues',
				'specifications.pivot.specificationValue',
				'productComments',
				'media'
			])
			->findOrFail($id)
			->append(['images', 'final_price', 'main_image']);

		foreach ($product->varieties as $variety) {
			$variety->makeHidden('product');
			$variety->append(['quantity', 'final_price']);
			foreach ($variety->attributes as $attribute) {
				$attribute->makeHidden('values');
			}
		}

		views($product)->collection('product')->record();
		$relatedProducts = Product::getRelatedProducts($product);

		return view('product::front.product.show', compact(['product', 'relatedProducts']));
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
