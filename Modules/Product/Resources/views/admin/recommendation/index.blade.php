@extends('admin.layouts.master')

@section('styles')
  <style>
    .glyphicon-move:before {
      content: none;
    }
  </style>
@endsection

@section('content')

  @php
    $groupLabel = trans("core::groups.$group");
  @endphp

  <div class="page-header">
		<x-breadcrumb :items="[
      ['title' => 'گروه های پیشنهادی', 'route_link' => 'admin.recommendations.groups'],
      ['title' => $groupLabel]
    ]" />
    <div>
      <button class="btn btn-sm btn btn-teal" type="button" onclick="sort(event)">ذخیره مرتب سازی</button>
      <x-create-button type="modal" target="add-new-product-modal" title="افزودن محصول" />
    </div>
  </div>

  <x-card>
		<x-slot name="cardTitle">گروه پیشنهادی : {{ $groupLabel }}</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<x-table-component id="recommendations-table">  
        <x-slot name="tableTh">  
          <tr>  
            <th>ردیف</th>  
            <th>شناسه</th>  
            <th>محصول</th>  
            <th>تاریخ ثبت</th>  
            <th>عملیات</th>  
          </tr>  
        </x-slot>  
        <x-slot name="tableTd">  
          @forelse ($recommendations as $recommendation)  
            <tr class="glyphicon-move" style="cursor: move;">  
              <td class="recommendation-id" data-recommendation-id="{{ $recommendation->id }}" style="display: none;"></td>  
              <td class="font-weight-bold">{{ $loop->iteration }}</td>  
              <td>{{ $recommendation->id }}</td>  
              <td>{{ $recommendation->product->title }}</td>  
              <td>{{ verta($recommendation->created_at)->format('Y/m/d H:i')}}</td>  
              <td>  
                @include('core::includes.delete-icon-button', [  
                  'model' => $recommendation,  
                  'route' => 'admin.recommendations.destroy',  
                  'disabled' => false,  
                ])  
              </td>  
            </tr>  
          @empty  
            @include('core::includes.data-not-found-alert', ['colspan' => 5])  
          @endforelse  
        </x-slot>  
      </x-table-component>
      <div class="row">
        <div class="col-12">
          <button class="btn btn-sm btn-teal" type="button" onclick="sort(event)">ذخیره مرتب سازی</button>
        </div>
      </div>
		</x-slot>
	</x-card>

  <x-modal id="add-new-product-modal" size="md">
    <x-slot name="title">افزودن محصول جدید به گروه پیشنهادی</x-slot>
    <x-slot name="body">
      <form action="{{ route('admin.specific-discounts.store') }}" method="POST">
        @csrf
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <input hidden name="group" value="{{ $group }}">
              <select id="product-select-box" name="product_id" class="form-control" required>  
                <option value="">انتخاب محصول</option>  
              </select>  
            </div> 
          </div> 
        </div>
        <div class="modal-footer justify-content-center mt-2">
          <button class="btn btn-sm btn-primary" type="submit">افزودن</button>
          <button class="btn btn-sm btn-danger" type="button" data-dismiss="modal">انصراف</button>
        </div>
      </form>
    </x-slot>
  </x-modal>

  <form 
    id="sort-recommendations-form" 
    action="{{ route('admin.recommendations.sort', $group) }}" 
    class="d-none" 
    method="POST">
    @csrf
  </form>

@endsection

@section('scripts')
  <script>
     $('#product-select-box').select2({  
      ajax: {  
        url: @json(route('admin.products.search')),  
        dataType: 'json',  
        delay: 250, 
        processResults: (response) => {  
          let products = response.data.products || [];  
          return {  
            results: products.map(product => ({  
              id: product.id,  
              title: product.title,  
            })),  
          };  
        },  
        cache: true,  
        error: (jqXHR, textStatus, errorThrown) => {  
          console.error("Error fetching products:", textStatus, errorThrown);  
        },  
      },  
      placeholder: 'عنوان محصول را وارد کنید',  
      minimumInputLength: 1,  
      templateResult: (repo) => {  
        if (repo.loading) return "در حال بارگذاری...";  

        let $container = $(  
          "<div class='select2-result-repository clearfix'>" +  
          "<div class='select2-result-repository__meta'>" +  
          "<div class='select2-result-repository__title'></div>" +  
          "</div>" +  
          "</div>"  
        );  

        $container.find(".select2-result-repository__title").text(repo.title);  

        return $container;  
      },  
      templateSelection: (repo) => {  
        return repo.id ? repo.title : repo.text;  
      },  
    });

    function sort(event) {  
      event.preventDefault();  

      const sortForm = $('#sort-recommendations-form');  
      const table = $('#recommendations-table');  
      let priority = 0;  
      const ids = []; 

      table.find('tbody tr').each(function() {   
        const recommendationId = parseInt($(this).find('.recommendation-id').data('recommendation-id'));   
        ids.push(`<input name="ids[${priority++}]" value="${recommendationId}" hidden/>`);  
      });  
    
      sortForm.append(ids.join(''));
      sortForm.submit();  
  }  

    $(document).ready(() => {  
      let items = $('#recommendations-table').find('tbody');
      if (items.length) {  
        let sortable = Sortable.create(items[0], {
          handle: '.glyphicon-move',  
          animation: 150  
        });  
      }    
    });  

  </script>
@endsection

