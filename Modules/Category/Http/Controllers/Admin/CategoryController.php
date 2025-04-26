<?php

namespace Modules\Category\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Attribute\Entities\Attribute;
use Modules\Category\Entities\Category;
use Modules\Category\Http\Requests\Admin\CategorySortRequest;
use Modules\Category\Http\Requests\Admin\CategoryStoreRequest;
use Modules\Category\Http\Requests\Admin\CategoryUpdateRequest;
use Modules\Specification\Entities\Specification;

class CategoryController extends Controller
{
	public function index()
	{
		$categories = Category::getCategoriesForAdmin();
		return view('category::admin.index', compact('categories'));
	}

	public function create()
	{
		$attributes = Attribute::select('id', 'label')->get();
		$specifications = Specification::select('id', 'label')->get();
		$categories = Category::getCategoriesToSetParent();

		return view('category::admin.create', compact(['attributes', 'specifications', 'categories']));
	}

	public function store(CategoryStoreRequest $request)
	{
		Category::storeOrUpdate($request);
		return redirect()->route('admin.categories.index')->with('success', 'دسته بندی با موفقیت ایجاد شد');
	}

	public function sort(CategorySortRequest $request)
	{
		Category::sort($request->input('categories'));
		return response()->success('آیتم های دسته بندی با موفقیت مرتب سازی شد');
	}

	public function edit(Category $category)
	{
		$attributes = Attribute::select('id', 'label')->get();
		$specifications = Specification::select('id', 'label')->get();
		$categories = Category::getCategoriesToSetParent();

		return view('category::admin.edit', compact(['categories', 'attributes', 'specifications', 'category']));
	}

	public function update(CategoryUpdateRequest $request, Category $category)
	{
		Category::storeOrUpdate($request, $category);
		return redirect()->route('admin.categories.index')->with('success', 'دسته بندی با موفقیت بروزرسانی شد');
	}

	public function destroy(Category $category)
	{
		$category->delete();
		ActivityLogHelper::deletedModel('دسته بندی حذف شد', $category);
		return redirect()->route('admin.categories.index')->with('success', 'دسته بندی با موفقیت حذف شد');
	}
}
