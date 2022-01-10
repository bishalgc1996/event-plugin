<?php
/**
 * The admin-specific functionality of the plugin.
 *

 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 *

 */
class Import_Events_Admin
{

    /**
     * Admin page URL
     *
     * @var string
     */
    public $adminpage_url;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        $this->adminpage_url = admin_url('admin.php?page=event_display_page');

        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    /**
     * Create the Admin menu and submenu and assign their links to global varibles.
     *
     * @since 1.0
     * @return void
     */
    public function add_menu_pages()
    {

        add_menu_page(__('Events Display', 'import-events'), __('Event Display Plugin Page', 'import-events'), 'manage_options', 'event_display_page', array($this, 'admin_page'), 'dashicons-calendar-alt', '30');
    }

    /**
     * Load Admin Scripts
     *
     * Enqueues the required admin scripts.
     *
     * @since 1.0
     * @param string $hook Page hook.
     * @return void
     */
    public function enqueue_admin_scripts($hook)
    {

        $js_dir = ED_PLUGIN_URL . 'assets/js/';
        wp_register_script('import-events-scripts', $js_dir . 'import-events-admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'wp-color-picker'), ED_VERSION);
        wp_enqueue_script('import-events-scripts');

    }

    /**
     * Load Admin Styles.
     *
     * Enqueues the required admin styles.
     *
     * @since 1.0
     * @param string $hook Page hook.
     * @return void
     */
    public function enqueue_admin_styles($hook)
    {

        $css_dir = ED_PLUGIN_URL . 'assets/css/';
        wp_enqueue_style('jquery-ui', $css_dir . 'jquery-ui.css', false, '1.12.0');
        wp_enqueue_style('import-events-display', $css_dir . 'import-events-admin.css', false, '');
        wp_enqueue_style('wp-color-picker');

    }

    /**
     * Load Admin page.
     *
     * @since 1.0
     * @return void
     */
    public function admin_page()
    {

        ?>
<div class="wrap">
  <h2><?php esc_html_e('Events Display', 'import-events');?></h2>
  <?php
// Set Default Tab to Import.
        $tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'shortcodes'; // input var okay.
        $ntab = isset($_GET['ntab']) ? sanitize_text_field(wp_unslash($_GET['ntab'])) : 'import'; // input var okay.
        ?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">

      <div id="postbox-container-1" class="postbox-container">
      </div>
      <div id="postbox-container-2" class="postbox-container">

        <h1 class="nav-tab-wrapper">




          <a href="<?php echo esc_url(add_query_arg('tab', 'shortcodes', $this->adminpage_url)); ?>"
            class="nav-tab <?php if ('shortcodes' === $tab) {echo 'nav-tab-active';}?>">
            <?php esc_html_e('Shortcodes', 'import-eventbrite-events');?>
          </a>

          <a href="<?php echo esc_url(add_query_arg('tab', 'support', $this->adminpage_url)); ?>"
            class="nav-tab <?php if ('support' === $tab) {echo 'nav-tab-active';}?>">
            <?php esc_html_e('Support & Help', '-events');?>
          </a>

          <a href="<?php echo esc_url(add_query_arg('tab', 'endpoint-url', $this->adminpage_url)); ?>"
            class="nav-tab <?php if ('endpoint-url' === $tab) {echo 'nav-tab-active';}?>">
            <?php esc_html_e('End Point', '-events');?>
          </a>

          <a href="<?php echo esc_url(add_query_arg('tab', 'events-with-pagination', $this->adminpage_url)); ?>"
            class="nav-tab <?php if ('events-with-pagination' === $tab) {echo 'nav-tab-active';}?>">
            <?php esc_html_e('Events with pagination', '-events');?>
          </a>
        </h1>

        <div class="import-events-page">
          <?php
if ('shortcodes' === $tab) {
            require_once ED_PLUGIN_DIR . '/templates/admin/import-events-shortcode.php';
        } elseif ('support' === $tab) {
            require_once ED_PLUGIN_DIR . '/templates/admin/import-events-support.php';
        } elseif ('endpoint-url' === $tab) {
            require_once ED_PLUGIN_DIR . '/templates/admin/import-events-endpoint.php';
        } elseif ('events-with-pagination' === $tab) {
            require_once ED_PLUGIN_DIR . '/templates/admin/import-events-with-pagination.php';

        }
        ?>
          <div style="clear: both"></div>
        </div>

      </div>
    </div>
  </div>
  <?php
}

    /**
     * Display notices in admin.
     *
     * @since    1.0.0
     */
    public function display_notices()
    {
        global $ed_errors, $ed_success_msg, $ed_warnings, $ed_info_msg;

        if (!empty($ed_errors)) {
            foreach ($ed_errors as $error):
            ?>
  <div class="notice notice-error is-dismissible">
    <p><?php echo $error; ?></p>
  </div>
  <?php
endforeach;
        }

        if (!empty($ed_success_msg)) {
            foreach ($ed_success_msg as $success):
            ?>
  <div class="notice notice-success is-dismissible">
    <p><?php echo $success; ?></p>
  </div>
  <?php
endforeach;
        }

        if (!empty($ed_warnings)) {
            foreach ($ed_warnings as $warning):
            ?>
  <div class="notice notice-warning is-dismissible">
    <p><?php echo $warning; ?></p>
  </div>
  <?php
endforeach;
        }

        if (!empty($ed_info_msg)) {
            foreach ($ed_info_msg as $info):
            ?>
  <div class="notice notice-info is-dismissible">
    <p><?php echo $info; ?></p>
  </div>
  <?php
endforeach;
        }

    }

    /**
     * Register custom post type for scheduled imports.
     *
     * @since    1.0.0
     */
    public function register_scheduled_import_cpt()
    {
        $labels = array(
            'name' => _x('Scheduled Import', 'post type general name', 'import-eventbrite-events'),
            'singular_name' => _x('Scheduled Import', 'post type singular name', 'import-eventbrite-events'),
            'menu_name' => _x('Scheduled Imports', 'admin menu', 'import-eventbrite-events'),
            'name_admin_bar' => _x('Scheduled Import', 'add new on admin bar', 'import-eventbrite-events'),
            'add_new' => _x('Add New', 'book', 'import-eventbrite-events'),
            'add_new_item' => __('Add New Import', 'import-eventbrite-events'),
            'new_item' => __('New Import', 'import-eventbrite-events'),
            'edit_item' => __('Edit Import', 'import-eventbrite-events'),
            'view_item' => __('View Import', 'import-eventbrite-events'),
            'all_items' => __('All Scheduled Imports', 'import-eventbrite-events'),
            'search_items' => __('Search Scheduled Imports', 'import-eventbrite-events'),
            'parent_item_colon' => __('Parent Imports:', 'import-eventbrite-events'),
            'not_found' => __('No Imports found.', 'import-eventbrite-events'),
            'not_found_in_trash' => __('No Imports found in Trash.', 'import-eventbrite-events'),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Scheduled Imports.', 'import-eventbrite-events'),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => false,
            'rewrite' => false,
            'capability_type' => 'page',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title'),
            'menu_position' => 5,
        );

        register_post_type('ed_scheduled_import', $args);
    }

    /**
     * Register custom post type for Save import history.
     *
     * @since    1.0.0
     */
    public function register_history_cpt()
    {
        $labels = array(
            'name' => _x('Import History', 'post type general name', 'import-eventbrite-events'),
            'singular_name' => _x('Import History', 'post type singular name', 'import-eventbrite-events'),
            'menu_name' => _x('Import History', 'admin menu', 'import-eventbrite-events'),
            'name_admin_bar' => _x('Import History', 'add new on admin bar', 'import-eventbrite-events'),
            'add_new' => _x('Add New', 'book', 'import-eventbrite-events'),
            'add_new_item' => __('Add New', 'import-eventbrite-events'),
            'new_item' => __('New History', 'import-eventbrite-events'),
            'edit_item' => __('Edit History', 'import-eventbrite-events'),
            'view_item' => __('View History', 'import-eventbrite-events'),
            'all_items' => __('All Import History', 'import-eventbrite-events'),
            'search_items' => __('Search History', 'import-eventbrite-events'),
            'parent_item_colon' => __('Parent History:', 'import-eventbrite-events'),
            'not_found' => __('No History found.', 'import-eventbrite-events'),
            'not_found_in_trash' => __('No History found in Trash.', 'import-eventbrite-events'),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Import History', 'import-eventbrite-events'),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => false,
            'rewrite' => false,
            'capability_type' => 'page',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title'),
            'menu_position' => 5,
        );

        register_post_type('ed_import_history', $args);
    }

    /**
     * Add Import Eventbrite Events ratting text
     *
     * @since  1.0
     * @param  string $footer_text WP Admin Footer text.
     * @return string $footer_text Altered WP Admin Footer text.
     */
    public function add_event_aggregator_credit($footer_text)
    {
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : ''; // input var okey.
        if (!empty($page) && 'eventbrite_event' === $page) {
            $rate_url = 'https://wordpress.org/support/plugin/import-eventbrite-events/reviews/?rate=5#new-post';

            $footer_text .= sprintf(
                /* translators: leave %1$s, %2$s and %3$s */
                esc_html__(' Rate %1$sImport Eventbrite Events%2$s %3$s', 'import-eventbrite-events'),
                '<strong>',
                '</strong>',
                '<a href="' . $rate_url . '" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
            );
        }
        return $footer_text;
    }

    /**
     * Render database upgrade notice. if older version is installed.
     *
     * @since 1.0.0
     * @return void
     */
    public function database_upgrade_notice()
    {
        global $ed_info_msg;
        $auto_import_options = get_option('xtei_auto_import_options', array());
        if (!empty($auto_import_options)) {
            $auto_import = isset($auto_import_options['enable_auto_import']) ? $auto_import_options['enable_auto_import'] : array();
            if (!empty($auto_import)) {
                $upgrade_args = array(
                    'ed_upgrade_action' => 'database',
                );
                $update_button = sprintf(
                    '<a class="button-primary" href="%1$s">%2$s</a>',
                    esc_url(wp_nonce_url(add_query_arg($upgrade_args), 'ed_upgrade_action_nonce')),
                    esc_html__('Update', 'import-eventbrite-events')
                );
                $ed_info_msg[] = esc_html__('Please click update for finish update of Import Eventbrite Events. ', 'import-eventbrite-events') . $update_button;
            }
        }
    }

    /**
     * Database upgrade Proceed.
     *
     * @since 1.1.0
     * @return void
     */
    public function maybe_proceed_database_upgrade()
    {
        if (isset($_GET['ed_upgrade_action']) && 'database' === sanitize_text_field(wp_unslash($_GET['ed_upgrade_action'])) && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'ed_upgrade_action_nonce')) { // input var okey.

            $auto_import_options = get_option('xtei_auto_import_options', array());
            $auto_import = isset($auto_import_options['enable_auto_import']) ? $auto_import_options['enable_auto_import'] : array();
            if (!empty($auto_import)) {

                $xtei_options = get_option('xtei_eventbrite_options', array());
                foreach ($auto_import as $import_into) {
                    $event_data = $event_cats = array();

                    if ('tec' === $import_into) {
                        $event_cats = isset($auto_import_options['xtei_event_cats']) ? $auto_import_options['xtei_event_cats'] : array();
                    }
                    if ('em' === $import_into) {
                        $event_cats = isset($auto_import_options['xtei_event_em_cats']) ? $auto_import_options['xtei_event_em_cats'] : array();
                    }
                    $event_data['import_into'] = $import_into;
                    $event_data['import_type'] = 'scheduled';
                    $event_data['import_frequency'] = isset($auto_import_options['cron_interval']) ? $auto_import_options['cron_interval'] : 'twicedaily';
                    $event_data['event_cats'] = $event_cats;
                    $event_data['event_status'] = isset($xtei_options['default_status']) ? $xtei_options['default_status'] : 'pending';
                    $event_data['import_origin'] = 'eventbrite';
                    $event_data['import_by'] = 'your_events';
                    $event_data['eventbrite_event_id'] = '';
                    $event_data['organizer_id'] = '';

                    $insert_args = array(
                        'post_type' => 'ed_scheduled_import',
                        'post_status' => 'publish',
                        'post_title' => 'Your profile Events',
                    );
                    $insert = wp_insert_post($insert_args, true);
                    if (is_wp_error($insert)) {
                        $ed_errors[] = esc_html__('Something went wrong when insert url.', 'import-eventbrite-events') . $insert->get_error_message();
                        return;
                    }
                    $import_frequency = isset($event_data['import_frequency']) ? $event_data['import_frequency'] : 'twicedaily';
                    update_post_meta($insert, 'import_origin', 'eventbrite');
                    update_post_meta($insert, 'import_eventdata', $event_data);
                    wp_schedule_event(time(), $import_frequency, 'ed_run_scheduled_import', array('post_id' => $insert));
                }
                delete_option('xtei_auto_import_options');
                $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'eventbrite_event'; // input var okey.
                $tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'scheduled'; // input var okey.
                $wp_redirect = admin_url('admin.php');
                $query_args = array(
                    'page' => $page,
                    'ed_msg' => 'upgrade_finish',
                    'tab' => $tab,
                );
                wp_redirect(add_query_arg($query_args, $wp_redirect));
                exit;
            }
        }
    }

    /**
     * Get Plugin Details.
     *
     * @since  1.1.0
     * @param  string $slug Plugin Slug.
     * @return array
     */
    public function get_wporg_plugin($slug)
    {

        if (empty($slug)) {
            return false;
        }

        $transient_name = 'support_plugin_box' . $slug;
        $plugin_data = get_transient($transient_name);
        if (false === $plugin_data) {
            if (!function_exists('plugins_api')) {
                include_once ABSPATH . '/wp-admin/includes/plugin-install.php';
            }

            $plugin_data = plugins_api(
                'plugin_information', array(
                    'slug' => $slug,
                    'is_ssl' => is_ssl(),
                    'fields' => array(
                        'banners' => true,
                        'active_installs' => true,
                    ),
                )
            );

            if (!is_wp_error($plugin_data)) {
                set_transient($transient_name, $plugin_data, 24 * HOUR_IN_SECONDS);
            } else {
                // If there was a bug on the Current Request just leave.
                return false;
            }
        }
        return $plugin_data;
    }

    /**
     * Render imported Events in history Page.
     *
     * @return void
     */
    public function ed_view_import_history_handler()
    {
        define('IFRAME_REQUEST', true);
        iframe_header();
        $history_id = isset($_GET['history']) ? absint($_GET['history']) : 0;
        if ($history_id > 0) {
            $imported_data = get_post_meta($history_id, 'imported_data', true);
            if (!empty($imported_data)) {
                ?>
  <table class="widefat fixed striped">
    <thead>
      <tr>
        <th id="title" class="column-title column-primary"><?php esc_html_e('Event', 'import-eventbrite-events');?></th>
        <th id="action" class="column-operation"><?php esc_html_e('Created/Updated', 'import-eventbrite-events');?></th>
        <th id="action" class="column-date"><?php esc_html_e('Action', 'import-eventbrite-events');?></th>
      </tr>
    </thead>
    <tbody id="the-list">
      <?php
foreach ($imported_data as $event) {
                    ?>
      <tr>
        <td class="title column-title">
          <?php
printf(
                        '<a href="%1$s" target="_blank">%2$s</a>',
                        get_the_permalink($event['id']),
                        get_the_title($event['id'])
                    );
                    ?>
        </td>
        <td class="title column-title">
          <?php echo ucfirst($event['status']); ?>
        </td>
        <td class="title column-action">
          <?php
printf(
                        '<a href="%1$s" target="_blank">%2$s</a>',
                        get_edit_post_link($event['id']),
                        __('Edit', 'import-eventbrite-events')
                    );
                    ?>
        </td>
      </tr>
      <?php
}
                ?>
    </tbody>
  </table>
  <?php
?>
  <?php
} else {
                ?>
  <div class="ed_no_import_events">
    <?php esc_html_e('No data found', 'import-eventbrite-events');?>
  </div>
  <?php
}
        } else {
            ?>
  <div class="ed_no_import_events">
    <?php esc_html_e('No data found', 'import-eventbrite-events');?>
  </div>
  <?php
}
        ?>
  <style>
  .ed_no_import_events {
    text-align: center;
    margin-top: 60px;
    font-size: 1.4em;
  }
  </style>
  <?php
iframe_footer();
        exit;
    }

}