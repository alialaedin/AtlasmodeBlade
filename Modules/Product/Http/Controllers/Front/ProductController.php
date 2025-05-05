<?php

namespace Modules\Product\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Category\Entities\Category;
use Modules\Color\Entities\ColorRange;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\RecommendationGroup;
use Modules\Product\Entities\Variety;
use Modules\Product\Services\NewProductService;

class ProductController extends Controller
{
	public function index(): View
	{
		$products = (new NewProductService())->getProducts();
		$sortTypes = RecommendationGroup::query()->where('show_in_filter', 1)->pluck('label', 'name')->toArray();
		$colorRanges = ColorRange::getColorRangesForFront();

		$allCategories = Category::getCategoriesToSetParent();
		$parentCategories = Category::getParentCategories();
		$childCategories = Category::getChildCategories();
		$grandChildCategories = Category::getGrandChildCategories();

		$requestCategory = [];
		if (request('category_id')) {
			$requestCategory = Category::getCategoriesToSetParent()->where('id', request('category_id'))->first();
			abort_if(!$requestCategory, 404);
		}

		$priceFilter = [
			'minPrice' => Variety::min('price'),
			'maxPrice' => Variety::max('price'),
		];

		return view('product::front.product.index', compact([
			'products', 
			'colorRanges', 
			'priceFilter', 
			'sortTypes', 
			'requestCategory', 
			'allCategories',
			'parentCategories',
			'childCategories',
			'grandChildCategories',
		]));
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
				'short_description',
				'published_at'
			])
			->active()
			->with([
				'categories' => fn($cQuery) => $cQuery->select(['id', 'title', 'parent_id']),
				'varieties' => fn($vQuery) => $vQuery->select(['id', 'product_id', 'price', 'discount', 'discount_type', 'discount_until']),
				'varieties.media',
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
			$variety->append(['quantity', 'final_price', 'images']);
			$variety->makeHidden('product');
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
			->orWhereHas('categories', fn($q) => $q->where('id', $serach)->orWhere('parent_id', $serach))
			->with([
				'media',
				'varieties' => function ($vQuery) {
					$vQuery->select(['id', 'product_id', 'discount', 'discount_until', 'discount_type', 'purchase_price', 'price']);
					$vQuery->with('store', fn ($sQuery) => $sQuery->select(['id', 'variety_id', 'balance']));
				},
			])
			->latest('id')
			->active()
			->take(8)
			->get()
			->each(function ($product) {
				$product->setAppends(['slug', 'main_image', 'final_price']);
				$product->makeHidden(['varieties', 'activeFlash', 'active_flash']);
			});

		return response()->success('', compact('products'));
	}

}
