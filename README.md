# Image Controller
Controller your images for client request with size, quality, and extenstion with easy way. You no longer need to create an image with multiple sizes, this package is already handling request images with size needed,

## Features
* Dynamic images request file
* Controller image size
* Controller image quality
* Controller image extension
* Cache image

## Required
* php >= 5.6.4
* laravel/framework 5.3.*
* intervention/image ^2.3
* intervention/imagecache ^2.3

## Installation
Require this package with composer:
```
composer require flipbox/image-controller
```
Add the ServiceProvider to the providers array in config/app.php
```
Flipbox\ImageController\ImageControllerServiceProvider::class,
```
Add the facade of this package to the $aliases array config/app.php
```
'ImageController' => Flipbox\ImageController\Facade::class
```
Copy the package resource to your application with the publish command:
```
php artisan vendor:publish
```
your image ready to control :-)

## Using package
To avoid clashed request, we suggest you to add a little code to the end of your `.htaccess` file in public laravel folder
```
RewriteRule .*\.(jpg|png|gif|tif|bmp)$ index.php [NC,L]
```
create folder `images` to your public folder (however you can change name of folder in config file), and put image to that folder, for example you put image with file name a `photo.jpg`, and you can access your photo as usual `http://localhost/images/photo.jpg`.

### Request with size
Now you can request image with specify size (`thumbnail`,`small`,`medium`,`large`)

`http://localhost/images/photo.jpg?size=thumbnail` default width 100px  
`http://localhost/images/photo.jpg?size=small` default width 240px  
`http://localhost/images/photo.jpg?size=medium` default width 500px  
`http://localhost/images/photo.jpg?size=large` default width 1024px  

### Request with specify width or height
Also you can request image with specify width or height or event both  
`http://localhost/images/photo.jpg?width=320` auto height  
`http://localhost/images/photo.jpg?height=320` auto width  
`http://localhost/images/photo.jpg?width=100&height=320` fixed width and height  

### Request with another extension
Real file extension will be ignored, now you can access your images file with extensions that defined in config or even with no extension  
`http://localhost/images/photo` valid by default  
`http://localhost/images/photo.jpg` valid by default  
`http://localhost/images/photo.png` valid by default  
`http://localhost/images/photo.gif` valid by default

### Uploaded file
We provide uploader file that make upload file very easy. First parameter is [UploadedFile](https://laravel.com/api/5.2/Illuminate/Http/UploadedFile.html) or [Base64 Encoding](https://en.wikipedia.org/wiki/Base64), and second parameter is `directory/prefix.`
```
ImageController::upload($request->file, 'profile');
```
this method will be return string of generated filename `str_random(34)`

### Model Assesor
Add [model accesor](https://laravel.com/docs/5.2/eloquent-mutators#accessors-and-mutators) to generate image link.
```
	/**
	 * Get ProfilePicture.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function getProfilePictureAttribute($value)
	{
		return ImageController::generateImageUrl($value, 'small');
	}

```

### ToDo
* Add Watermark
* Test 
