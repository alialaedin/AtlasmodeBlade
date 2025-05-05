
<!-- Mobile Category Modal -->
<div class="modal modal-mobile-category bg-white radius-medium d-flex flex-column gap-2 px-6  pb-lg-4 pt-4 pb-5 overflow-auto" data-id="category">
  <h5 class="text-medium-3-strong color-gray-900 mx-auto">دسته بندی ها</h5>
	@if ($categories->isNotEmpty())
		<ul class="category-lists content d-flex flex-column gap-2">
			@foreach ($categories as $parentCategory)
				@if ($parentCategory->children->isEmpty())
					<li>
						<a class="text-medium-2-strong d-flex gap-1 align-items-center" href="{{ route('front.products.index', ['category_id' => $parentCategory->id]) }}">
							<i class="icon-dot-single icon-fs-xlarge"></i>
							<span class="color-gray-900">{{ $parentCategory->title }}</span>
						</a>
					</li>
				@else
					<li class="d-flex flex-column">
						<div class="d-flex justify-content-between align-items-center">
							<a class="text-medium-2-strong d-flex gap-1 align-items-center" href="{{ route('front.products.index', ['category_id' => $parentCategory->id]) }}">
								<i class="icon-dot-single icon-fs-xlarge"></i>
								<span class="color-gray-900">{{ $parentCategory->title }}</span>
							</a>
							<button type="button" class="category-btn border-black radius-circle w-4 h-4 text-center">
								<i class="icon-angle-left icon-fs-medium-2"></i>
								<i class="icon-angle-down icon-fs-medium-2"></i>
							</button>
						</div>
						<ul class="menu-sublist border-e-gray-300 pe-3 w-p-100 h-p-100 d-flex flex-column flex-wrap">
							@foreach ($parentCategory->children as $childCategory)
								@if ($childCategory->children->isEmpty())
									<li class="mainMenu-sublist-item-main pb-1 mt-3 d-flex align-items-center gap-2">
										<a class="text-truncate text-medium" href="{{ route('front.products.index', ['category_id' => $childCategory->id]) }}">
											{{ $childCategory->title }}
										</a>
									</li>
								@else
									<li class="d-flex flex-column">
										<div class="mainMenu-sublist-item-main pb-1 d-flex align-items-center justify-content-between">
											<a class="text-truncate text-medium" href="{{ route('front.products.index', ['category_id' => $childCategory->id]) }}">{{ $childCategory->title }}</a>
											<button type="button" class="category-btn-child border-black radius-circle w-4 h-4 text-center">
												<i class="icon-angle-left icon-fs-medium-2"></i>
												<i class="icon-angle-down icon-fs-medium-2"></i>
											</button>
										</div>
										<ul class="menu-sublist-child d-flex flex-column border-e-gray-300 pe-3">
											@foreach ($childCategory->chidren ?? [] as $grandChildCategory)
												<li class="mainMenu-sublist-item me-3 text-medium">
													<a class="text-truncate text-medium" href="{{ route('front.products.index', ['category_id' => $childCategory->id]) }}">
														{{ $grandChildCategory->title }}
													</a>
												</li>
											@endforeach
										</ul>
									</li>
								@endif
							@endforeach
						</ul>
					</li>
				@endif
			@endforeach			
		</ul>
	@endif
</div>

<!-- Mobile Menu Modal -->
<div class="modal modal-mobile-menu bg-white d-flex flex-column gap-2 px-2  pb-lg-4 pt-4 pb-5 overflow-auto" data-id="hamburgerMenu">
  <div class="d-flex justify-content-between align-items-center">
		<figure>
			<img src="{{asset('front-assets/images/header/logo.9208f443 (1).svg')}}" alt="logo">
		</figure>
		<button type="button" class="modal-close">
			<i class="icon-cancel icon-fs-large color-gray-700"></i>
		</button>
  </div>
	<ul class="category-lists content d-flex flex-column mt-1">

		<li class="d-flex flex-column">
			<div class="d-flex justify-content-between align-items-center">
				<a class="text-medium-2-strong d-flex gap-1 align-items-center" href="{{ route('front.products.index') }}">
					<i class="icon-dot-single icon-fs-2xl"></i>
					<span class="color-gray-900">محصولات</span>
				</a>
				<button type="button" class="category-btn">
					<i class="icon-angle-left icon-fs-medium-2"></i>
					<i class="icon-angle-down icon-fs-medium-2"></i>
				</button>
			</div>
			@if ($categories->isNotEmpty())
				<ul class="menu-sublist pe-3 w-p-100 h-p-100 d-flex flex-column flex-wrap">
					@foreach ($categories as $parentCategory)
						@if ($parentCategory->children->isEmpty())
							<li class="mainMenu-sublist-item-main pb-1 mt-1 d-flex align-items-center gap-1">
								<i class="icon-dot-single icon-fs-xlarge color-secondary-500"></i>
								<a class="text-truncate text-medium" href="{{ route('front.products.index', ['category_id' => $parentCategory->id]) }}">{{ $parentCategory->title }}</a>
							</li>
						@else
							<li class="d-flex flex-column">
								<div class="mainMenu-sublist-item-main pb-1 d-flex align-items-center justify-content-between">
									<a class="text-truncate text-medium d-flex align-items-center gap-1" href="{{ route('front.products.index', ['category_id' => $parentCategory->id]) }}">
										<i class="icon-dot-single icon-fs-xlarge"></i>
										<span>{{ $parentCategory->title }}</span>
									</a>
									<button type="button" class="category-btn-child">
										<i class="icon-angle-left icon-fs-medium-2"></i>
										<i class="icon-angle-down icon-fs-medium-2"></i>
									</button>
								</div>
								<ul class="menu-sublist-child d-flex flex-column pe-3">
									@foreach ($parentCategory->children as $childCategory)
										@if ($childCategory->children->isEmpty())
											<li class="mainMenu-sublist-item me-3 text-medium">
												<a class="text-truncate text-medium d-flex align-items-center gap-1" href="{{ route('front.products.index', ['category_id' => $parentCategory->id]) }}">
													<i class="icon-dot-single icon-fs-xlarge"></i>
													<span>{{ $childCategory->title }}</span>
												</a>
											</li>
										@else
											{{-- دسته بندی های نوه باید نشون داده بشن --}}
										@endif
									@endforeach
								</ul>
							</li>
						@endif	
					@endforeach
				</ul>
			@endif
		</li>

		@if (isset($menus['header']) && $menus['header']->isNotEmpty())
			@foreach ($menus['header'] as $headerParentMenuItem)
				@if ($headerParentMenuItem->children->isEmpty())
					<li>
						<a class="text-medium-2-strong d-flex gap-1 align-items-center" href="{{ $headerParentMenuItem->link_url }}">
							<i class="icon-dot-single icon-fs-2xl"></i>
							<span class="color-gray-900">{{ $headerParentMenuItem->title }}</span>
						</a>
					</li>
				@else
					<li class="d-flex flex-column">
						<div class="d-flex justify-content-between align-items-center">
							<a class="text-medium-2-strong d-flex gap-1 align-items-center" href="{{ $headerParentMenuItem->link_url }}">
								<i class="icon-dot-single icon-fs-2xl"></i>
								<span class="color-gray-900">{{ $headerParentMenuItem->title }}</span>
							</a>
							<button type="button" class="category-btn">
								<i class="icon-angle-left icon-fs-medium-2"></i>
								<i class="icon-angle-down icon-fs-medium-2"></i>
							</button>
						</div>
						<ul class="menu-sublist pe-3 w-p-100 h-p-100 d-flex flex-column flex-wrap">
							@foreach ($headerParentMenuItem->children as $headerChildMenuItem)
								@if ($headerChildMenuItem->children->isEmpty())
									<li class="mainMenu-sublist-item-main pb-1 mt-1 d-flex align-items-center gap-1">
										<i class="icon-dot-single icon-fs-xlarge color-secondary-500"></i>
										<a class="text-truncate text-medium" href="{{ $headerChildMenuItem->link_url }}">
											{{ $headerChildMenuItem->title }}
										</a>
									</li>
								@else
									<li class="d-flex flex-column">
										<div class="mainMenu-sublist-item-main pb-1 d-flex align-items-center justify-content-between">
											<a class="text-truncate text-medium d-flex align-items-center gap-1" href="{{ $headerChildMenuItem->link_url }}">
												<i class="icon-dot-single icon-fs-xlarge"></i>
												<span>{{ $headerChildMenuItem->title }}</span>
											</a>
											<button type="button" class="category-btn-child">
												<i class="icon-angle-left icon-fs-medium-2"></i>
												<i class="icon-angle-down icon-fs-medium-2"></i>
											</button>
										</div>
										<ul class="menu-sublist-child d-flex flex-column pe-3">
											@foreach ($headerChildMenuItem->children as $headerGrandChildMenuItem)
												@if ($headerGrandChildMenuItem->children->isEmpty())
													<li class="mainMenu-sublist-item me-3 text-medium">
														<a class="text-truncate text-medium d-flex align-items-center gap-1" href="{{ $headerGrandChildMenuItem->link_url }}">
															<i class="icon-dot-single icon-fs-xlarge"></i>
															<span>{{ $headerGrandChildMenuItem->title }}</span>
														</a>
													</li>
												@else
													{{-- دسته بندی های نوه باید نشون داده بشن --}}
												@endif
											@endforeach
										</ul>
									</li>
								@endif	
							@endforeach
						</ul>
					</li>
				@endif
			@endforeach
		@endif

	</ul>
</div>

<!-- Search Modal -->
<div class="modal modal-search bg-white radius-medium d-flex flex-column gap-2 px-6  pb-lg-4 pt-4 pb-3" data-id="search">
  <form class="search-form-modal py-1 px-1 w-p-100 d-flex align-items-center radius-small border-gray-300">
		<input type="text" class="flex-grow-1 p-1 text-medium" placeholder="جستوجو کنید...">
		<i class="icon-search icon-fs-large"></i>
  </form>
  <div id="product-search-contianer" class="d-flex flex-column justify-content-center mt-2">
		<ul class="product-list grid w-p-100 gap-2"></ul>
		<a href="{{ route('front.products.index') }}" class="show-more-products-btn d-flex d-none justify-content-center gap-1 text-medium mt-2">
			<span>مشاهده بیشتر</span>
			<i class="icon-angle-down icon-fs-small"></i>
		</a>
  </div>
</div>

<!-- Exit Modal -->
<div class="modal modal-exit radius-medium d-flex flex-column bg-white gap-4 px-6 py-4" data-id="exit">
	<div class="d-flex justify-content-between border-b-gray-400">
		<h4 class="h4 text-center" style="font-size: 20px"> حساب کاربری خارج شوید؟</h4>
		<button type="button" class="modal-close">
			<i class="icon-cancel icon-fs-small"></i>
		</button>
	</div>
	<p class="text-button" style="font-size: 14px">با خروج از حساب کاربری, به سبد خرید فعلی خود دسترسی نخواهید داشت.</p>
	<div class="d-flex justify-content-center gap-4">
		<button 
			style="font-size: 14px"
			type="button" 
			class="cancel-modal-btn modal-close bg-success-300 color-white text-medium radius-medium px-6 py-1">
			انصراف
		</button>
		<button 
			style="font-size: 14px"
			type="button" 
			class="exit-modal-btn bg-error-100 color-white text-medium px-7 py-1 radius-medium">
			خروج
		</button>
	</div>
</div>

@push('scripts')
	<script>
        document.getElementById('product-search-contianer').classList.add('d-none');
		document.addEventListener('DOMContentLoaded', () => {

			let typingTimeout;
			let abortController;

			document.querySelector('.search-form-modal input').addEventListener('input', (event) => {
				clearTimeout(typingTimeout);

				if (abortController) {
					abortController.abort();
				}

				const query = event.target.value.trim();
				if (!query) return;

				typingTimeout = setTimeout(async () => {
					abortController = new AbortController();
					try {
						const url = `${JSON.parse('@json(route("front.products.search"))')}?q=${encodeURIComponent(query)}`;
						const response = await fetch(url, {
							method: 'GET',
							headers: {
								'Accept': 'application/json',
								'Content-Type': 'application/json',
							},
							signal: abortController.signal,
						});

						if (response.ok) {
							const { data } = await response.json();
							const products = data.products;

							const productList = $('.product-list');
							productList.empty();

							if (products?.length) {
								products.forEach(({ main_image, slug, title, final_price, id }) => {
									const productBox = `
										<a href="/products/${id}" class="g-col-lg-3 p-1 g-col-md-6 g-col-12 border-gray-400 radius-small d-flex gap-1 align-items-center">
											<figure>
												<img class="w-p-100 radius-small" src="${main_image.url}" alt="${slug}">
											</figure>
											<div class="d-flex flex-column gap-1">
												<span class="text-truncate text-medium">${title}</span>
												<div class="d-flex gap-1 align-items-center flex-wrap">
													<div class="text-button-1 d-flex gap-1 color-error-100">
														<span class="currency">${final_price.amount.toLocaleString()}</span>
														<span>تومان</span>
													</div>
												</div>
											</div>
										</a>`;
									productList.append(productBox);
								});
								document.querySelector('.show-more-products-btn').classList.remove('d-none');
                                document.getElementById('product-search-contianer').classList.remove('d-none');
							} else {
								document.querySelector('.show-more-products-btn').classList.add('d-none');
                                document.getElementById('product-search-contianer').classList.add('d-none');
							}
						} else {
							console.error('Failed to fetch products:', response.status);
						}
					} catch (error) {
						if (error.name === 'AbortError') {
							console.log('Request was aborted due to new input.');
						} else {
							console.error('Error fetching products:', error);
						}
					}
				}, 1000);
			});

			document.querySelector('.exit-modal-btn').addEventListener('click', async () => {
				const url = JSON.parse('@json(route("customer.logout"))');
				try {

					const response = await fetch(url, {
						method: 'POST',
						headers: {
							'Accept': 'application/json',
							'X-CSRF-TOKEN': @json(csrf_token()),
							'Content-Type': 'application/json',
						},
					});

					if (response.ok) {
						const { message } = await response.json();
						window.location.replace('/');
					}
				} catch (error) {
					console.error('Error logging out:', error);
				}
			});

			document.querySelector('.cancel-modal-btn').addEventListener('click', function () {
				document.querySelector('.modal-overlay').classList.remove('active');
				document.querySelector('.modal-exit').classList.remove('active');
				document.body.classList.remove('no-overflow');
			});

		});
	</script>
@endpush