<?php

namespace Modules\Home\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Advertise\Entities\Advertise;
use Modules\Blog\Entities\Post;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Order\Entities\OrderItem;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Recommendation;

class BladeHomeController extends Controller
{
	public function index()
	{
		$advertises = $this->getAdvertise();
		$specialCategories = $this->getSpecialCategories();
		$mostSaleProducts = $this->getMostSaleProducts();
		$newProducts = $this->getNewProducts();
		$posts = $this->getPosts();

		return view('home::index', compact(['advertises', 'specialCategories', 'mostSaleProducts', 'newProducts', 'posts']));
	}

	private function getAdvertise(): array
	{
		return Advertise::getForHome();
	}

	private function getSpecialCategories()
	{
		return Helpers::cacheForever('home_special_category', function () {
			return Category::query()
				->select(['id', 'title', 'slug', 'status', 'special'])
				->take(8)
				->special()
				->active()
				->latest()
				->with('media')
				->get();
		});
	}

	private function getMostSaleProducts()
	{
		$recommendation = $this->getByRecommendations(10, 'most_sales');

		if (count($recommendation) != 0) {
			return $recommendation;
		}

		Helpers::cacheRemember('home_most_sales', 300, function () {

			$mostSaleProducts = OrderItem::query()
				->latest(DB::raw('SUM(quantity)'))
				->groupBy('product_id')
				->selectRaw('DISTINCT product_id')
				->take(10)
				->get()
				->pluck('product_id')
				->toArray();

			if (count($mostSaleProducts) === 0) {
				return collect();
			}

			return Product::query()
				->whereIn('id', $mostSaleProducts)
				// ->orderByRaw('FIELD(id, ' . implode(", ", $mostSaleProducts) . ')')
				->available(true)
				->take(10)
				->get();
		});
	}

	private function getNewProducts($take = 10)
	{
		return Helpers::cacheRemember('new_products', 60, function () use ($take) {

			$recommendation = $this->getByRecommendations($take, 'new_products');

			if (count($recommendation) != 0) {
				return $recommendation;
			}

			return Product::query()
				->select(['id', 'status', 'title', 'unit_price', 'discount', 'discount_type'])
				->available(true)
				->take($take)
				->latest('id')
				->get();
		});
	}

	private function getPosts($take = 4)
	{
		return Helpers::cacheRemember('home_post', 120, function () use ($take) {
			return Post::query()
				->select(['id', 'post_category_id', 'title', 'status', 'published_at'])
				->with('category', fn($q) => $q->select(['id', 'name']))
				->published()
				->latest('id')
				->take($take)
				->get();
		});
	}

	private function getByRecommendations($take, $group)
	{
		$productIds = Recommendation::query()
			->byGroup($group)
			->take($take)
			->latest('order')
			->get(['product_id'])
			->pluck('product_id')
			->toArray();

		if (empty($productIds)) {
			return collect();
		}

		$sortStatus = [
			"'available'",
			"'soon'",
			"'out_of_stock'",
			"'draft'"
		];

		return Product::query()
			->select(['id', 'status', 'title', 'unit_price', 'discount', 'discount_type'])
			->whereIn('id', $productIds)
			->orderByRaw('FIELD(`status`, ' . implode(", ", $sortStatus) . ')')
			->available(true)
			->get();
	}
}
