<?php 

namespace Flipbox\ImageController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ImageControllerServiceProvider extends ServiceProvider
{
	/**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/image-controller.php' => config_path('image-controller.php'),
        ], 'config');
    }
	
	/**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/image-controller.php',
            'image-controller'
        );

        $config = $this->app->config['image-controller'];

        $this->appendRoute($config);

        $this->app->bind('image-controller', 'Flipbox\ImageController\ImageUploader');
    }

    /**
     * append route
     *
     * @param array $config
     * @return void
     */
    protected function appendRoute(array $config)
    {
    	$options = [
    		'namespace' => 'Flipbox\ImageController',
    		'prefix' => $config['prefix']
    	];

    	Route::group($options, function(){
    		Route::get('/{file}.{ext?}', 'ImageController@image')->where('file', '[A-Za-z0-9\/]+');
    		Route::get('/{file}', 'ImageController@image')->where('file', '[A-Za-z0-9\/]+');
    	});
    }
}
