<?php
/*
Plugin Name: Event Plugin
Plugin URI: #
Description: It is a simple event plugin
Version: 1.0
Author: Bishal GC
Author URI: bishalgc.com.np
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
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Events_Display')):

/**
 * Main  Events Display class
 */
    class Events_Display
{

        /** Singleton *************************************************************/

        private static $instance;

        public static function instance()
    {

            if (!isset(self::$instance) && !(self::$instance instanceof Events_Display)) {
                self::$instance = new Events_Display();
                self::$instance->setup_constants();

                add_action('wp_enqueue_scripts', array(self::$instance, 'ed_enqueue_style'));

                self::$instance->includes();
                self::$instance->common = new Import_Events_Common();

                self::$instance->cpt = new Events_Cpt();

            }
            return self::$instance;
        }

        private function __construct()
    {
            /* Do nothing here */
        }

        /**
         * Setup plugins constants.
         *
         * @access private
         * @since 1.0.0
         * @return void
         */
        private function setup_constants()
    {
            // Plugin version.
            if (!defined('ED_VERSION')) {
                define('ED_VERSION', '1.0');
            }

            // Plugin folder Path.
            if (!defined('ED_PLUGIN_DIR')) {
                define('ED_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }

            // Plugin folder URL.
            if (!defined('ED_PLUGIN_URL')) {
                define('ED_PLUGIN_URL', plugin_dir_url(__FILE__));
            }

            // Plugin root file.
            if (!defined('ED_PLUGIN_FILE')) {
                define('ED_PLUGIN_FILE', __FILE__);
            }

            // Options.
            if (!defined('ED_OPTIONS')) {
                define('ED_OPTIONS', 'event_display_options');
            }
        }
        /**
         * Include required files.
         *
         * @access private
         * @since 1.0.0
         * @return void
         */

        private function includes()
    {

            require_once ED_PLUGIN_DIR . 'includes/class-events-cpt.php';
            require_once ED_PLUGIN_DIR . 'includes/class-events-common.php';

        }

        public function ed_enqueue_style()
    {
            $css_dir = ED_PLUGIN_URL . 'assets/css/';
            wp_enqueue_style('import-events-front', $css_dir . 'import-events.css', false, '');
        }

    }

endif;

function run_events_display()
{
    return Events_Display::instance();
}
global $ed_events;
$ed_events = run_events_display();

/**
 * Get Import events setting options
 *
 * @since 1.0
 * @param string $type Option type.
 * @return array|bool Options.
 */
function ed_get_import_options($type = '')
{
    $ed_options = get_option(ED_OPTIONS);
    return $ed_options;
}

function ed_events_display_activate()
{

    if (version_compare(get_bloginfo('version'), '3.1', ' < ')) {
        deactivate_plugins(basename(__FILE__)); // Deactivate our plugin
    }
    global $ed_events;
    $ed_events->cpt->register_event_post_type();
    flush_rewrite_rules();

}
register_activation_hook(__FILE__, 'ed_events_display_activate');