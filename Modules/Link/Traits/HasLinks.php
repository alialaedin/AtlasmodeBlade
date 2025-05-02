<?php


namespace Modules\Link\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Contact\Entities\Contact;

trait HasLinks
{
	public function initializeHasLinks()
	{
		$this->mergeFillable(['link', 'linkable_id', 'linkable_type', 'new_tab']);
		$this->append('slug');
		$this->makeHidden('linkable');
	}

	public function setLink($link)
	{
		if ($link instanceof Model) {
			$this->linkable()->associate($link);
		} elseif (is_string($link)) {
			$this->link = $link;
		}
	}

	public function linkable()
	{
		return $this->morphTo('linkable');
	}

	public function getSlugAttribute()
	{
		return Cache::remember('linkable-model-' . $this->id, 600, function () {
			if (!class_exists($this->linkable_type) || !$this->linkable) {
				return '';
			}
			return $this->linkable->slug;
		});
	}

	public function getLinkUrlAttribute()
  {
    switch ($this->unique_type) {
      case 'IndexPost':
        return route('front.posts.index');
      case 'Post':
        return route('front.posts.show', $this->linkable_id);
      case 'IndexProduct':
        return route('front.products.index');
      case 'Product':
        return route('front.products.show', $this->linkable_id);
      case 'Category':
        return route('front.products.index', ['category_id' => $this->linkable_id]);
      case 'IndexAboutUs':
        return Contact::ABOUT_URL;
      case 'IndexContactUs':
        return Contact::CONTACT_URL;
      default:
        return $this->link;
    }
  } 

	public function getUniqueTypeAttribute()
  {
    if (!$this->linkable_type) {
      return 'self_link';
    }
    if ($this->linkable_id) {
      return basename($this->linkable_type);
    } else {
      if (Str::contains($this->linkable_type, 'Custom')) {
        return 'Index' . explode('\\', $this->linkable_type)[1];
      }else {
        return 'Index' . basename($this->linkable_type);
      }
    }
  }
}
