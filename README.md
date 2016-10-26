# Image Controller
Controller your images for client request with size, quality, and extenstion with easy way. You no longer need to create an image with multiple sizes, this package is already handling request images with size needed,

## Features
* Dynamic images request file
* Controller image size
* Controller image quality
* Controller image extension

## Required
* php >= 5.6.0
* https://github.com/intervention/image ^2.3
* Laravel Framework 5.3.*

## Installation
Require this package with composer:
```
composer require flipboxstudio/image-controller
```
Add the ServiceProvider to the providers array in config/app.php
```
Flipbox\ImageController\ImageControllerServiceProvider::class,
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

`http://localhost/images/photo.jpg?size=thumbanil` default width 100px  
`http://localhost/images/photo.jpg?size=small` default width 240px  
`http://localhost/images/photo.jpg?size=medium` default width 500px  
`http://localhost/images/photo.jpg?size=large` default width 1024px  

### Request with specify width or heigt
Also you can request image with specify width or height or event both  
`http://localhost/images/photo.jpg?width=320` auto height  
`http://localhost/images/photo.jpg?height=320` auto width  
`http://localhost/images/photo.jpg?width=100&height=320` fixed width and height  

### Request with another extension
Real file extension will be ignored, now you can access your images file with extensions that defined in config or even with no extension  
`http://localhost/images/photo` valid by default  
`http://localhost/images/photo.jpg` valid by default  
`http://localhost/images/photo.png` valid by default  
`http://localhost/images/photo.jif` valid by default  

### ToDo
* Cache
* Add Watermark
* Images Api Uploader
* Test 