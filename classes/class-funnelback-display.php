<?php

/** Funnelback Display */

class Funnelback_Display {
	public $raw_results;
	public $debug;
	public $cookie_name;

	public function __construct( $raw_results, $debug, $cookie_name ) {
		$this->raw_results = $raw_results;
		$this->debug       = $debug;
		$this->cookie_name = $cookie_name;
	}

	public function display() {
		if ( 200 === wp_remote_retrieve_response_code( $this->raw_results ) ) {
			$output = wp_remote_retrieve_body( $this->raw_results );
			$output .= $this->build_cookie_script();
			$output .= ( 'true' === $this->debug ) ? $this->debug() : '';

		} else {
			$output = $this->error();
			$output .= ( 'true' === $this->debug ) ? $this->debug() : '';
		}

		return $output;
	}

	public function display_cart() {
		if ( 200 === wp_remote_retrieve_response_code( $this->raw_results ) ) {
			$output = wp_remote_retrieve_body( $this->raw_results );
			$cookie = wp_remote_retrieve_cookie( $this->raw_results, $this->cookie_name );
			setcookie( $cookie->name, $cookie->value, $cookie->expires );
			
		} else {
			$output = $this->error();
		}

		return $output;
	}

	public function error() {
		return '<div class="alert alert-danger"><h2>Error: Unable to retrieve search results</h2><p>Please refresh the page and try again. If you continue seeing this error, please contact the <a href="https://www.bellevuecollege.edu/servicedesk">Bellevue College Service Desk</a>.</p></div>';
	}
	public function debug() {
		return "<hr><div class='alert alert-dark'><pre><code>". htmlentities( print_r( $this->raw_results, true ) )."</code></pre></div>";
	}

	private function build_cookie_script() {

		/**
		 * Get Cookie
		 */
		$cookie = wp_remote_retrieve_cookie( $this->raw_results, $this->cookie_name );

		if ( isset( $cookie->name ) ) {
			$expiration = date("M d Y H:i:s", $cookie->expires);

			return "<script>document.cookie=\"$cookie->name=$cookie->value;expires='$expiration'\";</script>";
		} else {
			return '';
		}

	}

}