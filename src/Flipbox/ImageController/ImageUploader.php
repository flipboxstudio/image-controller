<?php 

namespace Flipbox\ImageController;

use URL;
use File;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Config\Repository;
use Intervention\Image\ImageManager;

class ImageUploader
{
	/**
	 * temporary folder name
	 *
	 * @var string
	 */
	protected $temporaryPath = 'temporary';

	/**
	 * temporary image file
	 *
	 * @var string
	 */
	protected $temporaryImage = null;

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
	 * upload image
	 *
	 * @param mixed $file
	 * @return string
	 */
	public function upload($file, $path='')
	{
		$path = ltrim($path, '/');
		$path = rtrim($path, '/');
		$basePath = $this->getImageBasePath();
		$imagePath = $basePath.'/'.$path;

		if (!File::exists($imagePath)) {
			throw new ImageUploaderException("Directory {$path} doesn't exists");
		}

		$uploadedFile = $this->getUploadedFile($file);
		$fileName = $this->generateFileName($uploadedFile->getClientOriginalExtension());
		$uploadedFile->move($imagePath, $fileName);

		if (!is_null($this->temporaryImage)) {
			File::delete($this->temporaryImage);
		}

		return $path.'/'.$fileName;
	}

	/**
	 * get uploaded file
	 *
	 * @param mixed $file
	 * @return UploadedFile
	 */
	protected function getUploadedFile($file)
	{
		if ($file instanceof UploadedFile) {
			return $file;
		}

		return $this->createUploadedFile($file);
	}

	/**
	 * create uploaded file
	 *
	 * @param string $file
	 * @return UploadedFile
	 */
	protected function createUploadedFile($file)
	{
		try {
			$imageManager = new ImageManager;
			$image = $imageManager->make($file);
			$extension = $this->getExtensionFromMime($image->mime());
			$fileName = $this->generateFileName($extension);
			$temporaryPath = $this->getTemporaryImageBasePath();
			$this->temporaryImage = $temporaryPath.'/'.$fileName;
			$image->save($this->temporaryImage);

			return new UploadedFile(
				$this->temporaryImage,
				$fileName, 
				$image->mime(),
				$image->filesize(),
				null,
				TRUE
			);
		} catch (Exception $e) {
			throw new ImageUploaderException("Error create uploaded file");
		}
	}

	/**
	 * generate temporary filename
	 *
	 * @param string $extension
	 * @return string
	 */
	protected function generateFileName($extension='jpg')
	{
		return str_random(34).'.'.$extension;
	}

	/**
	 * get extension form mime
	 *
	 * @param string $mime
	 * @return string
	 */
	protected function getExtensionFromMime($mime)
	{
		switch ($mime) {
			case 'image/png':
				return 'png';
				break;

			case 'image/gif':
				return 'png';
				break;

			default:
				return 'jpg';
				break;
		}
	}

	/**
	 * get image base path
	 *
	 * @return string
	 */
	protected function getImageBasePath()
	{
		return rtrim($this->config->get('folder', public_path('images')), '/');
	}

	/**
	 * get temporary image base path
	 *
	 * @return string
	 */
	protected function getTemporaryImageBasePath()
	{
		$basePath = $this->getImageBasePath();

		$temporaryPath = $basePath . '/'. $this->temporaryPath;

		if (!File::exists($temporaryPath)) {
			File::makeDirectory($temporaryPath, 0755, true);
		}

		return $temporaryPath;
	}

	/**
	 * generate image file url
	 *
	 * @param string $file, $size
	 * @return string
	 */
	public function generateImageUrl($file, $size=null)
	{
		$url = $this->config->get('prefix').'/'.$file;

		if (!is_null($size)) {
			$url .= '?size='.$size;
		}

		return URL::to($url);
	}
}
