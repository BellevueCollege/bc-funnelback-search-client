<?php

/** Funnelback Request */

class Funnelback_Request {
	public $engine_url; 
	public $collection;
	public $raw_query;
	public $custom_query_param;
	public $cookie_name;

	public function __construct( $engine_url, $collection, $raw_query, $custom_query_param, $cookie_name ) {
		$this->engine_url          = $engine_url;
		$this->collection          = $collection;
		$this->raw_query           = $raw_query;
		$this->custom_query_peram  = $custom_query_param;
		$this->cookie_name         = $cookie_name;
	}

	public function get_results() {
		return wp_remote_get(
			$this->build_request_url(),
			array(
				'timeout' => 10,
				'headers' => $this->build_request_headers(),
				'cookies' => $this->build_request_cookies(),
			)
		);
	}
	/**
	 * Build request URL
	 */
	public function build_request_url() {
		$params = array(
			'collection' => $this->collection,
			'query'      => $this->custom_query_param,
		);

		$sanatized_query = array_merge(
			$params,
			$this->sanitize_query( $this->raw_query )
		);

		$query_url = add_query_arg( $sanatized_query, $this->engine_url );

		/**
		 * PHP kindly replaced dots with underscores in array keys. 
		 * This is a quick and dirty search/replace to fix this, as funnelback uses f.{thing} for several things
		 */
		return str_replace('f_', 'f.', $query_url);
	}

	public function build_request_headers() {
		return array(
			'X-Forwarded-For' => $this->get_user_ip(),
		);
	}

	public function build_request_cookies() {
		$cookies = array();
		if ( isset($_COOKIE[ $this->cookie_name ] ) )  {
			$cookies[] = new WP_Http_Cookie(
					array(
						'name'  => $this->cookie_name,
						'value' => $_COOKIE[ $this->cookie_name ]
					)
				);
		}
		return $cookies;
	}

	/**
	 * Utility: Sanitize a Query Array
	 */
	public function sanitize_query( $query ) {
		return array_map( 
			function( $val ) {
				return esc_attr( $val );
			}, $query
		);
	}

	public function get_user_ip() {
		if ( !empty($_SERVER['HTTP_CLIENT_IP'] ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return  $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}



}