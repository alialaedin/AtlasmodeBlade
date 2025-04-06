@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
		<x-breadcrumb :items="[['title' => 'گروه های پیشنهادی']]" />
  </div>

  <x-card>
		<x-slot name="cardTitle">گروه های پیشنهادی ({{ $recommendationGroups->count() }})</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<x-table-component>  
        <x-slot name="tableTh">  
          <tr>  
            <th>ردیف</th>  
            <th>عنوان (انگیلیسی)</th>  
            <th>عنوان (فارسی)</th>  
            <th>نمایش در صفحه اصلی</th>  
            <th>نمایش در فیلتر محصولات</th>  
            <th>تعداد آیتم ها</th>  
            {{-- <th>تاریخ ثبت</th>   --}}
            <th>عملیات</th>  
          </tr>  
        </x-slot>  
        <x-slot name="tableTd">  
          @forelse ($recommendationGroups as $group)  
            <tr>  
              <td class="font-weight-bold">{{ $loop->iteration }}</td>  
              <td>{{ $group->name }}</td>  
              <td>{{ $group->label }}</td>  
              <td>
                @if ($group->show_in_home)
                <span><i class="text-success fs-26 fa fa-check-circle-o"></i></span>
                @else
                <span><i class="text-danger fs-24 fa fa-close"></i></span>
                @endif
              </td>
              <td>
                @if ($group->show_in_filter)
                <span><i class="text-success fs-26 fa fa-check-circle-o"></i></span>
                @else
                <span><i class="text-danger fs-24 fa fa-close"></i></span>
                @endif
              </td>
              <td>{{ $group->items_count }}</td>  
              {{-- <td>{{ verta($group->created_at)->format('Y/m/d H:i')}}</td>   --}}
              <td>  
                <x-show-button :model="$group" route="admin.recommendations.index" title="آیتم ها" />
                <x-edit-button :is-modal="true" :has-title="true" :target="'editGroup-' . $group->id" />
              </td>  
            </tr>  
          @empty  
            @include('core::includes.data-not-found-alert', ['colspan' => 7])  
          @endforelse  
        </x-slot>  
      </x-table-component>
		</x-slot>
	</x-card>

  @foreach ($recommendationGroups ?? [] as $group)
    <x-modal id="editGroup-{{ $group->id }}" size="md">
      <x-slot name="title">ویرایش گروه پیشنهادی</x-slot>
      <x-slot name="body">
        <form action="{{ route('admin.recommendation-groups.update', $group) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-12">
              <label for="show-in-home-checkbox-{{ $group->id }}" class="custom-control custom-checkbox">
                <input id="show-in-home-checkbox-{{ $group->id }}" name="show_in_home" type="checkbox" class="custom-control-input" value="1" {{ $group->show_in_home ? 'checked' : '' }}/>
                <span class="custom-control-label">نمایش در صفحه اصلی</span>
              </label> 
              <label for="show-in-filter-checkbox-{{ $group->id }}" class="custom-control custom-checkbox">
                <input id="show-in-filter-checkbox-{{ $group->id }}" name="show_in_filter" type="checkbox" class="custom-control-input" value="1" {{ $group->show_in_filter ? 'checked' : '' }}/>
                <span class="custom-control-label">نمایش در فیلتر محصولات</span>
              </label> 
            </div> 
          </div>
          <div class="modal-footer justify-content-center pb-0">
            <button class="btn btn-sm btn-warning" type="submit">بروزرسانی</button>
            <button class="btn btn-sm btn-danger" type="button" data-dismiss="modal">انصراف</button>
          </div>
        </form>
      </x-slot>
    </x-modal>
  @endforeach

@endsection
