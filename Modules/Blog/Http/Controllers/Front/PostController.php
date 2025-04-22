<?php

namespace Modules\Blog\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;

class PostController extends Controller
{
  public function index($categoryId = null)
  {
    $search = '%' . request('search') . '%';
    $postCategories = PostCategory::getAllPostCategoriesForFront();
    $posts = Post::query()
      ->select(['id', 'title', 'status', 'published_at', 'summary', 'post_category_id', 'slug'])
      ->published()
      ->with('category', fn ($q) => $q->select(['id', 'name', 'slug']))
      ->when($categoryId, fn ($q) => $q->where('post_category_id', $categoryId))
      ->when($search, function ($q) use ($search) {
        $q->where('title', 'LIKE', $search)
          ->orWhere('summary', 'LIKE', $search)
          ->orWhereHas('category', fn ($cq) =>  $cq->where('name', 'LIKE', $search));
      })->paginate(9)->withQueryString();
    
    return view('blog::front.index', compact('posts', 'postCategories'));
  }

  public function show(Post $post)
  {
    abort_unless($post->isPublished(), 404, 'دسترسی به این مطلب امکان پذیر نیست');
    views($post)->record();

    $post->load(['creator', 'category']);
    $post->loadCount(['views', 'comments']);

    $relatedPosts = Post::getRelatedPosts($post);
    $latestPosts = Post::getLatestPosts($post);
    $postComments = Post::getLatestComments($post);
    $postCategories = PostCategory::getAllPostCategoriesForFront();

    return view('blog::front.show', compact(['post', 'relatedPosts', 'latestPosts', 'postComments', 'postCategories']));
  }

  public function byCategory($categoryId, $slug)
  {
    return $this->index($categoryId); 
  }
}
