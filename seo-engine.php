<?php
/*
Plugin Name: SEO Engine
Plugin URI: https://themeegg.com/downloads/seo-engine/
Description: Automatically adds alt and title attributes to all your images. Improves traffic from search results and makes them W3C/xHTML valid as well.
Version: 1.0.0
Author: ThemeEgg
Author URI: https://themeegg.com
Tested up to: 4.9.1
Text Domain: seo-engine
Domain Path: /i18n/languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define SEN_PLUGIN_FILE.
if ( ! defined( 'SEN_PLUGIN_FILE' ) ) {
	define( 'SEN_PLUGIN_FILE', __FILE__ );
}

// Include the main WooCommerce class.
if ( ! class_exists( 'SEO_Engine' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-seo-engine.php';
}

/**
 * Main instance of SEO Engine.
 *
 * Returns the main instance of SEN to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WooCommerce
 */
function sen() {
	return SEO_Engine::instance();
}

// Global for backwards compatibility.
$GLOBALS['seo-engine'] = sen();

//require_once( dirname( __FILE__ ) . '/includes/class-sen-images-engine.php' );
//$seo_engine = new SEN_Images_Engine();

?>
