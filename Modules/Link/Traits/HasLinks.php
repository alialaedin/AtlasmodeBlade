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
		$this->append(['slug', 'link_url', 'unique_type']);
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
      case 'IndexModules\Blog\Entities\Post':
        return route('front.posts.index');
      case 'Modules\Blog\Entities\Post':
        return route('front.posts.show', $this->linkable_id);
      case 'IndexModules\Product\Entities\Product':
        return route('front.products.index');
      case 'Modules\Product\Entities\Product':
        return route('front.products.show', $this->linkable_id);
      case 'Modules\Category\Entities\Category':
        return route('front.products.index', ['category_id' => $this->linkable_id]);
      case 'IndexCustom\AboutUs':
        return Contact::ABOUT_URL;
      case 'IndexCustom\ContactUs':
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
        return 'Index' . $this->linkable_type;
      }else {
        return 'Index' . basename($this->linkable_type);
      }
    }
  }
}
