@extends('admin.layouts.master')

@section('content')

  <div class="page-header">
    <x-breadcrumb :items="[['title' => 'انواع سایز چارت']]" />
    @can('write_size_chart')
      <button id="create-size-chart-type-btn" class="btn btn-sm btn-primary">سایز چارت جدید</button>
    @endcan
  </div>

  <x-card>
    <x-slot name="cardTitle">انواع سایزچارت</x-slot>
    <x-slot name="cardBody">
      <x-table-component>
        <x-slot name="tableTh">
          <tr>
            <th>ردیف</th>
            <th>عنوان</th>
            <th>ادمین سازنده</th>
            <th>تاریخ ثبت</th>
            <th>عملیات</th>
          </tr>
        </x-slot>
        <x-slot name="tableTd">
          @forelse($sizeChartTypes as $sizeChartType)
            <tr>
              <td class="font-weight-bold">{{ $loop->iteration }}</td>
              <td>{{ $sizeChartType->name }}</td>
              <td>{{ $sizeChartType->creator->name }}</td>
              <td style="direction: ltr">{{ verta($sizeChartType->created_at)->format('Y/m/d H:i') }}</td>
              <td>
                @can('read_size_chart')
                  <button 
                    class="btn btn-sm btn-dark show-size-chart-type-btn" 
                    data-id="{{ $sizeChartType->id }}">
                    مشاهده
                    <i class="fa fa-eye mr-1"></i>
                  </button>
                @endcan
                @can('modify_size_chart')
                  <button 
                    class="btn btn-sm btn-warning edit-size-chart-type-btn" 
                    data-update-url="{{ route('admin.size-chart-types.update', $sizeChartType) }}"
                    data-id="{{ $sizeChartType->id }}">
                    ویرایش
                    <i class="fa fa-pencil mr-1"></i>
                  </button>
                @endcan
                @can('delete_size_chart')
                  @include('core::includes.delete-icon-button', [
                    'model' => $sizeChartType,
                    'route' => 'admin.size-chart-types.destroy',
                    'title' => 'حذف'
                  ])
                @endcan
              </td>
            </tr>
          @empty
            @include('core::includes.data-not-found-alert', ['colspan' => 5])
          @endforelse
        </x-slot>
        <x-slot name="extraData">
          <div class="mb-2">{{ $sizeChartTypes->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}</div>
        </x-slot>
      </x-table-component>
    </x-slot>
  </x-card>

  <x-modal id="size-chart-type-modal" size="md">
    <x-slot name="title"></x-slot>
    <x-slot name="body">
      <form action="" method="POST">
        @csrf
        <div class="row">
          <div class="col-12 form-group">
            <label for="">عنوان : <span class="text-danger">&starf;</span></label>
            <input type="text" class="form-control" name="name" placeholder="عنوان تخفیف را وارد کنید" required/>
          </div>
        </div>
        <div id="values-row" class="row" style="gap: 8px;"></div>
        <div class="modal-footer justify-content-center mt-2">
          <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت و ذخیره</button>
          <button class="btn btn-sm btn-danger" type="button" data-dismiss="modal">انصراف</button>
        </div>
      </form>
    </x-slot>
  </x-modal>

  <x-modal id="show-chart-values-modal" size="md">
    <x-slot name="title">مقادیر سایز چارت</x-slot>
    <x-slot name="body">
      <div id="show-values-row" class="row" style="gap: 8px;"></div>
      <div class="row mt-3">
        <div class="col-12">
          <button class="btn-block btn btn-sm btn-danger" data-dismiss="modal">بستن</button>
        </div>
      </div>
    </x-slot>
  </x-modal>

  <div id="examples">
    <div id="example-size-chart-type-value-input" class="col-12 d-flex type-value-box" style="gap: 5px;">
      <button type="button" class="add-btn btn btn-success btn-sm">+</button>
      <button type="button" class="remove-btn btn btn-danger btn-sm">-</button>
      <input type="text" placeholder="مقدار" class="form-control form-control-sm">
    </div>  
    <div id="example-size-chart-type-value-text" class="col-12">
      <b class="counter fs-17"></b> : <span class="value-name fs-16"></span>
    </div>
  </div>

@endsection

@section('scripts')

  <script>  

    const exampleSizeChartTypeValueInputBox = $('#example-size-chart-type-value-input').clone().removeAttr('id');  
    const exampleSizeChartTypeValueText = $('#example-size-chart-type-value-text').clone().removeAttr('id');  
    const createEditModal = $('#size-chart-type-modal');  
    const showModal = $('#show-chart-values-modal');  
    const form = createEditModal.find('form');  
    const sizeChartTypes = @json($sizeChartTypes).data;  
    const createEditSizeChartValuesRow = $('#values-row');  
    const showSizeChartValuesRow = $('#show-values-row');  
    const createSizeChartTypeBtn = $('#create-size-chart-type-btn');  

    const openModal = (modal) => modal.modal('show');  
    const emptyValuesRow = (row) => row.empty();  
    const updateFormAction = (url = null) => form.attr('action', url ?? @json(route('admin.size-chart-types.store')));  
    const toggleRemoveBtn = (enable) => $('.type-value-box .remove-btn').prop('disabled', !enable);  
    const addTypeValueBox = () => createEditSizeChartValuesRow.append(exampleSizeChartTypeValueInputBox.clone());  
    const deleteExamples = () => $('#examples').remove();
    const changeModalTitle = (title) => createEditModal.find('.modal-title').text(title);

    const add = () => {  
      $(document).on('click', '.add-btn', () => {  
        addTypeValueBox();  
        toggleRemoveBtn($('.type-value-box').length > 1);  
      });  
    };  

    const remove = () => {  
      $(document).on('click', '.remove-btn', function (event) {  
        event.preventDefault();
        $(this).closest('.type-value-box').remove();  
        toggleRemoveBtn($('.type-value-box').length > 1);  
      });  
    };  

    const show = () => {
      $(document).on('click', '.show-size-chart-type-btn', function (event) {  
        emptyValuesRow(showSizeChartValuesRow);  
        const sizeChartTypeId = $(event.target).data('id');
        const sizeChartTypeValues = sizeChartTypes.find(s => s.id == sizeChartTypeId).values;
        let index = 1;
        sizeChartTypeValues.forEach(value => {
          const column = exampleSizeChartTypeValueText.clone()
          column.find('.counter').text(index++);  
          column.find('.value-name').text(value.name);
          showSizeChartValuesRow.append(column);
        });
        openModal(showModal);  
      });  
    };

    const create = () => {  
      createSizeChartTypeBtn.click(() => {  
        changeModalTitle('ایجاد سایز چارت جدید');
        openModal(createEditModal);  
        emptyValuesRow(createEditSizeChartValuesRow);  
        form.find('input[name=_method]').remove();  
        form.find('input[name="title"]').val(null);
        addTypeValueBox();  
        updateFormAction();  
        toggleRemoveBtn(false);  
      });  
    };  

    const edit = () => {  
      $(document).on('click', '.edit-size-chart-type-btn', function () {  
        changeModalTitle('ویرایش سایز چارت');
        openModal(createEditModal);  
        emptyValuesRow(createEditSizeChartValuesRow);  

        if (form.find('input[name="_method"]').length == 0) {  
          form.append('<input hidden name="_method" value="PUT">');  
        } 

        const sizeChartType = sizeChartTypes.find(s => s.id == $(this).data('id')); 

        form.find('input[name="title"]').val(sizeChartType.name);
        sizeChartType.values.forEach(value => {  
          const inputBox = exampleSizeChartTypeValueInputBox.clone();  
          inputBox.find('input').val(value.name);  
          createEditSizeChartValuesRow.append(inputBox);  
        });  

        toggleRemoveBtn(true);  
        updateFormAction($(this).data('update-url'));  
      });  
    };  

    const submit = () => {
      $('.submit-btn').click((event) => {
        event.preventDefault();
        let index = 0;
        $('.type-value-box').each(function () {
          $(this).find('input').attr('name', `values[${index++}]`);
        });
        form.submit();
      });
    };

    deleteExamples();
    show();
    create();  
    edit();  
    submit();
    add();
    remove();

  </script>

@endsection
