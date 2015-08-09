Joomla thumbnails
=================

Libary for Joomla to create thumbnails

Usage
================
```PHP
jimport('thumbnail.thumbnail');
$thumb = Thumbnails::instance();

$thumb->get( $image->{'image_intro'}, array(370,240), 'html', array( 'alt'=>$image->{'image_intro_alt'}, '{any-attribute}' => '{any_value}' ) );
OUTPUT: <img src="image_intro_370x240.png" width="370" height="240" alt="image_intro_aalt" any-attribute="any_value">

$thumb->get( $image->{'image_intro'}, array(370,240), 'array', array( 'alt'=>$image->{'image_intro_alt'}, '{any-attribute}' => '{any_value}' ) );
OUTPUT: array( 'src' => $src, 'width' => $size[0], 'height' => $size[1], 'created' => $created );
```

Next release
================
[] Support for Yahoo Image compression. 

License
=================
GNU/GPL v.3 or any newer. Please see LICENSE