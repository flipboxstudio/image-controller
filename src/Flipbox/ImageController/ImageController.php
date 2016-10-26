<?php 

namespace Flipbox\ImageController;

use File;
use Config;
use Response;
use Illuminate\Http\Request;
use Intervention\Image\ImageCache;
use Illuminate\Routing\Controller;
use Intervention\Image\ImageManager;

class ImageController extends Controller
{
	/**
	 * show image
	 *
	 * @param Request $request
	 * @return Image
	 */
	public function image(Request $request, $file, $ext='jpg')
	{
		$source = Config::get('image-controller.folder', public_path('images')) . '/' . $file;

		$matching = glob($source . ".*");

		if (count($matching) > 0) {
			$source = $matching[0];
		} else {
			$source = $this->getDefaultImage();
		}
		
		if (! in_array($ext, Config::get('image-controller.extensions'))) {
			$source = $this->getDefaultImage();
		}

		$image = $this->makeImage($request, $source);

		$format = Config::get('image-controller.output.format');
		$quality = Config::get('image-controller.output.quality', 100);

		$response = Response::make($image->encode($format, $quality));	

		$response->header('Content-Type', 'image/png');

		return $response;
	}

	/**
	 * make image from request
	 *
	 * @param Request $request
	 * @param string $source
	 * @return Image
	 */
	protected function makeImage(Request $request, $source)
	{
		$manager = new ImageManager();

		$img = $manager->cache(function($image) use ($request, $source) {
			$image = $image->make($source);

			return $this->manipulateImage($request, $image);

		}, Config::get('image-controller.cache_lifetime', 15), true);

		return $img;
	}

	/**
	 * manipulate image base request
	 *
	 * @param Request $request
	 * @param ImageCache $image
	 * @return Image
	 */
	protected function manipulateImage(Request $request, ImageCache $image)
	{
		if ($request->has('size') AND 
			array_key_exists($request->get('size'), Config::get('image-controller.sizes')))
		{
			$size = $this->getImageSize($request->get('size'));
			
			return $image->resize($size, null, function ($constraint) {
			    $constraint->aspectRatio();
			});
		}

		if ($request->has('width') AND $request->has('height')) {
			return $image->fit($request->get('width'), $request->get('height'));
		}
		
		if ($request->has('width')) {
			return $image->resize($request->get('width'), null, function($constraint){
				$constraint->aspectRatio();
			});
		}

		if ($request->has('height')) {
			return $image->resize(null, $request->get('height'), function($constraint){
				$constraint->aspectRatio();
			});
		}
	}

	/**
	 * get image size
	 *
	 * @param string $size
	 * @return integer
	 */
	protected function getImageSize($size)
	{
		switch ($size) {
			case 'thumbnail':
				return Config::get('image-controller.sizes.thumbnail', 100);
				break;

			case 'small':
				return Config::get('image-controller.sizes.small', 240);
				break;

			case 'medium':
				return Config::get('image-controller.sizes.medium', 500);
				break;

			case 'large':
				return Config::get('image-controller.sizes.large', 1024);
				break;
		}
	}

	/**
	 * get default image
	 *
	 * @return string
	 */
	protected function getDefaultImage()
	{
		$defaultImage = Config::get('image-controller.default_image');

		if (! is_null($defaultImage) AND File::exists($defaultImage)) {
			return $defaultImage;
		}

		return __DIR__.'/images/default.png';
	}
}
