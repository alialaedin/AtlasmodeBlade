<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;
use Modules\Blog\Http\Requests\Admin\Post\PostStoreRequest;
use Modules\Blog\Http\Requests\Admin\Post\PostUpdateRequest;
use Modules\Core\Http\Controllers\BaseController;
use Exception;

class PostController extends BaseController
{

  public function index()
  {
    $posts = Post::query()
      ->latest('id')
      ->filters()
      ->paginate()
      ->withQueryString();

    $categories = PostCategory::getAllCategories();

    return view('blog::admin.post.index', compact(['posts', 'categories']));
  }

  public function store(PostStoreRequest $request)
  {
    try {
      $category = PostCategory::query()->findOrFail($request->post_category_id);
      $post = $category->posts()->create($request->all());

      if ($request->tags)
        $post->attachTags($request->tags);
      if ($request->hasFile('image'))
        $post->addImage($request->image);
      if ($request->product_ids)
        $post->products()->attach($request->product_ids);

      ActivityLogHelper::storeModel('مطلب ثبت شد', $post);
      return redirect()->route('admin.posts.index')->with('success', 'مطلب با موفقیت ثبت شد');
    } catch (Exception $exception) {
      Log::error($exception->getTraceAsString());
      return redirect()->back()->withInput()->with('error', 'مشکلی در برنامه رخ داده است');
    }
  }

  public function update(PostUpdateRequest $request, Post $post)
  {
    try {
      $category = PostCategory::query()->findOrFail($request->post_category_id);
      $post->category()->associate($category);
      $post->fill($request->all());
      if ($request->hasFile('image')) {
        $post->addImage($request->image);
      }
      $post->save();
      $post->syncTags($request->input('tags', []));

      if ($request->product_ids) {
        $post->products()->sync($request->product_ids);
      }
      ActivityLogHelper::updatedModel('مطلب بروز شد', $post);
      return redirect()->route('admin.posts.index')->with('success', 'مطلب با موفقیت به روزرسانی شد');
    } catch (Exception $exception) {
      Log::error($exception->getTraceAsString());
      return redirect()->back()->withInput()->with('error', 'مشکلی در برنامه رخ داده است');
    }
  }

  public function show(Post $post)
  {
    return view('blog::admin.post.show', compact(['post']));
  }

  public function create()
  {
    $statuses = Post::getAvailableStatuses();
    $categories = PostCategory::getActiveCategories();

    return view('blog::admin.post.create', compact(['statuses', 'categories']));
  }

  public function edit(Post $post)
  {
    $statuses = Post::getAvailableStatuses();
    $categories = PostCategory::getActiveCategories();

    return view('blog::admin.post.edit', compact(['statuses', 'categories', 'post']));
  }

  public function destroy(Post $post)
  {
    $post->delete();
    ActivityLogHelper::deletedModel('مطلب حذف شد', $post);

    return redirect()->back()->with('success', 'مطلب با موفقیت حذف شد');
  }
}
