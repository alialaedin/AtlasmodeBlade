<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Menu\Entities\MenuGroup;
use Modules\Menu\Entities\MenuItem;
use Modules\Setting\Entities\Setting;
use Modules\Slider\Entities\Slider;

class AppServiceProvider extends ServiceProvider
{
	public static $routesAreCached = false;
	public static $configurationIsCached = false;
	public static $runningInConsole = true;
	private static $totalQueries;

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		static::$configurationIsCached = $this->app->configurationIsCached();
		static::$routesAreCached = $this->app->routesAreCached();
		static::$runningInConsole = $this->app->runningInConsole();
		if ($this->app->isLocal()) {
			//            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
		}
		$this->app->useLangPath(base_path('Modules/Core/Resources/lang')); // change lang path to Core Module
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Gate::before(function ($user, $ability) {
			return $user->hasRole('super_admin') ? true : null;
		});
		Paginator::defaultView('pagination.default');
		isset($_GET['kk']) &&
			DB::listen(function ($query) {
				//            \Log::debug($query->sql);
				//            \Log::debug($query->bindings);
				dump($query->sql);
				//            dump($query->time);
				dump($query->bindings);
				//            dump((debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 35)));
				if (!isset(static::$totalQueries)) {
					static::$totalQueries = 1;
				} else {
					static::$totalQueries += 1;
				}
				echo '<style>body {background: #1a202c}</style>';
				echo '<script>window.total =  ' . static::$totalQueries . ';window.onload = () => alert(window.total) </script>';
			});

		view()->composer('admin.layouts.master', function ($view) {

			$menuGroups = MenuGroup::getAllMenuGroups();
			$sliderGroups = Slider::getAllSliderGroups();
			$settingGroups = Setting::getGroups();
			$siteLogoUrl = Setting::where('name', 'logo')->first()->value;

			$view->with([
				'siteLogo' => $siteLogoUrl,
				'settingGroups' => $settingGroups,
				'menuGroups' => $menuGroups,
				'sliderGroups' => $sliderGroups,
			]);

		});

		view()->composer('front-layouts.master', function ($view) {

			$settings = Helpers::cacheForever('settings', function () {
				return Setting::query()->where('private', false)->get();
			});

			$menus = MenuItem::getMenusForFront();
			$categories = Category::getCategoriesForFront();
			$user = Auth::guard('customer')->user();
			$cartsCount = $user?->carts->count() ?? 0;
			
			$view->with([
				'settings' => $settings,
				'menus' => $menus,
				'categories' => $categories,
				'user' => $user,
				'cartsCount' => $cartsCount
			]);
		});
	}
}
