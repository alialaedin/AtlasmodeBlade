@extends('admin.layouts.master')

@section('styles')
	<style>
		.sortable li {
			cursor: move;
		}
		.menu-title-box {
			padding: 10px;
			margin: 5px 0;
			border: 1px solid #ccc;
			border-radius: 5px;
			background-color: #f9f9f9; 
			font-weight: bold; 
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
	</style>
@endsection

@section('content')

<div class="page-header">
	<x-breadcrumb :items="[['title' => 'منو های گروه ' . $menuGroup->label]]" />
	<div>
		@php $sortBtnClass = $menuItems->count() > 0 ? '' : 'd-none' @endphp
		<button id="sort-btn" type="button" class="btn btn-teal btn-sm align-items-center btn-sm {{ $sortBtnClass }}">ذخیره مرتب سازی</button>
		<x-create-button route="admin.menus.create" :parameter="['menuGroup' => $menuGroup]" title="ثبت منو جدید"/>
	</div>
</div>

<x-card>
	<x-slot name="cardTitle">لیست منو ها</x-slot>
	<x-slot name="cardOptions"><x-card-options /></x-slot>
	<x-slot name="cardBody">
		<ul class="list-style-cricle sortable" data-id="root">
			@foreach ($menuItems as $parentMenuItem)
				<li data-id="{{ $parentMenuItem->id }}">
					<div class="menu-title-box" style="opacity: {{ $parentMenuItem->status ? 1 : .5 }}">
						<span>{{ $parentMenuItem->title }}</span>
						<div>
							<a 
								class="btn btn-sm btn-icon btn-primary text-white" 
								href="{{ route('admin.menus.create', ['menuGroup' => $menuGroup, 'parent_id' => $parentMenuItem->id]) }}">
								<i class="fa fa-plus"></i>
							</a>
							<x-delete-button route="admin.menus.destroy" :model="$parentMenuItem" />
							<x-edit-button route="admin.menus.edit" :model="$parentMenuItem" />
						</div>
					</div>
					@if ($parentMenuItem->children->isNotEmpty())
						<ul class="list-style-square sortable">
							@foreach ($parentMenuItem->children as $childMenuItem)
								<li data-id="{{ $childMenuItem->id }}">
									<div class="menu-title-box" style="opacity: {{ $childMenuItem->status ? 1 : .5 }}">
										<span>{{ $childMenuItem->title }}</span>
										<div>
											<a 
												class="btn btn-sm btn-icon btn-primary text-white" 
												href="{{ route('admin.menus.create', ['menuGroup' => $menuGroup, 'parent_id' => $childMenuItem->id]) }}">
												<i class="fa fa-plus"></i>
											</a>
											<x-delete-button route="admin.menus.destroy" :model="$childMenuItem" />
											<x-edit-button route="admin.menus.edit" :model="$childMenuItem" />
										</div>
									</div>
									@if ($childMenuItem->children->isNotEmpty())
										<ul class="list-cricle-square sortable">
											@foreach ($childMenuItem->children as $grandChildMenuItem)
												<li data-id="{{ $grandChildMenuItem->id }}">
													<div class="menu-title-box" style="opacity: {{ $grandChildMenuItem->status ? 1 : .5 }}">
														<span>{{ $grandChildMenuItem->title }}</span>
														<div>
															<x-delete-button route="admin.menus.destroy" :model="$grandChildMenuItem" />
															<x-edit-button route="admin.menus.edit" :model="$grandChildMenuItem" />
														</div>
													</div>
												</li>
											@endforeach
										</ul>
									@endif
								</li>
							@endforeach
						</ul>
					@endif
				</li>
			@endforeach
		</ul>
	</x-slot>
</x-card>

@endsection

@section('scripts')
	<script>
		
		document.addEventListener('DOMContentLoaded', () => {

			const sortables = document.querySelectorAll('.sortable');
			const sortUrl = @json(route('admin.menus.sort'));
			const menuGroupId = @json($menuGroup->id);

			sortables.forEach(sortable => {
				new Sortable(sortable, {
					group: 'nested',
					animation: 150,
					fallbackOnBody: true,
					swapThreshold: 0.65,
				});
			});

			document.getElementById('sort-btn').addEventListener('click', handleSortEnd);

			function handleSortEnd() {
				document.getElementById('sort-btn').disabled = true;
				const rootElement = document.querySelector('.list-style-cricle');
				if (!rootElement) {
					return;
				}

				const data = buildMenuStructure(rootElement);

				postData(sortUrl, {
					menu_items: data,
					group_id: menuGroupId
				})
				.then(response => {
					popup('success', '', response.message);
					document.getElementById('sort-btn').disabled = false;
				})
				.catch(error => console.error('Error:', error));
			}

			function buildMenuStructure(element) {
				return Array.from(element.querySelectorAll(':scope > li')).map(item => {
					const children = item.querySelector('ul') ? buildMenuStructure(item.querySelector('ul')) : [];
					return {
						id: item.dataset.id,
						children: children
					};
				});
			}
			
			async function postData(url = '', data = {}) {
				const response = await fetch(url, {
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: JSON.stringify(data)
				});

				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}

				return response.json();
			}
		
		});

	</script>
@endsection
