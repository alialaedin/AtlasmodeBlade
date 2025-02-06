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
    public function index($id = null)
    {
        // $categories = Category::query()
        //     ->parents()
        //     ->orderByDesc('priority')
        //     ->filters();

        // if (\request('all')) {
        //     $categories->with('children');
        // }
        // $categories = $categories->get();
        if ($id) {
            $parentCategory = Category::find($id);
        }else{
            $parentCategory = null;
        }
        $categories = Category::where('parent_id',$id)->orderBy('priority','asc')->get();

        return view('category::admin.index', compact('categories','parentCategory'));
    }

    public function create($parent_id = null)  
    {  
        if($parent_id){
            $parentCategory = Category::find($parent_id);  
            $childrenCategories = $parentCategory->children()->get();  
            $parentsCategories = collect([$parentCategory])->merge($childrenCategories);  
            $isChildren = 1; 
        }else{
            $parentsCategories = Category::select('id','title')->parents()->get();
            $isChildren = 0; 
        }
        $attributes = Attribute::select('id', 'label')->get();
        $specifications = Specification::select('id', 'label')->get();

        return view('category::admin.create', compact(['attributes', 'specifications','parentsCategories','isChildren']));
    }

    public function store(CategoryStoreRequest $request , Category $category)
    {
        $category->fill($request->all());

        if($request->parent_id != null){
            $findParent = Category::query()->find($request->parent_id);
            $category->level = $findParent->level + 1 ;
        }
        $category->save();
        $category->attributes()->attach($request->attribute_ids);
        $category->specifications()->attach($request->specification_ids);
        $category->brands()->attach($request->brand_ids);
        ActivityLogHelper::storeModel('دسته بندی ثبت شد', $category);


        if ($request->hasFile('image')) {
            $category->addImage($request->file('image'));
        }
        if ($request->hasFile('icon')) {
            $category->addIcon($request->file('icon'));
        }

        if (request()->header('Accept') == 'application/json') {
            return response()->success('دسته بندی با موفقیت ایجاد شد.', compact('category'));
		}
        if (filled($request->parent_id)) {
            return redirect()->route('admin.categories.index',$request->parent_id)->with('success', 'دسته بندی با موفقیت ایجاد شد');
        }else{
            return redirect()->route('admin.categories.index')->with('success', 'دسته بندی با موفقیت ایجاد شد');
        }

    }

    public function sort(CategorySortRequest $request)
    {
        Category::sort($request->input('categories'));
        // Cache::deleteMultiple(['home_special_category', 'home_category']);

        if (request()->header('Accept') == 'application/json') {
            return response()->success('مرتب سازی با موفقیت انجام شد');
		}
        return redirect()->back()->with('success', 'مرتب سازی با موفقیت انجام شد');

    }

    public function show($id)
    {
        $category = Category::query()->find($id);

        if (request()->header('Accept') == 'application/json') {
            return response()->success('', compact('category'));
		}
        return view('category::admin.show', compact('category'));
    }

    public function edit(Category $category)
    {
        if ($category->parent_id == null) {
            $parentsCategories = Category::select('id', 'title')->parents()->get();
            $isChildren = 0; 
        }else{
            $parentCategory = Category::find($category->parent_id);  
            $childrenCategories = $parentCategory->children()->where('id', '!=', $category->id)->get();
            $parentsCategories = collect([$parentCategory])->merge($childrenCategories);  
            $isChildren = 1; 
        }
        $attributes = Attribute::select('id', 'label')->get();
        $specifications = Specification::select('id', 'label')->get();

        return view('category::admin.edit', compact(['parentsCategories','isChildren', 'attributes', 'specifications', 'category']));
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        $category = Category::query()->find($id);
        $category->fill($request->validated());
        if ($request->hasFile('image')) {
            $category->addImage($request->image);
        }
        if ($request->hasFile('icon')) {
            $category->addIcon($request->icon);
        }

        $category->attributes()->sync($request->attribute_ids);
        $category->specifications()->sync($request->specification_ids);
        $category->brands()->sync($request->brand_ids);

        $category->save();
        ActivityLogHelper::updatedModel('دسته بندی بروز شد', $category);

        return redirect()->route('admin.categories.index')->with('success', 'دسته بندی با موفقیت بروزرسانی شد');
    }


    public function destroy($id)
    {
        $category = Category::query()->findOrFail($id);
        $category->delete();
        ActivityLogHelper::deletedModel('دسته بندی حذف شد', $category);

        return redirect()->route('admin.categories.index')->with('success', 'دسته بندی با موفقیت حذف شد');
    }
}
