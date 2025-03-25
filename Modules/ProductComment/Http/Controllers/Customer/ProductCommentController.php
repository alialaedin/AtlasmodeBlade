<?php

namespace Modules\ProductComment\Http\Controllers\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Customer\Entities\Customer;
use Modules\ProductComment\Entities\ProductComment;
use Modules\ProductComment\Http\Requests\Customer\ProductCommentStoreRequest;

class ProductCommentController extends Controller
{
	protected ?Customer $user;

	public function __construct()
	{
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
	}

	public function index(): JsonResponse
	{
		$comments = $this->user->productComments()->with('product', 'childs')->filters()->paginateOrAll();
		return response()->success('لیست دیدگاه ها', compact('comments'));
	}

	public function store(ProductCommentStoreRequest $request)
	{
		$productComment = new ProductComment();
		$productComment->fill($request->except('status'));
		$productComment->creator()->associate(auth()->user());
		$productComment->product()->associate($request->product_id);
		$productComment->save();

		return response()->success('دیدگاه با موفقیت ثبت شد.', compact('productComment'));
	}

	public function show($id): JsonResponse
	{
		$comment = $this->user->productComments()->withCommonRelations()->findOrFail($id);

		return response()->success('', compact('comment'));
	}

	public function destroy($id): JsonResponse
	{
		$comment = $this->user->productComments()->findOrFail($id);
		$comment->delete();

		return response()->success('دیدگاه با موفقیت حذف شد', compact('comment'));
	}
}
