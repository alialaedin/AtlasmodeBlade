<?php

namespace Modules\Product\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\Entities\Attribute;
use Modules\Category\Entities\Category;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Product;
use Modules\Product\Services\NewProductService;
use Modules\Product\Services\ProductService;

class ProductController extends Controller
{
	public function index(): View
	{
		// $productService = new ProductService();
		// $products = $productService->filters();
		// $priceFilter = $productService->maxAndMinPrice();
		// $perPage = request('per_page', app(CoreSettings::class)->get('product.pagination', 12));
		// $products = $products->paginate($perPage)->withQueryString();
		// foreach ($products as $product) {
		//     $product->makeHidden('varieties');
		//     $product->makeHidden('activeFlash');
		//     $product->makeHidden('varietyOnlyDiscountsRelationship');
		// }
		// dd('Products index');

		// dd(in_array(1, json_decode(request()->attribute_value_id)));
		// dd(json_decode(request()->attribute_value_id));
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

		$q = request('q');
		if (!$q || mb_strlen($q) === 1) {
			return '';
		}
		$coreSetting = app(CoreSettings::class);

		//        $numberPattern = $coreSetting->get('search.products.number_pattern');
		//        if (is_numeric($q) && $numberPattern) {
		//            $q = str_replace('{number}', $q, $numberPattern);
		//        }

		$s =  '%' . $q . '%';
		$products = ((new ProductService())->filters())->with('varieties')->where(function ($query) use ($s) {
			$query->where('title', 'LIKE', $s)->orWhere('short_description', 'LIKE', $s);
		})->orWhereHas('varieties', function ($query) use ($s) {
			$query->where('name', 'LIKE', $s);
		})->latest()->active();

		if ($c = request('c')) {
			$products->whereHas('category', function ($query) use ($c) {
				$query->where('id', $c);
			});
		}
		$products->orWhere('title', 'LIKE', '%' . $q . '%');

		$products = $products->take(8)->get(['id', 'title', 'short_description', 'status', 'slug'])->map(function ($p) {
			$p->makeHidden('varieties');
			return $p;
		});
		/**
		 * @var Product $product 
		 */
		foreach ($products as $product) {
			$product->setAppends(['slug', 'price', 'major_image', 'images', 'major_final_price', 'rate']);
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
