@foreach ($menu_items as $editItem)
<x-modal id="edit-menu-{{ $editItem->id }}" size="md">
    <x-slot name="title">ویرایش منو</x-slot>
    <x-slot name="body">
        <form action="{{ route('admin.menu.update', [$editItem->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" value="{{ $group }}" name="group_id">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">عنوان:<span class="text-danger">&starf;</span></label>
                    <input type="text" class="form-control" name="title"
                        placeholder="عنوان منو را اینجا وارد کنید" value="{{ old('title', $editItem->title) }}"
                        required autofocus>
                </div>
                <div class="form-group">
                    <label class="control-label">پدر:</label>
                    <select name="parent_id" class="form-control select2">
                        @if ($parentMenu)
                            @foreach ($menu_items as $menu_item)  
                                <option value="{{ $menu_item->id }}">  
                                    {{ $menu_item->title }}  
                                </option>  
                            @endforeach  
                            <option value="{{ $parentMenu->id }}" selected>{{ $parentMenu->title }}</option>  
                        @else
                            @foreach ($menu_items as $menu_item)  
                                <option value="{{ $menu_item->id }}">{{ $menu_item->title }}</option>  
                            @endforeach  
                            <option value="" class="text-muted" selected>ندارد</option>
                        @endif
                    </select>
                </div>
                <div class="row">
                    <div class="col-12 form-group">
                        <label class="control-label">نوع لینک :</label><span class="text-danger">&starf;</span>
                        <select name="linkable_type" onchange="toggleEditInput(this.value,{{ $editItem->id }})"
                            class="form-control" id="typeLink-{{ $editItem->id }}">
                            @foreach ($linkables as $link)
                                <option class="model" value="{{ $link['unique_type'] }}"
                                    @if ($link['linkable_type'] == $editItem->linkable_type) selected @endif>{{ $link['label'] }}</option>
                            @endforeach
                            <option value="self_link2" @if ($editItem->link) selected @endif
                                class="custom-menu">لینک دلخواه</option>
                        </select>
                    </div>
                    <div class="col-12 form-group " id="divLinkableEditId-{{ $editItem->id }}"
                        style="display: none">
                        <label class="control-label">آیتم های لینک :</label>
                        <select name="linkable_id" id="linkableEditId-{{ $editItem->id }}"
                            class="form-control select2">
                            <option class="custom-menu">انتخاب</option>
                        </select>
                    </div>
                    <div class="col-12 form-group">
                        <label class="control-label">لینک دلخواه :</label>
                        <input id="linkEdit-{{ $editItem->id }}" type="text" name="link"
                            class="form-control" value="{{ old('link', $editItem->link) }}" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="label" class="control-label"> وضعیت: </label>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="status"
                                    id="status" value="1"
                                    {{ old('status', $editItem->status) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">فعال</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="label" class="control-label"> تب جدید: </label>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="new_tab"
                                    value="1"
                                    {{ old('new_tab', $editItem->new_tab) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">فعال</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-warning text-right item-right">به روزرسانی</button>
                    <button type="button" class="btn btn-outline-danger text-right item-right"
                        data-dismiss="modal">برگشت</button>
                </div>
            </div>
        </form>
    </x-slot>
</x-modal>
@endforeach