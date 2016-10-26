<?php 

return [
	/*
	|--------------------------------------------------------------------------
	| Images folder
	|--------------------------------------------------------------------------
	| define location of images source
	*/

	'folder' => public_path('images'),

	/*
	|--------------------------------------------------------------------------
	| Url prefix
	|--------------------------------------------------------------------------
	| prefix url for get image
	| example: http://example.com/imagas/filename.jpg <- you can override
	*/
	
	'prefix' => 'images',

	/*
	|--------------------------------------------------------------------------
	| output
	|--------------------------------------------------------------------------
	| output for request image, you can override format, quality, etc
	| for rendering image response
	*/

	'output' => [
		//Define the encoding format from one of the following formats:
		'format' => 'jpg', //value is jpg|png|gif
		
		'quality' => 100, //value is between 0-100
	],

	/*
	|--------------------------------------------------------------------------
	| sizes
	|--------------------------------------------------------------------------
	| size image that used for rendering request with size
	| for example http://example.com/images/image?size=thumbnail
	*/

	'sizes' => [
		'thumbnail' => 100,
		'small' => 240,
		'medium' => 500,
		'large' => 1024,
	],

	/*
	|--------------------------------------------------------------------------
	| default image
	|--------------------------------------------------------------------------
	| if filename of request image not found, we will use this image,
	| however we provide good default image if you set value to null
	*/

	'default_image' => null,

	/*
	|--------------------------------------------------------------------------
	| extensions
	|--------------------------------------------------------------------------
	| available extension for request image
	| you can access your image file with extension that you define here
	| real file extension will be ignored
	*/

	'extensions' => ['jpg','png','gif','tif','bmp'],

	/*
	|--------------------------------------------------------------------------
	| cache
	|--------------------------------------------------------------------------
	| image cache lifetime
	*/

	'cache_lifetime' => 20, //under minutes
];
