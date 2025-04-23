<?php

namespace Modules\Home\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Advertise\Entities\Advertise;
use Modules\Blog\Entities\Post;
use Modules\Category\Entities\Category;
use Modules\Home\Entities\SiteView;
use Modules\Product\Entities\RecommendationGroup;
use Modules\Slider\Entities\Slider;

class BladeHomeController extends Controller
{
	public function index()
	{
		SiteView::store();
		$advertisements = Advertise::getAdvertisementsForFront();
		$sliders = Slider::getAllSlidersForFront();
		$specialCategories = Category::getSpecialCategories();
		$posts = Post::getPostsForFront();
		$recommendationGroups = RecommendationGroup::getGroupsWithItemsForFront();

		return view('home::index', compact(['advertisements', 'specialCategories', 'recommendationGroups', 'posts', 'sliders']));
	}
}
