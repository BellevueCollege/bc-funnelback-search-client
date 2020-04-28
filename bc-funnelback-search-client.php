<?php
/*
Plugin Name: Funnelback Search Client
Plugin URI: https://github.com/BellevueCollege/bc-funnelback-search-client
Description: Funnelback search client for BC Website
Author: Bellevue College Integration Team
Version: 0.0.0-a2
Author URI: http://www.bellevuecollege.edu
GitHub Plugin URI: BellevueCollege/bc-funnelback-search-client
Text Domain: bcfunnelback
*/

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

require_once('classes/class-funnelback-request.php');
require_once('classes/class-funnelback-display.php');

// Shortcode
function bcfunnelback_shortcode( $sc_config ) {
	$sc_config = shortcode_atts( array(
		'query_peram'      => 'txtQuery',
		'site_peram'       => 'site',
		'engine_url'       => 'https://stage-15-20-search.clients.funnelback.com/s/search.html',
		'collection'       => 'bellevuecollege-search',
		'localstorage_key' => 'searchHistory',
		'debug'            => false
	), $sc_config, 'bcfunnelback_shortcode' );

	$cookie_name = 'user-id';

	$request = new Funnelback_Request(
		$sc_config['engine_url'],
		$sc_config['collection'],
		$_GET,
		$sc_config['query_peram'],
		$cookie_name,
	);
	$raw_results = $request->get_results();

	$results_display = new Funnelback_Display(
		$raw_results,
		$sc_config['debug'],
		$cookie_name,
	);

	return $results_display->display();

}

add_shortcode( 'bc-funnelback-search', 'bcfunnelback_shortcode' );

/**
 * Enqueue Scripts and Styles
 */
function bcfunnelback_scripts() {
	wp_register_style( 'bcfunnelback_style', plugin_dir_url( __FILE__ ) . 'css/funnelback.css', '1.0.1' );
	wp_enqueue_style( 'bcfunnelback_style' );

	//wp_enqueue_script( 'typeahead_script', 'https://stage-15-20-search.clients.funnelback.com/s/resources-global/thirdparty/typeahead-0.11.1/typeahead.bundle.min.js', array( 'jquery' ), '1.0.1', true );
	//wp_enqueue_script( 'handlebars_script', 'https://stage-15-20-search.clients.funnelback.com/s/resources-global/thirdparty/handlebars-4.0.5/handlebars.min.js', array( 'jquery' ), '1.0.1', true );
	//wp_enqueue_script( 'funnelback_script', 'https://stage-15-20-search.clients.funnelback.com/s/resources-global/js/funnelback.autocompletion-2.6.0.js', array( 'jquery' ), '1.0.1', true );
}

add_action( 'wp_enqueue_scripts', 'bcfunnelback_scripts' );
