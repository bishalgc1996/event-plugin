<?php
/**
 * Class for Register and manage Events.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Event Plugin
 * @subpackage Event Plugin/includes
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
class Events_Cpt
{
    // The Events Calendar Event Taxonomy
    public $event_slug;
    // Event post type.
    protected $event_posttype;
    // Event post type.
    protected $event_category;
    // Event post type.
    protected $event_tag;
    public function __construct()
    {
        $this->event_slug = 'event_display';
        $this->event_posttype = 'events';
        $this->event_category = 'events_type';
        $this->event_tag = 'event_tag';
        $ed_options = get_option(ED_OPTIONS);
        $deactive_ed_events = isset($ed_options['deactive_edevents']) ? $ed_options['deactive_edevents'] : 'no';
        if ($deactive_ed_events != 'yes') {
            add_action('init', array($this, 'register_event_post_type'));
            add_action('init', array($this, 'register_event_taxonomy'));
            add_action('add_meta_boxes', array($this, 'add_event_meta_boxes'));
            add_action('save_post', array($this, 'save_event_meta_boxes'), 10, 2);
            add_shortcode('events_display', array($this, 'events_archive'));
            add_shortcode('events_display_filter', array($this, 'events_archive_with_filter'));
        }
    }
    /**
     * get Events Post type
     *
     * @since    1.0.0
     */
    public function get_event_posttype()
    {
        return $this->event_posttype;
    }
    /**
     * get events category taxonomy
     *
     * @since    1.0.0
     */
    public function get_event_categroy_taxonomy()
    {
        return $this->event_category;
    }
    /**
     * get events tag taxonomy
     *
     * @since    1.0.0
     */
    public function get_event_tag_taxonomy()
    {
        return $this->event_tag;
    }
    /**
     * Register Events Post type
     *
     * @since    1.0.0
     */
    public function register_event_post_type()
    {
        /*
         * Event labels
         */
        $event_labels = array('name' => _x('Events', 'Post Type General Name', 'import-events'), 'singular_name' => _x('Event', 'Post Type Singular Name', 'import-events'), 'menu_name' => __(' Events', 'import-events'), 'name_admin_bar' => __(' Event', 'import-events'), 'archives' => __('Event Archives', 'import-events'), 'parent_item_colon' => __('Parent Event:', 'import-events'), 'all_items' => __(' Events', 'import-events'), 'add_new_item' => __('Add New Event', 'import-events'), 'add_new' => __('Add New', 'import-events'), 'new_item' => __('New Event', 'import-events'), 'edit_item' => __('Edit Event', 'import-events'), 'update_item' => __('Update Event', 'import-events'), 'view_item' => __('View Event', 'import-events'), 'search_items' => __('Search Event', 'import-events'), 'not_found' => __('Not found', 'import-events'), 'not_found_in_trash' => __('Not found in Trash', 'import-events'), 'featured_image' => __('Featured Image', 'import-events'), 'set_featured_image' => __('Set featured image', 'import-events'), 'remove_featured_image' => __('Remove featured image', 'import-events'), 'use_featured_image' => __('Use as featured image', 'import-events'), 'insert_into_item' => __('Insert into Event', 'import-events'), 'uploaded_to_this_item' => __('Uploaded to this Event', 'import-events'), 'items_list' => __('Event Items list', 'import-events'), 'items_list_navigation' => __('Event Items list navigation', 'import-events'), 'filter_items_list' => __('Filter Event items list', 'import-events'));
        $rewrite = array('slug' => $this->event_slug, 'with_front' => false, 'pages' => true, 'feeds' => true, 'ep_mask' => EP_NONE);
        $event_cpt_args = array('label' => __('Events', 'import-events'), 'description' => __('Post type for Events', 'import-events'), 'labels' => $event_labels, 'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions'), 'taxonomies' => array($this->event_category, $this->event_tag), 'hierarchical' => false, 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'menu_position' => 5, 'menu_icon' => 'dashicons-calendar', 'show_in_admin_bar' => true, 'show_in_nav_menus' => true, 'can_export' => true, 'has_archive' => true, 'exclude_from_search' => false, 'publicly_queryable' => true, 'rewrite' => $rewrite);
        register_post_type($this->event_posttype, $event_cpt_args);
    }
    public function register_event_taxonomy()
    {
        /* Register the Event Category taxonomy. */
        register_taxonomy($this->event_category, array($this->event_posttype), array('labels' => array('name' => __('Event Types', 'import-events'), 'singular_name' => __('Event Type', 'import-events'), 'menu_name' => __('Event Types', 'import-events'), 'name_admin_bar' => __('Event Type', 'import-events'), 'search_items' => __('Search Types', 'import-events'), 'popular_items' => __('Popular Types', 'import-events'), 'all_items' => __('All Types', 'import-events'), 'edit_item' => __('Edit Type', 'import-events'), 'view_item' => __('View Type', 'import-events'), 'update_item' => __('Update Type', 'import-events'), 'add_new_item' => __('Add New Type', 'import-events'), 'new_item_name' => __('New Type Name', 'import-events')), 'public' => true, 'show_ui' => true, 'show_in_nav_menus' => true, 'show_admin_column' => true, 'hierarchical' => true, 'query_var' => true));
        /* Register the event Tag taxonomy. */
        register_taxonomy($this->event_tag, array($this->event_posttype), array('public' => true, 'show_ui' => true, 'show_in_nav_menus' => true, 'show_tagcloud' => true, 'show_admin_column' => true, 'hierarchical' => false, 'query_var' => $this->event_tag,
            /* Labels used when displaying taxonomy and terms. */
            'labels' => array('name' => __('Event Tags', 'import-events'), 'singular_name' => __('Event Tag', 'import-events'), 'menu_name' => __('Event Tags', 'import-events'), 'name_admin_bar' => __('Event Tag', 'import-events'), 'search_items' => __('Search Tags', 'import-events'), 'popular_items' => __('Popular Tags', 'import-events'), 'all_items' => __('All Tags', 'import-events'), 'edit_item' => __('Edit Tag', 'import-events'), 'view_item' => __('View Tag', 'import-events'), 'update_item' => __('Update Tag', 'import-events'), 'add_new_item' => __('Add New Tag', 'import-events'), 'new_item_name' => __('New Tag Name', 'import-events'), 'separate_items_with_commas' => __('Separate tags with commas', 'import-events'), 'add_or_remove_items' => __('Add or remove tags', 'import-events'), 'choose_from_most_used' => __('Choose from the most used tags', 'import-events'), 'not_found' => __('No tags found', 'import-events'), 'parent_item' => null, 'parent_item_colon' => null)));
    }
    public function add_event_meta_boxes()
    {
        add_meta_box('events_metabox', __('Events Details', 'import-events'), array($this, 'render_event_meta_boxes'), array($this->event_posttype), 'normal', 'high');
    }
    public function render_event_meta_boxes($post)
    {
        // Use nonce for verification
        wp_nonce_field(ED_PLUGIN_DIR, 'ed_event_metabox_nonce');
        $start_hour = get_post_meta($post->ID, 'event_start_hour', true);
        $start_minute = get_post_meta($post->ID, 'event_start_minute', true);
        $start_meridian = get_post_meta($post->ID, 'event_start_meridian', true);
        $end_hour = get_post_meta($post->ID, 'event_end_hour', true);
        $end_minute = get_post_meta($post->ID, 'event_end_minute', true);
        $end_meridian = get_post_meta($post->ID, 'event_end_meridian', true);
        ?>

<table class="ed_form_table">
  <thead>
    <tr>
      <th colspan="2">
        <?php _e('Time & Date', 'import-events');?>
        <hr>
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php _e('Start Date & Time', 'import-events');?>:</td>
      <td>
        <input type="text" name="event_start_date" class="xt_datepicker" id="event_start_date"
          value="<?php echo get_post_meta($post->ID, 'event_start_date', true); ?>" /> @
        <?php
$this->generate_dropdown('event_start', 'hour', $start_hour);
        $this->generate_dropdown('event_start', 'minute', $start_minute);
        $this->generate_dropdown('event_start', 'meridian', $start_meridian);
        ?>
      </td>
    </tr>
    <tr>
      <td><?php _e('End Date & Time', 'import-events');?>:</td>
      <td>
        <input type="text" name="event_end_date" class="xt_datepicker" id="event_end_date"
          value="<?php echo get_post_meta($post->ID, 'event_end_date', true); ?>" /> @
        <?php
$this->generate_dropdown('event_end', 'hour', $end_hour);
        $this->generate_dropdown('event_end', 'minute', $end_minute);
        $this->generate_dropdown('event_end', 'meridian', $end_meridian);
        ?>
      </td>

    </tr>
  </tbody>
</table>
<div style="clear: both;"></div>
<table class="ed_form_table">
  <thead>
    <tr>
      <th colspan="2">
        <?php _e('Location Details', 'import-events');?>
        <hr>
      </th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td><?php _e('Venue', 'import-events');?>:</td>
      <td>
        <input type="text" name="venue_name" id="venue_name"
          value="<?php echo get_post_meta($post->ID, 'venue_name', true); ?>" />
      </td>
    </tr>

    <tr>
      <td><?php _e('Address', 'import-events');?>:</td>
      <td>
        <input type="text" name="venue_address" id="venue_address"
          value="<?php echo get_post_meta($post->ID, 'venue_address', true); ?>" />
      </td>
    </tr>

    <tr>
      <td><?php _e('City', 'import-events');?>:</td>
      <td>
        <input type="text" name="venue_city" id="venue_city"
          value="<?php echo get_post_meta($post->ID, 'venue_city', true); ?>" />
      </td>
    </tr>

    <tr>
      <td><?php _e('State', 'import-events');?>:</td>
      <td>
        <input type="text" name="venue_state" id="venue_state"
          value="<?php echo get_post_meta($post->ID, 'venue_state', true); ?>" />
      </td>
    </tr>

    <tr>
      <td><?php _e('Country', 'import-events');?>:</td>
      <td>
        <input type="text" name="venue_country" id="venue_country"
          value="<?php echo get_post_meta($post->ID, 'venue_country', true); ?>" />
      </td>
    </tr>

    <tr>
      <td><?php _e('Zipcode', 'import-events');?>:</td>
      <td>
        <input type="text" name="venue_zipcode" id="venue_zipcode"
          value="<?php echo get_post_meta($post->ID, 'venue_zipcode', true); ?>" />
      </td>
    </tr>


    <tr>
      <td><?php _e('Website', 'import-events');?>:</td>
      <td>
        <input type="text" name="venue_url" id="venue_url"
          value="<?php echo get_post_meta($post->ID, 'venue_url', true); ?>" />
      </td>
    </tr>
  </tbody>
</table>

<?php
}
    public function generate_dropdown($start_end, $type, $selected = '')
    {
        if ($start_end == '' || $type == '') {
            return;
        }
        $select_name = $start_end . '_' . $type;
        if ($type == 'hour') {
            ?>
<select name="<?php echo $select_name; ?>">
  <option value="01" <?php selected($selected, '01');?>>01</option>
  <option value="02" <?php selected($selected, '02');?>>02</option>
  <option value="03" <?php selected($selected, '03');?>>03</option>
  <option value="04" <?php selected($selected, '04');?>>04</option>
  <option value="05" <?php selected($selected, '05');?>>05</option>
  <option value="06" <?php selected($selected, '06');?>>06</option>
  <option value="07" <?php selected($selected, '07');?>>07</option>
  <option value="08" <?php selected($selected, '08');?>>08</option>
  <option value="09" <?php selected($selected, '09');?>>09</option>
  <option value="10" <?php selected($selected, '10');?>>10</option>
  <option value="11" <?php selected($selected, '11');?>>11</option>
  <option value="12" <?php selected($selected, '12');?>>12</option>
</select>
<?php
} elseif ($type == 'minute') {
            ?>
<select name="<?php echo $select_name; ?>">
  <option value="00" <?php selected($selected, '00');?>>00</option>
  <option value="05" <?php selected($selected, '05');?>>05</option>
  <option value="10" <?php selected($selected, '10');?>>10</option>
  <option value="15" <?php selected($selected, '15');?>>15</option>
  <option value="20" <?php selected($selected, '20');?>>20</option>
  <option value="25" <?php selected($selected, '25');?>>25</option>
  <option value="30" <?php selected($selected, '30');?>>30</option>
  <option value="35" <?php selected($selected, '35');?>>35</option>
  <option value="40" <?php selected($selected, '40');?>>40</option>
  <option value="45" <?php selected($selected, '45');?>>45</option>
  <option value="50" <?php selected($selected, '50');?>>50</option>
  <option value="55" <?php selected($selected, '55');?>>55</option>
</select>

<?php
} elseif ($type == 'meridian') {
            ?>
<select name="<?php echo $select_name; ?>">
  <option value="am" <?php selected($selected, 'am');?>>am</option>
  <option value="pm" <?php selected($selected, 'pm');?>>pm</option>
</select>
<?php
}
    }
    /**
     * Save Testimonial meta box Options
     */
    public function save_event_meta_boxes($post_id, $post)
    {
        // Verify the nonce before proceeding.
        if (!isset($_POST['ed_event_metabox_nonce']) || !wp_verify_nonce($_POST['ed_event_metabox_nonce'], ED_PLUGIN_DIR)) {
            return $post_id;
        }
        // check user capability to edit post
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
        // can't save if auto save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        // check if team then save it.
        if ($post->post_type != $this->event_posttype) {
            return $post_id;
        }
        // Event Date & time Details
        $event_start_date = isset($_POST['event_start_date']) ? sanitize_text_field($_POST['event_start_date']) : '';
        $event_end_date = isset($_POST['event_end_date']) ? sanitize_text_field($_POST['event_end_date']) : '';
        $event_start_hour = isset($_POST['event_start_hour']) ? sanitize_text_field($_POST['event_start_hour']) : '';
        $event_start_minute = isset($_POST['event_start_minute']) ? sanitize_text_field($_POST['event_start_minute']) : '';
        $event_start_meridian = isset($_POST['event_start_meridian']) ? sanitize_text_field($_POST['event_start_meridian']) : '';
        $event_end_hour = isset($_POST['event_end_hour']) ? sanitize_text_field($_POST['event_end_hour']) : '';
        $event_end_minute = isset($_POST['event_end_minute']) ? sanitize_text_field($_POST['event_end_minute']) : '';
        $event_end_meridian = isset($_POST['event_end_meridian']) ? sanitize_text_field($_POST['event_end_meridian']) : '';
        $start_time = $event_start_date . ' ' . $event_start_hour . ':' . $event_start_minute . ' ' . $event_start_meridian;
        $end_time = $event_end_date . ' ' . $event_end_hour . ':' . $event_end_minute . ' ' . $event_end_meridian;
        $start_ts = strtotime($start_time);
        $end_ts = strtotime($end_time);
        // Venue Deatails
        $venue_name = isset($_POST['venue_name']) ? sanitize_text_field($_POST['venue_name']) : '';
        $venue_address = isset($_POST['venue_address']) ? sanitize_text_field($_POST['venue_address']) : '';
        $venue_city = isset($_POST['venue_city']) ? sanitize_text_field($_POST['venue_city']) : '';
        $venue_state = isset($_POST['venue_state']) ? sanitize_text_field($_POST['venue_state']) : '';
        $venue_country = isset($_POST['venue_country']) ? sanitize_text_field($_POST['venue_country']) : '';
        $venue_zipcode = isset($_POST['venue_zipcode']) ? sanitize_text_field($_POST['venue_zipcode']) : '';
        $venue_lat = isset($_POST['venue_lat']) ? sanitize_text_field($_POST['venue_lat']) : '';
        $venue_lon = isset($_POST['venue_lon']) ? sanitize_text_field($_POST['venue_lon']) : '';
        $venue_url = isset($_POST['venue_url']) ? esc_url($_POST['venue_url']) : '';
        // Oraganizer Deatails
        $organizer_name = isset($_POST['organizer_name']) ? sanitize_text_field($_POST['organizer_name']) : '';
        $organizer_email = isset($_POST['organizer_email']) ? sanitize_text_field($_POST['organizer_email']) : '';
        $organizer_phone = isset($_POST['organizer_phone']) ? sanitize_text_field($_POST['organizer_phone']) : '';
        $organizer_url = isset($_POST['organizer_url']) ? esc_url($_POST['organizer_url']) : '';
        // Save Event Data
        // Date & Time
        update_post_meta($post_id, 'event_start_date', $event_start_date);
        update_post_meta($post_id, 'event_start_hour', $event_start_hour);
        update_post_meta($post_id, 'event_start_minute', $event_start_minute);
        update_post_meta($post_id, 'event_start_meridian', $event_start_meridian);
        update_post_meta($post_id, 'event_end_date', $event_end_date);
        update_post_meta($post_id, 'event_end_hour', $event_end_hour);
        update_post_meta($post_id, 'event_end_minute', $event_end_minute);
        update_post_meta($post_id, 'event_end_meridian', $event_end_meridian);
        update_post_meta($post_id, 'start_ts', $start_ts);
        update_post_meta($post_id, 'end_ts', $end_ts);
        // Venue
        update_post_meta($post_id, 'venue_name', $venue_name);
        update_post_meta($post_id, 'venue_address', $venue_address);
        update_post_meta($post_id, 'venue_city', $venue_city);
        update_post_meta($post_id, 'venue_state', $venue_state);
        update_post_meta($post_id, 'venue_country', $venue_country);
        update_post_meta($post_id, 'venue_zipcode', $venue_zipcode);
        update_post_meta($post_id, 'venue_lat', $venue_lat);
        update_post_meta($post_id, 'venue_lon', $venue_lon);
        update_post_meta($post_id, 'venue_url', $venue_url);
        // Organizer
        update_post_meta($post_id, 'organizer_name', $organizer_name);
        update_post_meta($post_id, 'organizer_email', $organizer_email);
        update_post_meta($post_id, 'organizer_phone', $organizer_phone);
        update_post_meta($post_id, 'organizer_url', $organizer_url);
    }
    /**
     * render events lisiting.
     */
    public function events_archive($atts = array())
    {
        $current_date = current_time('timestamp');
        if (is_front_page()) {
            $paged = (get_query_var('page') ? get_query_var('page') : 1);
        }
        $eve_args = array('post_type' => 'events', 'post_status' => 'publish', 'meta_key' => 'start_ts', 'paged' => $paged);
        // posts per page
        if (isset($atts['posts_per_page']) && $atts['posts_per_page'] != '' && is_numeric($atts['posts_per_page'])) {
            $eve_args['posts_per_page'] = $atts['posts_per_page'];
        }
        // Past Events
        if ((isset($atts['start_date']) && $atts['start_date'] != '') || (isset($atts['end_date']) && $atts['end_date'] != '')) {
            $start_date_str = $end_date_str = '';
            if (isset($atts['start_date']) && $atts['start_date'] != '') {
                try {
                    $start_date_str = strtotime(sanitize_text_field($atts['start_date']));
                } catch (Exception $e) {
                    $start_date_str = '';
                }
            }
            if (isset($atts['end_date']) && $atts['end_date'] != '') {
                try {
                    $end_date_str = strtotime(sanitize_text_field($atts['end_date']));
                } catch (Exception $e) {
                    $end_date_str = '';
                }
            }
            if ($start_date_str != '' && $end_date_str != '') {
                $eve_args['meta_query'] = array('relation' => 'AND', array('key' => 'end_ts', 'compare' => '>=', 'value' => $start_date_str), array('key' => 'start_ts', 'compare' => '<=', 'value' => $end_date_str));
            } elseif ($start_date_str != '') {
                $eve_args['meta_query'] = array(array('key' => 'end_ts', 'compare' => '>=', 'value' => $start_date_str));
            } elseif ($end_date_str != '') {
                $eve_args['meta_query'] = array('relation' => 'AND', array('key' => 'end_ts', 'compare' => '>=', 'value' => strtotime(date('Y-m-d'))), array('key' => 'start_ts', 'compare' => '<=', 'value' => $end_date_str));
            }
        } else {
            if (isset($atts['past_events']) && $atts['past_events'] == 'yes') {
                $eve_args['meta_query'] = array(array('key' => 'end_ts', 'compare' => '<=', 'value' => $current_date));
            } else {
                $eve_args['meta_query'] = array(array('key' => 'end_ts', 'compare' => '>=', 'value' => $current_date));
            }
        }
        // Category
        if (isset($atts['category']) && $atts['category'] != '') {
            $categories = explode(',', $atts['category']);
            $tax_field = 'slug';
            if (is_numeric(implode('', $categories))) {
                $tax_field = 'term_id';
            }
            if (!empty($categories)) {
                $eve_args['tax_query'] = array(array('taxonomy' => $this->event_category, 'field' => $tax_field, 'terms' => $categories));
            }
        }
        // Order by
        if (isset($atts['orderby']) && $atts['orderby'] != '') {
            if ($atts['orderby'] == 'event_start_date' || $atts['orderby'] == 'event_end_date') {
                if ($atts['orderby'] == 'event_end_date') {
                    $eve_args['meta_key'] = 'end_ts';
                }
                $eve_args['orderby'] = 'meta_value';
            } else {
                $eve_args['orderby'] = sanitize_text_field($atts['orderby']);
            }
        } else {
            $eve_args['orderby'] = 'meta_value';
        }
        // Order
        if (isset($atts['order']) && $atts['order'] != '') {
            if (strtoupper($atts['order']) == 'DESC' || strtoupper($atts['order']) == 'ASC') {
                $eve_args['order'] = sanitize_text_field($atts['order']);
            }
        } else {
            if (isset($atts['past_events']) && $atts['past_events'] == 'yes' && $eve_args['orderby'] == 'meta_value') {
                $eve_args['order'] = 'DESC';
            } else {
                $eve_args['order'] = 'ASC';
            }
        }
        $col = 3;
        $css_class = 'col-ed-md-4';
        if (isset($atts['col']) && $atts['col'] != '' && is_numeric($atts['col'])) {
            $col = $atts['col'];
            switch ($col) {
                case '1':
                    $css_class = 'col-ed-md-12';
                    break;
                case '2':
                    $css_class = 'col-ed-md-6';
                    break;
                case '3':
                    $css_class = 'col-ed-md-4';
                    break;
                case '4':
                    $css_class = 'col-ed-md-3';
                    break;
                default:
                    $css_class = 'col-ed-md-4';
                    break;
            }
        }
        $events_display = new WP_Query($eve_args);
        $wp_list_events = '';
        /* Start the Loop */
        if (is_front_page()) {
            $curr_paged = $paged;
            global $paged;
            $temp_paged = $paged;
            $paged = $curr_paged;
        }
        $ed_options = get_option(ED_OPTIONS);
        $direct_link = isset($ed_options['direct_link']) ? $ed_options['direct_link'] : 'no';
        ?>
<div class="ed_archive row_grid">
  <?php
$template_args = array();
        $template_args['css_class'] = $css_class;
        $template_args['direct_link'] = $direct_link;
        if ($events_display->have_posts()):
            while ($events_display->have_posts()):
                $events_display->the_post();
                get_ed_template('ed-archive-content.php', $template_args);
            endwhile; // End of the loop.
            if ($events_display->max_num_pages > 1): // custom pagination

                ?>
  <div class="col-ed-md-12">
    <nav class="prev-next-posts">
      <div class="prev-posts-link alignright">
        <?php echo get_next_posts_link('Next Events &raquo;', $events_display->max_num_pages); ?>
      </div>
      <div class="next-posts-link alignleft">
        <?php echo get_previous_posts_link('&laquo; Previous Events'); ?>
      </div>
    </nav>
  </div>
  <?php
    endif;
        else:
            echo apply_filters('ed_no_events_found_message', __('There are no upcoming Events at this time.', 'import-events'));
        endif;
        ?>
  <?php
do_action('ed_after_event_list', $events_display);
        $wp_list_events = ob_get_contents();
        ob_end_clean();
        wp_reset_postdata();
        if (is_front_page()) {
            global $paged;
            $paged = $temp_paged;
        }
        return $wp_list_events;
    }
    public function events_archive_with_filter()
    {
        ?>
  <section class="section events-listing-block">
    <div class="container">
      <div class="listing-filter-block events-filter-block">
        <div class="row d-flex justify-content-center">
          <div class="col">
            <div class="col-wrap">
              <label for="locations">Event Type</label>
              <div class="select-wrap">
                <select id="event-type">
                  <option value="all" selected> All Type</option>
                  <?php
$args = array('taxonomy' => 'events_type', 'hide_empty' => false);
        $categories = get_terms($args);
        if ($categories):
            foreach ($categories as $category) {
                ?>
                  <option value="<?php echo $category->slug; ?>">
                    <?php echo $category->name; ?>
                  </option>
                  <?php
    }
        endif;?>
                </select>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="col-wrap">
              <label for="events">Month</label>
              <div class="select-wrap">
                <select id="event-month">
                  <option value="all" selected>Month</option>
                  <?php
$args = array('post_type' => 'events', 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'ASC');
        $loop_one = new WP_Query($args);
        if ($loop_one->have_posts()) {
            $date_array = array();
            while ($loop_one->have_posts()):
                $loop_one->the_post();
                $start_date = get_post_meta(get_the_ID(), 'event_start_date', true);
                $start_date_str = get_post_meta(get_the_ID(), 'start_ts', true);
                $end_date_str = get_post_meta(get_the_ID(), 'end_ts', true);
                $start_date_formated = date_i18n('F j', $start_date_str);
                if ($start_date_formated && !in_array($start_date_formated, $date_array)) {
                    array_push($date_array, $month['name']);
                    $date_array[] = $start_date_formated;
                    ?>
                  <option value="<?php echo $start_date; ?>"><?php echo $start_date_formated; ?></option>
                  <?php
    }
                wp_reset_postdata();
            endwhile;
        }?>
                  <!--option value="in-person">In Person</option-->
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="events-listing">
        <div class="row d-flex" data-js-filter="target">
          <?php
$args = array('post_type' => 'events', 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'ASC');
        $loop = new WP_Query($args);
        while ($loop->have_posts()):
            $loop->the_post();
            $venue['state'] = get_post_meta(get_the_ID(), 'venue_state', true);
            $venue_address = get_post_meta(get_the_ID(), 'venue_address', true);
            $start_date_str = get_post_meta(get_the_ID(), 'start_ts', true);
            $start_date_formated = date_i18n('F j Y', $start_date_str);
            $start_time = date_i18n('G:i', $start_date_str);
            $website = get_post_meta(get_the_ID(), 'iee_event_link', true);
            $event_url = get_post_meta(get_the_ID(), 'iee_event_link', true);
            $iee_options = get_option(IEE_OPTIONS);
            $time_format = isset($iee_options['time_format']) ? $iee_options['time_format'] : '12hours';
            if ($time_format == '12hours') {
                $start_time = date_i18n('h:i a', $start_date_str);
            } elseif ($time_format == '24hours') {
            $start_time = date_i18n('G:i', $start_date_str);
        } else {
            $start_time = date_i18n(get_option('time_format'), $start_date_str);
        }
        ?>
          <div class="col card-event">
            <div class="card-wrap">
              <div class="img-wrap">
                <?php
if (get_the_post_thumbnail()) {?>
                <a href="<?php echo $event_url; ?>"> <?php the_post_thumbnail('eventlisting-size');?>
                </a>
                <?php
} else {?>
                <a href="<?php echo $event_url; ?>"><img
                    src="<?php echo ED_PLUGIN_URL . 'assets/images/dummy-event.png'; ?>" width="408" height="209"></a>
                <?php
}
        ?>
              </div>
              <div class="text">
                <h3>
                  <a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
                </h3>
                <ul>
                  <li class="date"><?php echo $start_date_formated; ?></li>
                  <li class="time"><?php echo $start_time; ?></li>
                </ul>


                <?php
if ($button_text) {
            echo '<div class="btn-wrap">
                                    <a href="' . esc_url($website) . '" class="btn btn-border" target=”_blank”>' . $button_text . '</a>';
            echo '</div>';
        }
        ?>
              </div>
            </div>

          </div>
          <?php
endwhile;
        wp_reset_postdata();
        ?>
        </div>
      </div>
    </div>
  </section>

  <?php
}
    public function events_slider()
    {
        $args1 = array('post_type' => 'events', 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'ASC');
        $loop_slider = new WP_Query($args1);
        ?>
  <div class="cycle-slideshow" data-cycle-fx="scrollHorz" data-cycle-pause-on-hover="true" data-cycle-speed="200">

    <?php
while ($loop_slider->have_posts()):
            $loop_slider->the_post();
            ?>
    <a href="<?php the_permalink();?>">
      <h3><?php the_title();?></h3>
    </a>
    <img src="<?php echo get_the_post_thumbnail_url(); ?>" />

    <?php
endwhile;
        ?>
  </div>


  <?php
}
}