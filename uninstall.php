<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       #
 *
 * @package    Event Plugin
 */

// If uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}



// Delete option from options table
delete_option('ep_myplugin_options');

//remove any additional options and custom tables

register_uninstall_hook($file, $function);
