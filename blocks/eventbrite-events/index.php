<?php
/**
 * Eventbrite Events Block Initializer
 *
 * @since   1.6
 * @package    Import_Eventbrite_Events
 * @subpackage Import_Eventbrite_Events/includes
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Gutenberg Block
 *
 * @return void
 */
function ed_register_gutenberg_block()
{
    global $ed_events;
    if (function_exists('register_block_type')) {
        // Register block editor script.
        $js_dir = ED_PLUGIN_URL . 'assets/js/';
        wp_register_script(
            'ed-events-block',
            $js_dir . 'gutenberg.blocks.js',
            array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor'),
            ED_VERSION
        );

        // Register block editor style.
        $css_dir = ED_PLUGIN_URL . 'assets/css/';
        wp_register_style(
            'ed-events-block-style',
            $css_dir . 'import-events.css',
            array(),
            ED_VERSION
        );

        // Register our block.
        register_block_type('ed-block/events', array(
            'attributes' => array(
                'col' => array(
                    'type' => 'number',
                    'default' => 3,
                ),
                'posts_per_page' => array(
                    'type' => 'number',
                    'default' => 12,
                ),
                'past_events' => array(
                    'type' => 'string',
                ),
                'start_date' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'end_date' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'order' => array(
                    'type' => 'string',
                    'default' => 'ASC',
                ),
                'orderby' => array(
                    'type' => 'string',
                    'default' => 'event_start_date',
                ),

            ),
            'editor_script' => 'ed-events-block', // The script name we gave in the wp_register_script() call.
            'editor_style' => 'ed-events-block-style', // The script name we gave in the wp_register_style() call.
            'render_callback' => array($ed_events->cpt, 'events_slider'),
        ));
    }
}

add_action('init', 'ed_register_gutenberg_block');