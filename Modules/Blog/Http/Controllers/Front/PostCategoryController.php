<?php

namespace Modules\Blog\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Blog\Entities\PostCategory;

class PostCategoryController extends Controller
{
  public function index()
  {
    $postCategories = PostCategory::active()
      ->orderBy('order', 'asc')
      ->filters()
      ->get(['id', 'name', 'slug']);

    return response()->success('', compact('postCategories'));
  }
}
