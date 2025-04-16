@extends('admin.layouts.master')
@section('content')

    <div class="page-header">
        <x-breadcrumb :items="[
			['title' => 'محصولات انبار', 'route_link' => 'admin.stores.index'],
			['title' => 'تراکنش های انبار']
		]" />
    </div>

    <x-alert-danger />

    <x-card>
        <x-slot name="cardTitle">جستجوی پیشرفته</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <div class="row">
                <form action="{{ route('admin.stores.transactions') }}" method="GET"
                    class="col-12">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>شناسه :</label>
                                <input type="number" class="form-control" name="id" value="{{ request('id') }}">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>نوع تغییرات :</label>
                                <select class="form-control" name="type">
                                    <option value="">همه</option>
                                    @foreach (config('store.transaction_types') as $name => $label)
                                        <option value="{{ $name }}"
                                            {{ request('type') == $name ? 'selected' : null }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-xl-3 form-group">
                            <label>از تاریخ :</label>
                            <input class="form-control fc-datepicker" id="start_date_show" type="text" autocomplete="off" placeholder="از تاریخ" />
                            <input name="start_date" id="start_date_hide" type="hidden" value="{{ request('start_date') }}" />
                        </div>
                        <div class="col-12 col-xl-3 form-group">
                            <label>تا تاریخ :</label>
                            <input class="form-control fc-datepicker" id="end_date_show" type="text" autocomplete="off" placeholder="تا تاریخ" />
                            <input name="end_date" id="end_date_hide" type="hidden" value="{{ request('end_date') }}" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-9 col-lg-8 col-md-6 col-12">
                            <button class="btn btn-primary btn-block" type="submit">جستجو <i class="fa fa-search"></i></button>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                            <a href="{{ route('admin.stores.transactions') }}" class="btn btn-danger btn-block">
								حذف همه فیلتر ها <i class="fa fa-close"></i>
							</a>
                        </div>
                    </div>
                </form>
            </div>
        </x-slot>
    </x-card>

    <x-card>
        <x-slot name="cardTitle">تراکنش های انبار</x-slot>
		<x-slot name="cardOptions">
			<div class="card-options">
				<a href="{{ route('admin.stores.index') }}" class="btn btn-sm btn-info fs-12">محصولات انبار</a>
			</div>
		</x-slot>
        <x-slot name="cardBody">
			<x-table-component>
				<x-slot name="tableTh">
					<tr>
						@php($tableTh = ['ردیف', 'شناسه تراکنش', 'محصول', 'شناسه محصول', 'عامل', 'توضیحات', 'تعداد', 'نوع تغییرات', 'تاریخ ثبت'])
						@foreach ($tableTh as $th)
							<th>{{ $th }}</th>
						@endforeach
					</tr>
				</x-slot>
				<x-slot name="tableTd">
					@forelse ($storeTransactions as $transaction)
						<tr>
							<td class="font-weight-bold">{{ $loop->iteration }}</td>
							<td>{{ $transaction->id }}</td>
							<td>{{ $transaction->store->variety->title }}</td>
							<td>{{ $transaction->store->variety_id}}</td>
							<td>
                                @if ($transaction->creatorable instanceof Modules\Customer\Entities\Customer)
                                    {{ $transaction->creatorable->full_name }}
                                @else
                                    {{ $transaction->creatorable->name }}
                                @endif
                            </td>
							<td style="white-space: wrap;">{{ $transaction->description }}</td>
							<td>{{ $transaction->quantity }}</td>
							<td>
								<x-badge isLight="true">
									<x-slot name="type">{{ $transaction->type == 'increment' ? 'success' : 'danger' }}</x-slot>
									<x-slot name="text">{{ $transaction->type == 'increment' ? 'افزایش' : 'کاهش' }}</x-slot>
								</x-badge>
							</td>
							<td>{{ verta($transaction->created_at)->format('Y/m/d H:i') }}</td>
						</tr>
					@empty
						@include('core::includes.data-not-found-alert', ['colspan' => 9])
					@endforelse
				</x-slot>
				<x-slot
					name="extraData">{{ $storeTransactions->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}</x-slot>
			</x-table-component>
        </x-slot>
    </x-card>
@endsection

@section('scripts')
    @include('core::includes.date-input-script', [
        'dateInputId' => 'start_date_hide',
        'textInputId' => 'start_date_show',
    ])

    @include('core::includes.date-input-script', [
        'dateInputId' => 'end_date_hide',
        'textInputId' => 'end_date_show',
    ])
@endsection
