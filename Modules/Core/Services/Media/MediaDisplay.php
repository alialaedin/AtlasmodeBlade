<?php

namespace Modules\Core\Services\Media;

use Modules\Core\Entities\Media;

class MediaDisplay
{
    public function __construct(
        public int $id,
        public string $url,
        public array $conversions,
    ) {}


    public static function objectCreator(Media|null $media): MediaDisplay
    {
        // if the image wasn't exists in each reason we return empty string to error doesn't occur in Front
        // attention. the media object occurs when the row of this media doesn't exist in media table of database.
        if (!$media)
            return new MediaDisplay(0,'',[ 'lg' => '','md' => '','sm' => '' ],);


        $conversions = [];
        if (env('APP_ENV') == 'local') {
            return new MediaDisplay(
                $media->id,
                asset("localImage/variety-414.jpg"),
                [
                    'lg' => asset("localImage/conversions/variety-414-thumb_lg.jpg"),
                    'md' => asset("localImage/conversions/variety-414-thumb_md.jpg"),
                    'sm' => asset("localImage/conversions/variety-414-thumb_sm.jpg"),
                ]
            );
        }

        // this is not correct. we accept just some specific extensions
//        $extension = $media->extension === 'webp' ? 'jpg' : $media->extension;
        $extension = $media->extension;

        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $media->file_name);

        foreach (['lg', 'md', 'sm'] as $x) {
            $conversions[$x] = url(
                'storage/' . $media->uuid . '/conversions/'
                . $withoutExt. '-thumb_' . $x . '.' . $extension
            );
        }

        return new MediaDisplay(
            $media->id,
            $media->getUrl(),
            $conversions,
        );

    }

    public static function ckfinderImageConverter(string|null $ckEditorText):string|null
    {
        if (!$ckEditorText || $ckEditorText == '') return $ckEditorText;
        return preg_replace(
            '/src="\/[^"]+?([a-zA-Z0-9._-]+\.[a-zA-Z0-9]+)"/',
            'src="'. env('APP_URL') .'/storage/ckfinder/images/$1"',
            $ckEditorText
        );
    }

}
