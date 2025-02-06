<?php

namespace Modules\Comment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Blog\Entities\Post;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Http\Requests\Admin\CommentAnswerRequest;
use Modules\Comment\Http\Requests\Admin\CommentUpdateRequest;

class CommentController extends Controller
{
  public function index(Post $post)
  {
    $comments = $post
      ->comments()
      ->latest('id')
      ->with(['children', 'parent'])
      ->paginate();

    $totalComments = $comments->total();

    return view('comment::admin.index', compact(['comments', 'post', 'totalComments']));
  }

  public function all()
  {
    $comments = Comment::query()
      ->select('id', 'name', 'email', 'body', 'commentable_type', 'commentable_id', 'parent_id', 'status', 'created_at')
      ->where('commentable_type', Post::class)
      ->latest('id')
      ->with(['children', 'parent', 'commentable'])
      ->paginate();

    $totalComments = $comments->total();

    return view('comment::admin.all', compact(['comments', 'totalComments']));
  }

  public function show($id)
  {
    $comment = Comment::query()->with(['commentable', 'children'])->findOrFail($id);

    return view('comment::admin.show', compact(['comment']));
  }

  public function answer($id, CommentAnswerRequest $request)
  {
    $oldComment = Comment::without('children')->findOrFail($id);
    if ($oldComment->parent_id) {
      return redirect()->back()->with('error', 'این نظر پاسخ نظر دیگری است و نمی توان برای آن پاسخی ثبت کرد');
    }

    $comment = new Comment();
    $comment->fill($request->all());
    $comment->creator()->associate(Auth::guard('admin-api')->user());
    $comment->parent()->associate($oldComment);
    $comment->commentable()->associate($oldComment->commentable);
    $comment->save();

    ActivityLogHelper::storeModel('پاسخ به نظر مظلب ثبت شد', $comment);

    return redirect()->back()->with('success', 'پاسخ با موفقیت ثبت شد');
  }

  public function update($id, CommentUpdateRequest $request)
  {
    $comment = Comment::with('commentable', 'creator')->without('children')->findOrFail($id);
    $comment->update($request->only(['name', 'email', 'body', 'status']));

    ActivityLogHelper::updatedModel(' نظر مظلب ویرایش شد', $comment);

    return redirect()->back()->with('success', 'نظر شما با موفقیت ویرایش شد');
  }

  public function destroy(Comment $comment)
  {
    $comment->delete();
    ActivityLogHelper::deletedModel(' نظر مظلب حذف شد', $comment);

    return redirect()->back()->with('success', 'نظر شما با موفقیت حذف شد');
  }
}
