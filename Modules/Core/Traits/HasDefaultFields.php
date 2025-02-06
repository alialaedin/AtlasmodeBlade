<?php

namespace Modules\Core\Traits;

/**
 * @property array $defaults
 */
trait HasDefaultFields
{
    public function initializeHasDefaultFields()
    {
        if (isset($this->defaults)) {
            $defaults = $this->defaults;
            static::creating(function ($model) use ($defaults) {
                foreach ($defaults as $key => $value) {
                    $model->{$key} = $model->{$key} ?? $value;
                }
            });
        }
    }
}
