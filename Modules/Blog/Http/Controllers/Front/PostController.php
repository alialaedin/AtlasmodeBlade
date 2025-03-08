<?php

namespace Modules\Blog\Http\Controllers\Front;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Advertise\Entities\Advertise;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;

class PostController extends Controller
{
  public function index()
  {
    $search = '%' . request('search') . '%';
    $postCategroryId = request('post_category_id');

    $posts = Post::query()
      ->select(['id', 'title', 'status', 'published_at', 'summary', 'post_category_id', 'slug'])
      ->published()
      ->with('category', fn ($q) => $q->select(['id', 'name', 'slug']))
      ->when($postCategroryId, fn ($q) => $q->where('post_category_id', $postCategroryId))
      ->when($search, function ($q) use ($search) {
        $q->where('title', 'LIKE', $search)
          ->orWhere('summary', 'LIKE', $search)
          ->orWhereHas('category', fn ($cq) =>  $cq->where('name', 'LIKE', $search));
      })
      ->take(9)
      ->get();
    
    return view('blog::front.index', compact('posts'));
  }

  public function show($id)
  {
    $user = auth()->user();
    $getPost = Post::query()->published()->findOrFail($id)->loadCommonRelations();
    $getPost->setAttribute('view_count', views($getPost)->count());
    views($getPost)->record();

    if ($getPost->status != Post::STATUS_PUBLISHED || Carbon::now()->lt($getPost->published_at)) {
      return response()->error('دسترسی به این مطلب امکان پذیر نیست', [], 403);
    }

    $category  = PostCategory::query()->active()->get();
    $suggests  = Post::query()->published()
      ->withCount('views')
      ->with('category')
      ->where('post_category_id',  $getPost->post_category_id)
      ->where('id', '!=', $getPost->id)
      ->inRandomOrder()->take(3)->get();

    $lastPost  = Post::query()->select(['id', 'title', 'slug', 'created_at'])->published()
      ->where('id', '!=', $getPost->id)->withCount('views')->latest()->take(10)->get();
    $banner = Advertise::getForHome();

    $post = [
      'post' => $getPost,
      'category' => $category,
      'suggests' => $suggests,
      'lastPost' => $lastPost,
      'user' => $user,
      'banner' => $banner
    ];

    return response()->success('', compact('post'));
  }
}
