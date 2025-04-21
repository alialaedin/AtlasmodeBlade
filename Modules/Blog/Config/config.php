<?php

use Modules\Blog\Entities\Post;

return [

	'name' => 'Blog',
	'orderCacheTime' => 6000,

	'statuses' => [
		Post::STATUS_PUBLISHED => 'انتشار یافته',
		Post::STATUS_DRAFT => 'پیش نویس',
		Post::STATUS_PENDING => 'در انتظار تایید',
		Post::STATUS_UNPUBLISHED => 'منتشر نشده',
	],

	'status_color' => [
		Post::STATUS_PUBLISHED => 'success',
		Post::STATUS_DRAFT => 'info',
		Post::STATUS_PENDING => 'warning',
		Post::STATUS_UNPUBLISHED => 'danger',
	]
];
