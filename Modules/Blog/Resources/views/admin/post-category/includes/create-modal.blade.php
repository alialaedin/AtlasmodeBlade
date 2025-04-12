<div class="modal fade" id="createPostCategoryModal" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content modal-content-demo">
      <div class="modal-header">
        <p class="modal-title" style="font-size: 20px;">ثبت دسته بندی مطلب جدید</p>
        <button aria-label="Close" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.post-categories.store') }}" method="post" class="save">
          @csrf
          <div class="row">

            <div class="col-12">
              <div class="form-group">
                <input 
                  type="text" 
                  class="form-control" 
                  placeholder="نام دسته بندی *"
                  name="name" 
                  required 
                  value="{{ old('name') }}"
                />
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
          </div>

          <div class="modal-footer justify-content-center pb-0">
            <button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
            <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
