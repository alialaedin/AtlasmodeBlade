<?php

namespace Modules\ProductComment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\ProductComment\Entities\ProductComment;
use Modules\ProductComment\Http\Requests\Admin\ProductCommentStoreRequest;

class ProductCommentController extends Controller
{
    public function index()
    {
        $comments = ProductComment::query()->parents()->filters()->latest('id')->with('childs')->paginate()->withQueryString();

        return view('productcomment::admin.index', compact('comments'));
    }

    public function show(ProductComment $productComment)
    {
        return view('productcomment::admin.show', compact([
            'comment' => $productComment
        ]));
    }

    public function answer(ProductCommentStoreRequest $request, ProductComment $productComment)
    {
        $productComment->fill($request->except('status'));
        $productComment->creator()->associate(auth()->user());
        $productComment->product()->associate($request->product_id);
        $productComment->status = ProductComment::STATUS_APPROVED;
        $productComment->save();

        return redirect()->back()->with('success', "جواب با موفقیت ثبت شد");
    }

    public function destroy(ProductComment $productComment)
    {
        $productComment->delete();
        ActivityLogHelper::deletedModel(' دیدگاه حذف شد', $productComment);

        return redirect()->back()->with('success', 'دیدگاه با موفقیت حذف شد');
    }

    public function assignStatus(Request $request)
    {
        $request->validate([
            'status' => ['required', Rule::in(ProductComment::getAvailableStatus())],
            'id' => ['required', 'exists:product_comments,id']
        ]);

        ProductComment::query()->findOrFail($request->id)->update(['status' => $request->status]);

        return redirect()->back()->with('success', "وضعیت دیدگاه با موفقیت به {$request->status} تغییر کرد");
    }
}
