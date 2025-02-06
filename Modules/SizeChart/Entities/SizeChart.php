<?php

namespace Modules\SizeChart\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Classes\CoreSettings;
use Modules\Product\Entities\Product;
use Modules\SizeChart\Database\factories\SizeChartFactory;
use Modules\SizeChart\Entities\SizeChartType;

class SizeChart extends Model
{
  protected $fillable = ['title', 'chart'];

  //    public static function booted()
  //    {
  //        static::deleting(function (){
  //            if ($this->products->exists()){
  //                throw  Helpers::makeValidationException('سایز چارت دارای محصول است');
  //                //TODO
  //            }
  //        });
  //
  //    }

  public static function storeSizeCharts($sizeChartRequest, $product)
  {
    SizeChart::query()->where('product_id', $product->id)->delete();
    foreach ($sizeChartRequest as $sizeChart) {
      static::storeSizeChart($sizeChart, $product);
    }
  }

  public static function storeSizeChart($sizeChart, $product)
  {
    $sizeChartModel = new SizeChart([
      'title' => $sizeChart['title'],
      'chart' => is_string($sizeChart['chart']) ? $sizeChart['chart'] : json_encode($sizeChart['chart']),
    ]);
    if (app(CoreSettings::class)->get('size_chart.type')) {
      $sizeChartModel->type()->associate($sizeChart['type_id']);
    }
    $sizeChartModel->product()->associate($product);
    $sizeChartModel->save();
  }

  public function setType($type_id, $sizeChartModel)
  {
    if (!$type_id) return;

    $type = SizeChartType::query()->find('type_id');
    $sizeChartModel->type()->associate($type);
  }

  protected static function newFactory()
  {
    return SizeChartFactory::new();
  }

  public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function setChartAttribute($value)
  {
    $this->attributes['chart'] = json_encode($value);
  }

  public function getChartAttribute($chart)
  {
    return json_decode($chart);
  }

  public function type()
  {
    return $this->belongsTo(SizeChartType::class, 'type_id');
  }

  public function scopeFilters($query)
  {
    return $query; 
  }
}
