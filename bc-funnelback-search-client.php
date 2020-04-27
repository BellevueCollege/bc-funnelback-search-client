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

// Shortcode
function bcfunnelback_shortcode( $sc_config ) {
	$sc_config = shortcode_atts( array(
		'query_peram'      => 'txtQuery',
		'site_peram'       => 'site',
		'site_filter_id'   => 'site_home_url',
		'engine_url'       => 'https://stage-15-20-search.clients.funnelback.com/s/search.html',
		'collection'       => 'bellevuecollege-search',
		'localstorage_key' => 'searchHistory',
		'debug'            => false
	), $sc_config, 'bcfunnelback_shortcode' );

	$sanatized_query = array_map( 
		function( $val ) {
			return esc_attr( $val );
		}, $_GET
	);

	
	$funnelback_perams = array(
		'collection' => $sc_config['collection'],
		'query'      => $sanatized_query[$sc_config['query_peram']]
	);

	$sanatized_query = array_merge( $funnelback_perams, $_GET);

	$funnelback_url = $sc_config['engine_url'];

	$query_url = add_query_arg( $sanatized_query, $funnelback_url );

	/**
	 * PHP kindly replaced dots with underscores in array keys. 
	 * This is a quick and dirty search/replace to fix this, as funnelback uses f.{thing} for several things
	 */
	$query_url = str_replace('f_', 'f.', $query_url);

	$results = wp_remote_get( $query_url );

	
	if( 'true' === $sc_config['debug'] ) {
		if ( 200 === $results['response']['code'] ) {
			return $results['body'] . "<hr><div class='alert alert-dark'><p class='text-monospace'>$query_url</p><pre><code>". print_r($sanatized_query, true)."</code></pre></div>";
		} else {
			return '<div class="alert alert-danger"><h2>Error: Unable to retrieve search results</h2><p>Please refresh the page and try again. If you continue seeing this error, please contact the <a href="https://www.bellevuecollege.edu/servicedesk">Bellevue College Service Desk</a>.</p><hr><pre>'. print_r($results, true).'</pre></div>';
		}
	} else {
		if ( 200 === $results['response']['code'] ) {
			return $results['body'];
		} else {
			return '<div class="alert alert-danger"><h2>Error: Unable to retrieve search results</h2><p>Please refresh the page and try again. If you continue seeing this error, please contact the <a href="https://www.bellevuecollege.edu/servicedesk">Bellevue College Service Desk</a>.</p></div>';
		}
	}

}

add_shortcode( 'bc-funnelback-search', 'bcfunnelback_shortcode' );

function bcfunnelback_scripts() {
	wp_register_style( 'bcfunnelback_style', plugin_dir_url( __FILE__ ) . 'css/funnelback.css', '1.0.1' );
	wp_enqueue_style( 'bcfunnelback_style' );

	//wp_enqueue_script( 'typeahead_script', 'https://stage-15-20-search.clients.funnelback.com/s/resources-global/thirdparty/typeahead-0.11.1/typeahead.bundle.min.js', array( 'jquery' ), '1.0.1', true );
	//wp_enqueue_script( 'handlebars_script', 'https://stage-15-20-search.clients.funnelback.com/s/resources-global/thirdparty/handlebars-4.0.5/handlebars.min.js', array( 'jquery' ), '1.0.1', true );
	//wp_enqueue_script( 'funnelback_script', 'https://stage-15-20-search.clients.funnelback.com/s/resources-global/js/funnelback.autocompletion-2.6.0.js', array( 'jquery' ), '1.0.1', true );
}

add_action( 'wp_enqueue_scripts', 'bcfunnelback_scripts' );
