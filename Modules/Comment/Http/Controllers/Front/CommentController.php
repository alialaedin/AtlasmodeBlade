<?php

namespace Modules\Comment\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Blog\Entities\Post;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Http\Requests\Front\CommentStoreRequest;

class CommentController extends Controller
{
  public function store(Post $post, CommentStoreRequest $request)
  {
    $parentComment = Comment::without('children')->active()->find($request->parent_id);
    if ($parentComment && $parentComment->parent_id) {
      return redirect()->back()->with('error', 'امکان پاسخگویی به جواب وجود ندارد');
    }

    $comment = new Comment();
    $comment->fill($request->validated());
    $comment->creator()->associate(Auth::guard('customer')->user());
    $comment->parent()->associate($parentComment);
    $comment->commentable()->associate($parentComment->commentable);
    $comment->save();

    return redirect()->back()->with('success', 'نظر با موفقیت ثبت شده و پس از تایید نمایش داده خواهد شد');
  }
}
