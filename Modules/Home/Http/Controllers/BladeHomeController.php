<?php

namespace Modules\Home\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Advertise\Entities\Advertise;
use Modules\Blog\Entities\Post;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Recommendation;
use Modules\Product\Entities\RecommendationGroup;

class BladeHomeController extends Controller
{
	public function index()
	{
		$advertises = $this->getAdvertise();
		$specialCategories = $this->getSpecialCategories();
		$recommendationGroups = $this->getRecommendationGroups();
		$posts = $this->getPosts();

		return view('home::index', compact(['advertises', 'specialCategories', 'recommendationGroups', 'posts']));
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
				->latest('id')
				->with('media')
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

	private function getRecommendationGroups()
	{
		$recommendationGroups = RecommendationGroup::getForHomePage()->filter(fn ($group) => $group->items_count > 0);
		foreach ($recommendationGroups as $group) {
			$productIds = Recommendation::where('group_id', $group->id)->latest('order')->pluck('product_id')->toArray();
			$products = Product::query()
				->select(['id', 'status', 'title'])
				->whereIn('id', $productIds)
				->available(true)
				->take(8)
				->with([
					'media',
					'varieties' => function ($vQuery) {
						$vQuery->select(['id', 'product_id', 'discount', 'discount_until', 'discount_type', 'price']);
						$vQuery->with('store:id,variety_id,balance');
						$vQuery->with('product:id');
					}
				])
				->get()
				->each(function ($product) {
					$product->append(['main_image', 'final_price']);
					$product->makeHidden(['varieties', 'activeFlash']);
				});
			$group->products = $products;
		}
		return $recommendationGroups;
	}
}
