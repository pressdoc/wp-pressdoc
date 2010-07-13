<?php

/**
 * Plugin Name: PressDoc
 * Plugin URI: http://pressdoc.com
 * Description: Publish social media press releases from PressDoc directly to your blog.
 * Author: Stefan Borsje - PressDoc B.V.
 * Version: 0.1
 * Author URI: http://pressdoc.com
 */

  define('PRESSDOC', '0.1');

  include('php' . DIRECTORY_SEPARATOR . 'pressdoc-importer.php');
  include('php' . DIRECTORY_SEPARATOR . 'pressdoc-api.php');

?>
