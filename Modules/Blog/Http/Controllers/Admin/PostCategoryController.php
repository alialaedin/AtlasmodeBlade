<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Blog\Entities\PostCategory;
use Modules\Blog\Http\Requests\Admin\PostCategory\PostCategoryStoreRequest;
use Modules\Blog\Http\Requests\Admin\PostCategory\PostCategoryUpdateRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PostCategoryController extends Controller
{
  public function index(): View
  {
    $postCategories = PostCategory::query()
      ->filters()
      ->select(['id', 'name', 'status', 'order', 'created_at'])
      ->latest('id')
      ->withCount('posts')
      ->paginate()
      ->withQueryString();

    return view('blog::admin.post-category.index', compact('postCategories'));
  }

  public function store(PostCategoryStoreRequest $request): RedirectResponse
  {
    $postCategory = PostCategory::query()->create($request->all());
    ActivityLogHelper::storeModel('دسته بندی مطلب ثبت شد', $postCategory);

    return redirect()->back()->with('success', 'دسته بندی مطلب با موفقیت ثبت شد');
  }

  public function update(PostCategoryUpdateRequest $request, PostCategory $postCategory): RedirectResponse
  {
    $postCategory->update($request->all());
    ActivityLogHelper::updatedModel('دسته بندی مطلب بروز شد', $postCategory);

    return redirect()->back()->with('success', 'دسته بندی مطلب با موفقیت به روزرسانی شد');
  }

  public function destroy(PostCategory $postCategory): RedirectResponse
  {
    $postCategory->delete();
    ActivityLogHelper::deletedModel('دسته بندی مطلب حذف شد', $postCategory);

    return redirect()->back()->with('success', 'دسته بندی مطلب با موفقیت حذف شد');
  }
}
