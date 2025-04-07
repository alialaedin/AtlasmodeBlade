<header class="header px-4 px-md-8 px-3xl-0 pb-1 bg-gray-100 position-relative">
  <div class="header-top container-2xl d-flex justify-content-between align-items-sm-center pb-2 pt-3 pt-lg-5">

		<button type="button" data-modal="hamburgerMenu" class="d-lg-none">
			<i class="icon-hamburger-menu icon-fs-medium"></i>
		</button>

		<!-- Logo -->
		<figure class=" d-flex">
			<a href="{{ url('/') }}" class="logo-header d-flex">
			<img class="w-p-100" src="{{ asset('front-assets/images/header/logo.9208f443 (1).svg') }}" alt="logo">
			</a>
		</figure>

		<!-- Menu -->
		<nav class="header-nav d-none d-lg-block me-2">
			<ul class="d-flex gap-3">

				@foreach ($categories as $parentCategory)
					@if ($parentCategory->children->isEmpty())
						<li class="mainMenu-li">
							<a href="{{ route('front.products.index', ['category_id' => $parentCategory->id]) }}" class="mainMenu-item d-flex align-items-center gap-2">
								<span class="text-medium">{{ $parentCategory->title }}</span>
							</a>
						</li>
					@else
						<li class="mainMenu-li-hasSubMenu">
							<a href="{{ route('front.products.index', ['category_id' => $parentCategory->id]) }}" class="mainMenu-item-hasSubMenu d-flex align-items-center gap-1">
								<span class="text-medium">{{ $parentCategory->title }}</span>
								<i class="icon-angle-down icon-fs-small color-gray-700"></i>
							</a>
							<div class="subMenu w-p-100 position-absolute py-6 px-5 bg-white">
								<ul class="mainMenu-sublist w-p-100 h-p-100 d-flex flex-column flex-wrap">
									@foreach ($parentCategory->children as $childCategory)
										@if ($childCategory->children->isEmpty())
											<li class="mainMenu-sublist-item-main pb-1 mt-3 d-flex align-items-center gap-2">
												<span class="horizontal-divider h-5 bg-black"></span>
												<a 
													class="text-truncate text-medium" 
													href="{{ route('front.products.index', ['category_id' => $childCategory->id]) }}">
													{{ $childCategory->title }}
												</a>
											</li>
										@else
											<li class="d-flex flex-column">
												<div class="mainMenu-sublist-item-main pb-1 d-flex align-items-center gap-2">
													<span class="horizontal-divider h-5 bg-black"></span>
													<a 
														class="text-truncate text-medium" 
														href="{{ route('front.products.index', ['category_id' => $childCategory->id]) }}">
														{{ $childCategory->title }}
													</a>
													<i class="icon-angle-left icon-fs-medium pb-1"></i>
												</div>
												<ul class="d-flex flex-column">
													@foreach ($childCategory->children as $grandChildCategory)
														<li class="mainMenu-sublist-item me-3 text-medium">
															<a 
																class="text-truncate color-gray-700 text-medium" 
																href="{{ route('front.products.index', ['category_id' => $grandChildCategory->id]) }}">
																{{ $grandChildCategory->title }}
															</a>
														</li>
														<li class="mainMenu-sublist-item me-3 text-medium">
															<a 
																class="text-truncate color-gray-700 text-medium" 
																href="{{ route('front.products.index', ['category_id' => $grandChildCategory->id]) }}">
																{{ $grandChildCategory->title }}
															</a>
														</li>
													@endforeach
												</ul>
											</li>
										@endif
									@endforeach
								</ul>
							</div>
						</li>
					@endif
				@endforeach

				@foreach (count($menus) ? $menus['header'] : [] as $menuItem)
					@php
						$href = '/';
						// switch ($menuItem->linkable_type) {
						// 	case 'Modules\Category\Entities\Category':
						// 		$href = route('front.products', ['category_id' => $menuItem->linkable_id]);
						// 		break;
						// 	case 'Modules\Blog\Entities\Post':
						// 		$href = route('front.posts', ['id' => $menuItem->linkable_id]);
						// 		break;
						// 	case 'Custom\AboutUs':
						// 		$href = url('/about-us');
						// 		break;
						// 	case 'Custom\ContactUs':
						// 		$href = url('/contact');
						// 		break;
						// 	default:
						// 		$href = $menuItem->link;
						// 		break;
						// }
					@endphp
					<li class="mainMenu-li">
						<a href="{{ $href }}" class="mainMenu-item d-flex align-items-center gap-2">
							<span class="text-medium">{{ $menuItem->title }}</span>
						</a>
					</li>
				@endforeach

			</ul>
		</nav>

		<!-- Buttons -->
		<div class="d-flex align-items-center gap-1 mt-lg-0">

			<!-- Search -->
			<button type="button" data-modal="search" class="search">
				<i class="icon-search icon-fs-large"></i>
			</button>
			<span class="horizontal-divider d-none d-lg-block h-4 bg-gray-300 mx-2"></span>

			<!-- User -->
			@if ($user === null)
				<a href="./user-panel.html" class="d-none d-lg-block">
					<i class="icon-user icon-fs-large"></i>
				</a>
			@endif

			<!-- Favorite -->
			<a href="./user-panel.html" class="favorite d-none d-lg-block">
				<svg data-v-7caa044c="" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.761 20.8538C9.5904 19.5179 7.57111 17.9456 5.73929 16.1652C4.45144 14.8829 3.47101 13.3198 2.8731 11.5954C1.79714 8.25031 3.05393 4.42083 6.57112 3.28752C8.41961 2.69243 10.4384 3.03255 11.9961 4.20148C13.5543 3.03398 15.5725 2.69398 17.4211 3.28752C20.9383 4.42083 22.2041 8.25031 21.1281 11.5954C20.5302 13.3198 19.5498 14.8829 18.2619 16.1652C16.4301 17.9456 14.4108 19.5179 12.2402 20.8538L12.0051 21L11.761 20.8538Z" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.7393 7.05301C16.8046 7.39331 17.5615 8.34971 17.6561 9.47499" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
			</a>

			<!-- Shipping Cart -->
			<a href="./order.html" class="shopping-cart position-relative">
				<svg data-v-a42951fa="" width="27" height="27" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-a42951fa="" d="M15.7729 9.30504V6.27304C15.7729 4.18904 14.0839 2.50004 12.0009 2.50004C9.91691 2.49104 8.21991 4.17204 8.21091 6.25604V6.27304V9.30504" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path data-v-a42951fa="" fill-rule="evenodd" clip-rule="evenodd" d="M16.7422 21.0003H7.25778C4.90569 21.0003 3 19.0953 3 16.7453V11.2293C3 8.87933 4.90569 6.97433 7.25778 6.97433H16.7422C19.0943 6.97433 21 8.87933 21 11.2293V16.7453C21 19.0953 19.0943 21.0003 16.7422 21.0003Z" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
				<span class="position-absolute radius-circle text-center text-button-1 bg-primary-500 color-white w-4 h-4">2</span>
			</a>

			<!-- When Is Login -->
			@if ($user !== null)
				<button type="button" class="login-btn bg-black me-2 px-3 py-2 mb-1 position-relative radius-u">
					<div class="d-flex align-items-center color-white gap-1">
						<i class="icon-user icon-fs-medium-2"></i>
						<span class="text-button"> حساب کاربری</span>
						<i class="icon-caret-down icon-fs-xsmall"></i>
					</div>
					<div class="position-absolute flex-column bg-black px-3 gap-3 pb-2 pt-3">
						<a href="./user-panel.html" class="color-white text-button text-nowrap">مشاهده حساب کاربری</a>
						<span data-modal="exit" class="color-white text-button">خروج</span>
					</div>
				</button> 
			@endif

		</div>
  </div>
</header>