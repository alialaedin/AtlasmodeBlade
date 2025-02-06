<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;

class MediaResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request
   * @return array
   */
  public function toArray($request)
  {
    if (env('APP_ENV') == 'local') {
      return [
        'id' => $this->id,
        'url' => asset("localImage/variety-414.jpg"),
        'conversions' => [
          'lg' => asset("localImage/conversions/variety-414-thumb_lg.jpg"),
          'md' => asset("localImage/conversions/variety-414-thumb_md.jpg"),
          'sm' => asset("localImage/conversions/variety-414-thumb_sm.jpg"),
        ],
      ];
    }

    try {
      $this->getExtensionAttribute();
    } catch (\Error $exception) {
      // یعنی عکس نداره
      return null;
    }
    if (in_array($this->getExtensionAttribute(), ['docx', 'doc', 'ppt', 'txt', 'pptx', 'ppt'])) {
      $type = 'document';
    } else if (in_array($this->getExtensionAttribute(), ['zip', 'rar'])) {
      $type = 'archive';
    } else {
      $type = $this->type;
    }
    $conversions = [];
    $extension = $this->extension === 'webp' ? 'jpg' : $this->extension;
    if (in_array($this->model_type, [Product::class, Variety::class])) {
      $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->file_name);

      foreach (['lg', 'md', 'sm'] as $x) {
        $conversions[$x] = url(
          'storage/' . $this->uuid . '/conversions/'
            . $withoutExt . '-thumb_' . $x . '.' . $extension
        );
      }
    }

    return [
      'id' => $this->id,
      'type' => $type,
      'name' => $this->file_name,
      'url' => $this->getUrl(),
      'conversions' => $conversions,
      //            'srcset' => $this->getSrcSetArray(),
      //            'placeholder' => $this->getSvgPlaceholder()
    ];
  }
}
