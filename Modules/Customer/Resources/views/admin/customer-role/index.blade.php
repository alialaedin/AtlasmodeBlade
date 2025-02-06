@extends('admin.layouts.master')
@section('content')

    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'نشق های مشتریان']]" />
        @can('write_customer')
            <x-create-button type="modal" target="create-customer-role-modal" title="نقش جدید" />
        @endcan
    </div>

    <x-card>
        <x-slot name="cardTitle">نقش ها ({{ $customerRoles->count() }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-table-component>
                <x-slot name="tableTh">
                    <tr>
						<th>ردیف</th>
						<th>نام</th>
						<th>دیدن محصولات زمان بندی شده</th>
						<th>تاریخ ثبت</th>
						<th>عملیات</th>
                    </tr>
                </x-slot>
                <x-slot name="tableTd">
                    @forelse($customerRoles as $role)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
							<td>
                                @if ($role->see_expired)
                                    <span><i class="text-success fs-26 fa fa-check-circle-o"></i></span>
                                @else
                                    <span><i class="text-danger fs-24 fa fa-close"></i></span>
                                @endif
                            </td>
                            <td>{{ verta($role->created_at)->format('Y/m/d H:i') }}</td>
                            <td>
								@include('core::includes.edit-modal-button', [
									'target' => '#edit-customer-role-modal-' . $role->id,
								])
								@include('core::includes.delete-icon-button', [
									'model' => $role,
									'route' => 'admin.customer-roles.destroy',
									'disabled' => !$role->is_deletable,
								])
                            </td>
                        </tr>
                    @empty
                        @include('core::includes.data-not-found-alert', ['colspan' => 5])
                    @endforelse
                </x-slot>
            </x-table-component>
        </x-slot>
    </x-card>

	<x-modal id="create-customer-role-modal" size="md">
        <x-slot name="title">ثبت نقش جدید</x-slot>
        <x-slot name="body">
            <form action="{{ route('admin.customer-roles.store') }}" method="POST">

                @csrf

                <div class="row">

                    <div class="col-12">
                        <div class="form-group">
							<label for="">نام : <span class="text-danger">&starf;</span></label>
                            <input class="form-control" name="name" required autofocus/>
                        </div>
                    </div>

					<div class="col-12 ">
						<div class="form-group">
							<label class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" name="see_expired" value="1"/>
								<span class="custom-control-label">دیدن محصولات زمانبندی شده</span>
							</label>
						</div>
					</div>

                </div>

                <div class="modal-footer justify-content-center mt-2">
                    <button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
                    <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
                </div>

            </form>
        </x-slot>
    </x-modal>

	@foreach ($customerRoles as $role)
		<x-modal id="edit-customer-role-modal-{{ $role->id }}" size="md">
			<x-slot name="title">ثبت نقش جدید</x-slot>
			<x-slot name="body">
				<form action="{{ route('admin.customer-roles.update', $role) }}" method="POST">

					@csrf
					@method('PUT')

					<div class="row">

						<div class="col-12">
							<div class="form-group">
								<label for="">نام : <span class="text-danger">&starf;</span></label>
								<input class="form-control" name="name" required autofocus value="{{ $role->name }}"/>
							</div>
						</div>

						<div class="col-12 ">
							<div class="form-group">
								<label class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="see_expired" value="1" {{ $role->see_expired ? 'checked' : '' }}/>
									<span class="custom-control-label">دیدن محصولات زمانبندی شده</span>
								</label>
							</div>
						</div>

					</div>

					<div class="modal-footer justify-content-center mt-2">
						<button class="btn btn-sm btn-warning" type="submit">بروزرسانی</button>
						<button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
					</div>

				</form>
			</x-slot>
		</x-modal>
	@endforeach


@endsection

