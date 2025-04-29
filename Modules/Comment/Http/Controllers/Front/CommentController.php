<?php

namespace Modules\Comment\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
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

    $comment = $post->comments()->create($request->all());
    ActivityLogHelper::storeModel("نظر برای مطلب با شناسه $post->id توسط مشتری ثبت شدت", $comment);

    return redirect()->back()->with('success', 'نظر با موفقیت ثبت شده و پس از تایید نمایش داده خواهد شد');
  }
}
