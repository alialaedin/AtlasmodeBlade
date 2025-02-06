<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\InvalidBase64Data;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\FileAdderFactory;
use Illuminate\Http\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMedia
{
    use \Spatie\MediaLibrary\InteractsWithMedia;

    protected bool $mediaEventsFired = false;

    public bool $preserveOriginal = false;

    public static function bootInteractsWithMedia()
    {
        static::deleted(function (HasMedia $model) {
            if ($model->shouldDeletePreservingMedia()) {
                return;
            }

            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                if (!$model->forceDeleting) {
                    return;
                }
            }

            $model->media()->cursor()->each(fn (Media $media) => $media->delete());
        });
    }

    public function addMedia($file): FileAdder
    {
        $this->fireMediaEvents();
        $fileAdder =  app(FileAdderFactory::class)->create($this, $file);
        if ($this->preserveOriginal){
            $fileAdder->preservingOriginal();
        }

        return $fileAdder;
    }

    public function addMediaFromBase64(string $base64data, ...$allowedMimeTypes): FileAdder
    {
        $this->fireMediaEvents();
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            [$_, $base64data] = explode(';', $base64data);
            [$_, $base64data] = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            throw InvalidBase64Data::create();
        }

        // decoding and then reencoding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            throw InvalidBase64Data::create();
        }

        $binaryData = base64_decode($base64data);

        // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
        $tmpFile = tempnam(sys_get_temp_dir(), 'media-library');
        file_put_contents($tmpFile, $binaryData);

        $this->guardAgainstInvalidMimeType($tmpFile, $allowedMimeTypes);

        $file = app(FileAdderFactory::class)->create($this, $tmpFile);

        $extension = (new File($tmpFile))->extension();
        $name = $this->getMediaName();

        return $file
            ->usingFileName($name . '.' . $extension)->usingName($name);
    }

    public function getMediaName()
    {
        return mb_strtolower(class_basename(static::class) . '-' . $this->getKey());
    }

    public function registerMediaConversions(Media $media = null): void
    {
        foreach (['lg' => 400, 'md' => 200, 'sm' => 50] as $name => $width) {
            $this->addMediaConversion('thumb_' . $name)
                ->width($width)->keepOriginalImageFormat();
        }
    }

    public function fireMediaEvents()
    {
        if ($this->mediaEventsFired){
            return;
        }
        $this->mediaEventsFired = true;
        if (isset($this->attributes['id']) && $this->attributes['id']) {
            $this->fireModelEvent('updated');
        } else {
            $this->fireModelEvent('created');
        }
    }
}
