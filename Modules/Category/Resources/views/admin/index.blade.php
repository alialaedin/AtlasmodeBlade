@extends('admin.layouts.master')

@section('content')

<div class="page-header">
	<x-breadcrumb :items="[['title' => 'لیست دسته بندی های محصول']]" />
	<div>
		@php $sortBtnClass = $categories->count() > 0 ? '' : 'd-none' @endphp
		<button id="sort-btn" type="button" class="btn btn-teal btn-sm align-items-center btn-sm {{ $sortBtnClass }}">ذخیره مرتب سازی</button>
		<x-create-button route="admin.categories.create" title="ثبت دسته بندی جدید"/>
	</div>
</div>

<x-card>
	<x-slot name="cardTitle">لیست دسته بندی ها</x-slot>
	<x-slot name="cardOptions"><x-card-options /></x-slot>
	<x-slot name="cardBody">
		<ul class="list-style-cricle sortable" data-id="root">
			@foreach ($categories as $parentCategory)
				<li data-id="{{ $parentCategory->id }}">
					<div class="menu-title-box" style="opacity: {{ $parentCategory->status ? 1 : .5 }}">
						<span>{{ $parentCategory->title }}</span>
						<div>
							<a 
								class="btn btn-sm btn-icon btn-primary text-white" 
								href="{{ route('admin.categories.create', ['parent_id' => $parentCategory->id]) }}">
								<i class="fa fa-plus"></i>
							</a>
							<x-delete-button route="admin.categories.destroy" :model="$parentCategory" />
							<x-edit-button route="admin.categories.edit" :model="$parentCategory" />
						</div>
					</div>
					@if ($parentCategory->children->isNotEmpty())
						<ul class="list-style-square sortable">
							@foreach ($parentCategory->children as $childCategory)
								<li data-id="{{ $childCategory->id }}">
									<div class="menu-title-box" style="opacity: {{ $childCategory->status ? 1 : .5 }}">
										<span>{{ $childCategory->title }}</span>
										<div>
											<a 
												class="btn btn-sm btn-icon btn-primary text-white" 
												href="{{ route('admin.categories.create', ['parent_id' => $childCategory->id]) }}">
												<i class="fa fa-plus"></i>
											</a>
											<x-delete-button route="admin.categories.destroy" :model="$childCategory" />
											<x-edit-button route="admin.categories.edit" :model="$childCategory" />
										</div>
									</div>
									@if ($childCategory->children->isNotEmpty())
										<ul class="list-style-cricle sortable">
											@foreach ($childCategory->children as $grandChildCategory)
												<li data-id="{{ $grandChildCategory->id }}">
													<div class="menu-title-box" style="opacity: {{ $grandChildCategory->status ? 1 : .5 }}">
														<span>{{ $grandChildCategory->title }}</span>
														<div>
															<x-delete-button route="admin.categories.destroy" :model="$grandChildCategory" />
															<x-edit-button route="admin.categories.edit" :model="$grandChildCategory" />
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
			const sortUrl = @json(route('admin.categories.sort'));

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

				const data = buildCategoryStructure(rootElement);

				postData(sortUrl, {
					categories: data
				})
				.then(response => {
					popup('success', '', response.message);
					document.getElementById('sort-btn').disabled = false;
				})
				.catch(error => console.error('Error:', error));
			}

			function buildCategoryStructure(element) {
				return Array.from(element.querySelectorAll(':scope > li')).map(item => {
					const children = item.querySelector('ul') ? buildCategoryStructure(item.querySelector('ul')) : [];
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
			/* background-color: #f9f9f9;  */
			background-color: transparent; 
			font-weight: bold; 
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
	</style>
@endsection
