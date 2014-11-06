<?php
/**
 * Thumbnails on the fly
 * Create thumbnails based on size
 * 
 * @author  Martin Kollerup <martin.kollerup@gmail.com>
 */

error_reporting(E_ALL);

defined ( '_JEXEC' ) or die;

class Thumbnails {

	/**
	* @var 	string 	Stores the class instance.
	*/
	private static $instance = null;

	public function __construct()
	{
		$this->thumbnail_path = 'images/thumbnail/';

		jimport('joomla.image.image');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
	}

	public static function instance() {
		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * [get description]
	 * @param  [type]  $src            [description]
	 * @param  [type]  $size           [description]
	 * @param  string  $type           [description]
	 * @param  [type]  $tags           [description]
	 * @param  integer $creationMethod [description]
	 * @return [type]                  [description]
	 */
	public function get($src, $size, $type = 'array', $tags = null, $creationMethod = 1){
		if(empty($src) || empty($size) || !is_array($size) || count($size) != 2)
			return false;

		$filename 		= JFile::getName($src);
		$fileExtension 	= JFile::getExt($filename);

		$folder = explode($filename,$src);

		$this->thumbnail_path = JURI::base( true ).'cache/thumb/'.$folder[0];

		$thumbFileName 	= str_replace('.' . $fileExtension, '_' . $size[0] .'x' . $size[1] . '.' . $fileExtension, $filename);

		//check if thumbnail folder and thumbnail file exits
		if(JFile::exists($this->thumbnail_path.$thumbFileName)){
			return self::comeback($this->thumbnail_path.$thumbFileName, $size, $type, $tags, 0);
		} else {
			if($this->createThumb($src,$size,$thumbFileName,$this->thumbnail_path,(int)$creationMethod)){
				self::comeback($thumbFileName, $size, $type, $tags, 1);
			} else {
				//fallback to src image
				self::comeback($src,$size,$type,$tags,0);
			}
		}
	}
	/**
	 * Create thumbnail based on size
	 * @param  string $src            Original image src
	 * @param  array  $size           Array(width,height)
	 * @param  string $thumbName      Filename for the new thumbnail
	 * @param  string $thumbPath      Thumbnail path - the thumbnail will be saved here
	 * @param  [type] $creationMethod Creation method. 4 will crop. The rest is resizing.
	 * @return bool                   True on succes else false
	 */
	public function createThumb($src,$size,$thumbName,$thumbPath,$creationMethod){
		
		if(empty($src) || empty($size) || !is_array($size) || count($size) != 2)
			return false;

		if (JFolder::exists(JPATH_BASE.'/'.$thumbPath) || JFolder::create(JPATH_BASE.'/'.$thumbPath)) {
			$sourceImage 	= new JImage($src);
			$imgProperties 	= JImage::getImageFileProperties($src);
			
			if ($creationMethod == 4) {
				$srcHeight = $sourceImage->getHeight();
				$srcWidth = $sourceImage->getWidth();
				// auto crop centered coordinates
				$left = round(($srcWidth - $size[0]) / 2);
				$top = round(($srcHeight - $size[1]) / 2);
				// crop image
				$thumb = $sourceImage->crop($size[0], $size[1], $left, $top, true);
			} else {
				// resize image
				$thumb = $sourceImage->resize($size[0], $size[1], true, $creationMethod);
			}

			$thumbFileName = $thumbPath . $thumbName;
			$thumb->toFile($thumbFileName, $imgProperties->type);
			
			if (JFile::exists($thumbFileName)) {
				return true;
			} else {
				if(JDEBUG){
					throw new Exception('Thumbnails:Thumbnail ('.$thumbFileName.') could not be created');
				}
				return false;
			}
		} else {
			if(JDEBUG){
				throw new Exception('Thumbnails:Could not create thumbnails folder');
			}
			return false;

		}
	}

	/**
	 * Return array
	 * @param  string $src  thumbnail size
	 * @param  array  $size array of size
	 * @return array       src, width and height
	 */
	public function comeback($src, $size, $type = 'array', $tags = null, $created = 0){
		switch ($type) {
			case 'html':
				$all_tags = null;
				if(is_array($tags)){

					jimport( 'joomla.filter.filterinput' );
					foreach ($tags as $tag => $tag_content) {
						if($tag_content)
							$all_tags .= $tag.'="'.strip_tags($tag_content).'" ';
					}
				}
				echo '<img width="'.$size[0].'" height="'.$size[1].'" src="'.$src.'" '.$all_tags.'/>';
				break;
			default:
				return array( 'src' => $src, 'width' => $size[0], 'height' => $size[1], 'created' => $created );
				break;
		} 
	}
}