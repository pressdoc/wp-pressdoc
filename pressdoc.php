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

  include('pressdoc-news-feed-importer.php');
  include('php' . DIRECTORY_SEPARATOR . 'pressdoc-api.php');

  //function PressDoc_settings_page() {
  //}

  //function PressDoc_add_settings_pages() {
  //  global $wpdb;

  //  if (function_exists('add_submenu_page')) {
  //    add_submenu_page('plugins.php', __('PressDoc Configuration'), __('PressDoc Configuration'), 'manage_options', __FILE__, 'PressDoc_settings_page');
  //  }
  //}

  //add_action('admin_menu', 'PressDoc_add_settings_pages');
?>
