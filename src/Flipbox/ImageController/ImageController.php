<?php 

namespace Flipbox\ImageController;

use File;
use Response;
use Illuminate\Http\Request;
use Illuminate\Config\Repository;
use Intervention\Image\ImageCache;
use Illuminate\Routing\Controller;
use Intervention\Image\ImageManager;

class ImageController extends Controller
{
	/**
	 * config
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Create a new ImageController instance.
	 *
	 * @return void
	 */
	public function __construct(Repository $config)
	{
		$this->config = new Repository($config['image-controller']);
	}

	/**
	 * show image
	 *
	 * @param Request $request
	 * @return Image
	 */
	public function image(Request $request, $file, $ext='jpg')
	{
		$source = $this->getSource($file, $ext);
		$image = $this->makeImage($request, $source);
		$format = $this->config->get('output.format');
		$quality = $this->config->get('output.quality', 100);

		$response = Response::make($image->encode($format, $quality));
		$response->header('Content-Type', 'image/png');

		return $response;
	}

	/**
	 * get source
	 *
	 * @param string $file
	 * @param string $ext
	 * @return string
	 */
	protected function getSource($file, $ext)
	{
		$baseFolder = rtrim($this->config->get('folder', public_path('images')), '/');
		$source = $baseFolder . '/' . $file;	
		$matching = glob($source . ".*");

		if (count($matching) > 0) {
			$source = $matching[0];
		} else {
			$source = $this->getDefaultImage();
		}
		
		if (! in_array($ext, $this->config->get('extensions'))) {
			$source = $this->getDefaultImage();
		}

		return $source;
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

		return $manager->cache(function($image) use ($request, $source) {
			$image = $image->make($source);

			return $this->manipulateImage($image, $request);

		}, $this->config->get('cache_lifetime', 15), true);
	}

	/**
	 * manipulate image base request
	 *
	 * @param ImageCache $image
	 * @param Request $request
	 * @param string $size
	 * @return Image
	 */
	protected function manipulateImage(ImageCache $image, Request $request, $size=null)
	{
		if (!is_null($size)) {
			$size = $this->getImageSize($size);
			return $this->setReturnImage($image, $size);	
		}

		if ($request->has('size') AND $this->isValidSize($request->size)) {
			$size = $this->getImageSize($request->size);
			return $this->setReturnImage($image, $size);
		}

		if ($request->has('width') AND $request->has('height')) {
			return $this->setReturnImage($image, $request->width, $request->height, true);
		}

		if ($request->has('width') OR $request->has('height')) {
			return $this->setReturnImage($image, $request->width, $request->height);
		}

		return $this->manipulateImage($image, $request, $this->config->get('sizes.default', 'original'));
	}

	/**
	 * set return image
	 *
	 * @param ImageCache $image
	 * @param int $width
	 * @param int $height
	 * @return image
	 */
	protected function setReturnImage(ImageCache $image, $width=null, $height=null, $fit=false)
	{
		if (is_null($width) AND is_null($height)) {
			return $image;
		}

		if ($fit) {
			return $image->fit($width, $height, function($constraint){
				$constraint->aspectRatio();
			});
		}

		return $image->resize($width, $height, function($constraint){
			$constraint->aspectRatio();
		});
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
				return $this->config->get('sizes.thumbnail', 100);
				break;

			case 'small':
				return $this->config->get('sizes.small', 240);
				break;

			case 'medium':
				return $this->config->get('sizes.medium', 500);
				break;

			case 'large':
				return $this->config->get('sizes.large', 1024);
				break;

			default:
				return null;
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
		$defaultImage = $this->config->get('default_image');

		if (! is_null($defaultImage) AND File::exists($defaultImage)) {
			return $defaultImage;
		}

		return __DIR__.'/images/default.png';
	}

	/**
	 * check size is valid
	 *
	 * @param string $size
	 * @return bool
	 */
	protected function isValidSize($size)
	{
		return in_array($size, ['thumbnail', 'small', 'medium', 'large', 'original']);
	}
}
