@extends('admin.layouts.master')

@section('content')

    <div class="page-header">
        <x-breadcrumb :items="[
			['title' => 'لیست همه ادمین ها', 'route_link' => 'admin.admins.index'],
			['title' => 'مشاهده فعالیت ها']
		]" />
    </div>

    <x-card>
        <x-slot name="cardTitle">لیست فعالیت ها ({{ number_format($totalActivity) }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-table-component>
                <x-slot name="tableTh">
                    <tr>
                        <th>ردیف</th>
						<th>توضیحات</th>
						<th>شناسه لاگ</th>
						<th>تاریخ</th>
						<th>ساعت</th>
                    </tr>
                </x-slot>
                <x-slot name="tableTd">
                    @forelse ($activities as $activity)
						<tr>
							<td class="font-weight-bold">{{ $loop->iteration }}</td>
							<td>{{ $activity->description }}</td>
							<td>{{ $activity->id }}</td>
							<td>{{ verta($activity->created_at)->formatDate() }}</td>
							<td>{{ verta($activity->created_at)->formatTime() }}</td>
						</tr>
					@empty
                        @include('core::includes.data-not-found-alert', ['colspan' => 8])
                    @endforelse
                </x-slot>
				<x-slot name="extraData">
					<div class="mb-2">{{ $activities->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}</div>
				</x-slot>
            </x-table-component>
        </x-slot>
    </x-card>
@endsection
