<?php

namespace Modules\Specification\Entities;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Traits\HasAuthors;

class Specification extends BaseModel
{
  use HasAuthors, EagerLoadPivotTrait;

  public const TYPE_TEXT = 'text';
  public const TYPE_SELECT = 'select';
  public const TYPE_MULTI_SELECT = 'multi_select';

  protected $fillable = [
    'name',
    'label',
    'group',
    'type',
    'required',
    'public',
    'show_filter',
    'status',
  ];

  public function scopeActive($query)
  {
    $query->where('status', true);
  }

  public static function getAvailableTypes()
  {
    return [static::TYPE_TEXT, static::TYPE_SELECT, static::TYPE_MULTI_SELECT];
  }

  public function values()
  {
    return $this->hasMany(SpecificationValue::class, 'specification_id');
  }

  public function getTypeLabelAttribute()
  {
    return $this->getAvailableTypeLabels()[$this->type];
  }

  private function getAvailableTypeLabels()
  {
    return [
      static::TYPE_TEXT => 'متنی',
      static::TYPE_SELECT => 'انتخابی (تک مقدار)',
      static::TYPE_MULTI_SELECT => 'انتخابی (چند مقدار)',
    ];
  }

  public function scopeFilters($query)
  {
    return $query
      ->when(request('name'), fn($q) => $q->where('name', 'LIKE', '%' . request('name') . '%'))
      ->when(request('label'), fn($q) => $q->where('label', 'LIKE', '%' . request('label') . '%'))
      ->when(request('group'), fn($q) => $q->where('group', 'LIKE', '%' . request('group') . '%'))
      ->when(!is_null(request('public')), fn($q) => $q->where('public', request('public')))
      ->when(!is_null(request('required')), fn($q) => $q->where('required', request('required')))
      ->when(!is_null(request('status')), fn($q) => $q->where('status', request('status')))
      ->when(request('start_date'), fn($q) => $q->whereDate('created_at', '>=', request('start_date')))
      ->when(request('end_date'), fn($q) => $q->whereDate('created_at', '<=', request('end_date')));
  }
}
