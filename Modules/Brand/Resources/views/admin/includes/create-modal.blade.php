<div class="modal fade" id="createBrandModal" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-content-demo">
      <div class="modal-header">
        <p class="modal-title" style="font-size: 20px;">ثبت برند جدید</p>
        <button aria-label="Close" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.brands.store') }}" method="post" class="save" enctype="multipart/form-data">
          @csrf
          <div class="row">

            <div class="col-12 col-lg-6">
              <div class="form-group">
                <label for="name" class="control-label">نام برند :<span class="text-danger">&starf;</span></label>
                <input type="text" id="name" class="form-control" name="name" required value="{{ old('name') }}">
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <div class="form-group">
                <label for="image" class="control-label"> عکس برند: </label>
                <input type="file" id="image" class="form-control" name="image" value="{{ old('image') }}">
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <div class="form-group">
                <label class="custom-control custom-checkbox">
                  <input
                    type="checkbox"
                    class="custom-control-input"
                    name="status"
                    value="1"
                    {{ old('status', 1) == 1 ? 'checked' : null }}
                  />
                  <span class="custom-control-label">وضعیت</span>
                </label>
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <div class="form-group">
                <label class="custom-control custom-checkbox">
                  <input
                    type="checkbox"
                    class="custom-control-input"
                    name="show_index"
                    value="1"
                    {{ old('show_index', 1) == 1 ? 'checked' : null }}
                  />
                  <span class="custom-control-label">نمایش در صفحه اصلی</span>
                </label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label for="description" class="control-label"> توضیحات: </label>
                <textarea id="description" class="form-control" rows="4" name="description">{{ old('description') }}</textarea>
              </div>
            </div>

          </div>

          <div class="modal-footer justify-content-center">
            <button class="btn btn-primary" type="submit">ثبت و ذخیره</button>
            <button class="btn btn-outline-danger" data-dismiss="modal">انصراف</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
