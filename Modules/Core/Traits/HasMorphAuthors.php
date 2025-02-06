<?php

namespace Modules\Core\Traits;

trait HasMorphAuthors
{
    use HasAuthors;

    public function creator()
    {
        return $this->morphTo('creatorable');
    }

    public function updater()
    {
        return $this->morphTo('updaterable');
    }
}
