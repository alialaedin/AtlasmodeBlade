<?php

namespace Modules\Product\Http\Controllers\Customer;

use Illuminate\Http\JsonResponse;
use Modules\Customer\Entities\Customer;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;

class ProductController extends Controller
{
	public function indexFavorites(): JsonResponse
	{
		/** @var Customer $user */
		$user = auth()->user();
		$favorites = $user->favorites()
      ->select(['products.id', 'products.title', 'products.slug', 'products.image_alt'])
      ->with([
        'media',
        'varieties' => fn($q) => $q->select(['id', 'product_id', 'price', 'discount', 'discount_until', 'discount_type']),
      ])
      ->get()
      ->each(function ($p) {
        $p->append(['main_image', 'final_price']);
        $p->makeHidden('varieties');
      });

		return response()->success('لیست مورد علاقه های شما .', compact('favorites'));
	}

	public function addToFavorites($productId): JsonResponse
	{
		$user = auth()->user();
		$product = Product::query()->select(['id', 'title'])
			->findOrFail($productId)->makeHidden('images');
		/**
		 * @var Customer $user 
		 */

		$response = $user->favorites()->where('product_id', $product->id);
		if ($response->exists()) {
			return response()->success('قبلا به لیست مورد علاقه های شما افزوده شده است.');
		}

		$response->save($product);

		return response()->success('به لیست مورد علاقه ها افزوده شد', compact('product'));
	}

	public function deleteFromFavorites($productId)
	{
		/** @var Customer $user */
		$user = auth()->user();
		$product = Product::query()->select(['id', 'title'])
			->findOrFail($productId)->makeHidden('images');
		$user->favorites()->detach([$product->id]);

		return response()->success('از لیست مورد علاقه ها حذف شد');
	}
}
