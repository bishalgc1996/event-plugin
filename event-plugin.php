<?php
/*
Plugin Name: Event Plugin
Plugin URI: #
Description: It is a simple event plugin
Version: 1.0
Author: Bishal GC
Author URI: bishalgc.com
 */

/* Copyright Bishal GC (email : gcbishal03@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Events_Display' ) ):

	/**
	 * Main  Events Display class
	 */
	class Events_Display {

		/** Singleton *************************************************************/

		private static $instance;

		public static function instance() {

			if ( ! isset( self::$instance )
			     && ! ( self::$instance instanceof Events_Display )
			) {
				self::$instance = new Events_Display();
				self::$instance->setup_constants();

				add_action( 'wp_enqueue_scripts',
					array( self::$instance, 'ed_enqueue_style' ) );
				add_action( 'wp_enqueue_scripts',
					array( self::$instance, 'ed_enqueue_script' ) );

				self::$instance->includes();
				self::$instance->common = new Import_Events_Common();

				self::$instance->cpt   = new Events_Cpt();
				self::$instance->admin = new Import_Events_Admin();

			}

			return self::$instance;
		}

		private function __construct() {
			/* Do nothing here */
		}

		/**
		 * Setup plugins constants.
		 *
		 * @access private
		 * @return void
		 * @since  1.0.0
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'ED_VERSION' ) ) {
				define( 'ED_VERSION', '1.0' );
			}

			// Plugin folder Path.
			if ( ! defined( 'ED_PLUGIN_DIR' ) ) {
				define( 'ED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin folder URL.
			if ( ! defined( 'ED_PLUGIN_URL' ) ) {
				define( 'ED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin root file.
			if ( ! defined( 'ED_PLUGIN_FILE' ) ) {
				define( 'ED_PLUGIN_FILE', __FILE__ );
			}

			// Options.
			if ( ! defined( 'ED_OPTIONS' ) ) {
				define( 'ED_OPTIONS', 'event_display_options' );
			}
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @return void
		 * @since  1.0.0
		 */

		private function includes() {
			require_once ED_PLUGIN_DIR . 'includes/class-events-cpt.php';
			require_once ED_PLUGIN_DIR
			             . 'includes/class-import-events-list-table.php';
			require_once ED_PLUGIN_DIR . 'includes/class-events-common.php';
			require_once ED_PLUGIN_DIR
			             . 'includes/class-import-events-admin.php';
			require_once ED_PLUGIN_DIR
			             . 'includes/custom-rest-api-functions.php';
			require_once ED_PLUGIN_DIR . 'includes/ajax-processor.php';
			require_once ED_PLUGIN_DIR . 'includes/custom-functions.php';
		}

		public function ed_enqueue_style() {
			$css_dir = ED_PLUGIN_URL . 'assets/css/';
			wp_enqueue_style( 'import-events-front',
				$css_dir . 'import-events.css', false, '' );
			wp_enqueue_style( 'import-events-front-bs',
				$css_dir . 'bootstrap.min.css', false, '' );
		}

		/**
		 * Enqueue script front-end
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function ed_enqueue_script() {

			$js_dir = ED_PLUGIN_URL . 'assets/js/';

			wp_enqueue_script( 'import-events-front-bs-js',
				$js_dir . 'bootstrap.min.js', false, '' );


			wp_enqueue_script( 'events-slider-js',
				$js_dir . 'jquery.cycle2.min.js', array( 'jquery' ), '',
				false );


			wp_enqueue_script( 'import-events-filter-js',
				$js_dir . 'ajax-script.js', array( 'jquery' ), '',
				false );

			wp_localize_script( 'import-events-filter-js', 'my_ajax_url', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			) );



		}

	}

endif;

function run_events_display() {
	return Events_Display::instance();
}

global $ed_events;
$ed_events = run_events_display();

/**
 * Get Import events setting options
 *
 * @param  string  $type  Option type.
 *
 * @return array|bool Options.
 * @since 1.0
 */
function ed_get_import_options( $type = '' ) {
	$ed_options = get_option( ED_OPTIONS );

	return $ed_options;
}

function ed_events_display_activate() {

	global $ed_events;
	$ed_events->cpt->register_event_post_type();
	flush_rewrite_rules();

}

register_activation_hook( __FILE__, 'ed_events_display_activate' );