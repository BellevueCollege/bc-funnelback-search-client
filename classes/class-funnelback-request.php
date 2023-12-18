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
		$this->custom_query_param  = $custom_query_param;
		$this->cookie_name         = $cookie_name;
	}

	public function get_results() {
		return wp_remote_get(
			self::build_request_url(
				$this->engine_url,
				$this->collection,
				$this->custom_query_param,
				$this->raw_query
			),
			array(
				'timeout' => 10,
				'headers' => self::build_request_headers(),
				'cookies' => self::build_request_cookies( $this->cookie_name ),
			)
		);
	}

	public function post_request() {
		return wp_remote_post(
			self::build_request_url(
				$this->engine_url,
				$this->collection,
				$this->custom_query_param,
				$this->raw_query
			),
			array(
				'timeout' => 10,
				'headers' => self::build_request_headers(),
				'cookies' => self::build_request_cookies( $this->cookie_name ),
			)
		);
	}
	public function put_request() {
		return wp_remote_request(
			self::build_request_url(
				$this->engine_url,
				$this->collection,
				$this->custom_query_param,
				$this->raw_query
			),
			array(
				'timeout' => 10,
				'headers' => self::build_request_headers(),
				'cookies' => self::build_request_cookies( $this->cookie_name ),
				'method'  => 'PUT'
			)
		);
	}
	public function delete_request() {
		return wp_remote_request(
			self::build_request_url(
				$this->engine_url,
				$this->collection,
				$this->custom_query_param,
				$this->raw_query
			),
			array(
				'timeout' => 10,
				'headers' => self::build_request_headers(),
				'cookies' => self::build_request_cookies( $this->cookie_name ),
				'method'  => 'DELETE'
			)
		);
	}
	/**
	 * Build request URL
	 */
	public static function build_request_url( $engine_url, $collection, $custom_query_param, $raw_query ) {
		
		// Fall back to default query if custom query param is not set
		if ( isset( $_GET[ $custom_query_param ] ) ) {
			$unsanatized_query = $_GET[ $custom_query_param ];
		} else if ( isset( $_GET[ 'query' ] ) ) {
			$unsanatized_query = $_GET[ 'query' ];
		} else {
			$unsanatized_query = '';
		}
		
		$params = array(
			'collection' => $collection,
			'query'      => $unsanatized_query,
		);

		$sanatized_query = array_merge(
			$params,
			self::sanitize_query( $raw_query )
		);

		$query_url = add_query_arg( $sanatized_query, $engine_url );

		/**
		 * PHP kindly replaced dots with underscores in array keys. 
		 * This is a quick and dirty search/replace to fix this, as funnelback uses f.{thing} for several things
		 */
		return str_replace('f_', 'f.', $query_url);
	}

	public static function build_request_headers() {
		return array(
			'X-Forwarded-For' => self::get_user_ip(),
		);
	}

	public static function build_request_cookies( $cookie_name ) {
		$cookies = array();
		if ( isset($_COOKIE[ $cookie_name ] ) )  {
			$cookies[] = new WP_Http_Cookie(
					array(
						'name'  => $cookie_name,
						'value' => $_COOKIE[ $cookie_name ]
					)
				);
		}
		return $cookies;
	}

	/**
	 * Utility: Sanitize a Query Array
	 */
	public static function sanitize_query( $query ) {
		return array_map( 
			function( $val ) {
				return urlencode( stripslashes( $val ) );
			}, $query
		);
	}

	public static function get_user_ip() {
		if ( !empty($_SERVER['HTTP_CLIENT_IP'] ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return  $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}



}