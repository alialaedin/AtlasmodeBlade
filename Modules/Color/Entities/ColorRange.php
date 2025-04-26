<?php

namespace Modules\Color\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Modules\Product\Entities\Variety;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;

class ColorRange extends Model implements Sortable, HasMedia
{
	use SortableTrait, InteractsWithMedia;

	protected $fillable = ['title', 'status', 'description', 'order'];
	protected $appends = ['logo'];
	protected $hidden = ['media'];
	public $sortable = [
		'order_column_name' => 'order',
		'sort_when_creating' => true,
	];

	private const ADMIN_COLOR_RANGES_CACHE_KEY = 'adminColorRanges';
	private const FRONT_COLOR_RANGES_CACHE_KEY = 'frontColorRanges';

	protected static function booted()
	{
		static::deleted(fn (self $colorRange) => ActivityLogHelper::deletedModel('طیف رنکی حذف شد', $colorRange));
		static::deleting(function (self $colorRange) {
			if ($colorRange->varieties->isNotEmpty()) {
				throw new ModelCannotBeDeletedException('این طیف به تنوعی اختصاص داده شده و قابل حذف نمی باشد');
			}
		});

		Helpers::clearCacheInBooted(self::class, self::ADMIN_COLOR_RANGES_CACHE_KEY);
		Helpers::clearCacheInBooted(self::class, self::FRONT_COLOR_RANGES_CACHE_KEY);
	}

	public static function getColorRangesForAdmin()
	{
		return Cache::rememberForever(self::ADMIN_COLOR_RANGES_CACHE_KEY, function () {
			return self::orderByDesc('order')->get();
		});
	}

	public static function getColorRangesForFront()
	{
		return Cache::rememberForever(self::FRONT_COLOR_RANGES_CACHE_KEY, function () {
			return self::orderByDesc('order')->where('status', 1)->get();
		});
	}

	public static function sort(Request $request)
	{
		$order = 99;
		foreach ($request->input('color_range_ids') as $colorRangeId) {
			$model = self::find($colorRangeId);
			if (!$model) continue;
			$model->order = $order--;
			$model->save();
		}
	}

	public static function storeOrUpdate(Request $request, self|null $colorRange = null)
	{
		if ($colorRange) {
			$colorRange->update($request->all());
			ActivityLogHelper::updatedModel(' طیف رنگی ویرایش شد', $colorRange);
		} else {
			$colorRange = self::query()->create($request->all());
			ActivityLogHelper::storeModel(' طیف رنگی ثبت شد', $colorRange);
		}

		if ($request->hasFile('logo')) {
			$colorRange->clearMediaCollection('logo');
			$colorRange->addLogo($request->file('logo'));
		}
	}

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('logo')->singleFile();
	}

	public function addLogo($file)
	{
		return $this->addMedia($file)->toMediaCollection('logo');
	}

	public function getLogoAttribute(): ?MediaResource
	{
		$media = $this->getFirstMedia('logo');
		return !$media ? null : new MediaResource($media);
	}

	public function varieties()
	{
		return $this->belongsToMany(Variety::class);
	}
}
