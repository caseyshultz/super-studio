<?php
/*
Site config
*/
define('SITE_DIR', "/"); // "/" or "sp1" or "player/one"
/*
Database config is done in the following file.
*/
require_once('php/mysql.php');
/*
Deeper site config
*/
$GLOBALS['sysmsg']=array();
define('SERVER_REQUEST',$_SERVER['REQUEST_URI']); // "/sp1/scrap.php?some=stuff"
$l=1; // The following function cannot take a reference for this.
if(SITE_DIR=="/"){
  define('SITE_REQUEST',str_replace('/','',SERVER_REQUEST,$l)); // "/scrap.php?some=stuff"
  define('SITE_PATH',$_SERVER['DOCUMENT_ROOT']); // "/var/www" or "/var/www/sp1/webroot"
  define('SITE_ROOT',SITE_PATH); // "/var/www" or "/var/www/sp1/webroot"
  define('SITE_HOST',$_SERVER['HTTP_HOST']); // "localhost" or "studioplayer.com"
  define('SITE_HOME',SITE_DIR); // "/"
  /*
  Audio files config
  The "audio" directory needs to be directly under the site root.
  */
  define('AUDIO_PATH',SITE_PATH."/audio/"); // "/var/www/sp1/audio/"
  define('AUDIO_DIR',"/audio/"); // "/sp1/audio/"
  define("AUDIO_FORMATS","audio/mpeg,audio/wav,audio/wave,audio/x-wav,audio/x-pn-wav,audio/ogg");
}
else{ // example SITE_DIR is "sp1"
  define('SITE_REQUEST',str_replace('/'.SITE_DIR,'',SERVER_REQUEST,$l)); // "/scrap.php?some=stuff"
  define('SITE_PATH',$_SERVER['DOCUMENT_ROOT']); // "/var/www"
  define('SITE_ROOT',SITE_PATH.'/'.SITE_REQUEST); // "/var/www/sp1"
  define('SITE_HOST',$_SERVER['HTTP_HOST']); // "localhost" or "studioplayer.com"
  define('SITE_HOME',str_replace(SITE_PATH,'',SITE_ROOT,$l).'/'); // /sp1/
  /*
  Audio files config
  The "audio" directory needs to be directly under the site root.
  */
  define('AUDIO_PATH',SITE_PATH.'/'.SITE_DIR."/audio/"); // "/var/www/sp1/audio/"
  define('AUDIO_DIR','/'.SITE_DIR.'/'."audio/"); // "/sp1/audio/"
  define("AUDIO_FORMATS","audio/mpeg,audio/wav,audio/wave,audio/x-wav,audio/x-pn-wav,audio/ogg");
}

/*
File size settings
*/
$max_upload = (int)(ini_get('upload_max_filesize'));
$max_post = (int)(ini_get('post_max_size'));
$memory_limit = (int)(ini_get('memory_limit'));
$upload_mb = min($max_upload, $max_post, $memory_limit)."MB";
if(!$upload_mb || $upload_mb == 0){
  $GLOBALS['sysmsg'][]="Maximum upload size is NOT defined.";
}

/*
Bring in the functions
*/
require_once('php/process.php');
require_once('php/makeHTML.php');
require_once('php/sysmsg.php');
?>
