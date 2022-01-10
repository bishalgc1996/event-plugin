<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Shortcode_Listing_Table extends WP_List_Table
{

    public function prepare_items()
    {

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage,
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'id' => __('ID', 'import-eventbrite-events'),
            'how_to_use' => __('Title', 'import-eventbrite-events'),
            'shortcode' => __('Shortcode', 'import-eventbrite-events'),
            'action' => __('Action', 'import-eventbrite-events'),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = array();

        $data[] = array(
            'id' => 1,
            'how_to_use' => 'Display All Events',
            'shortcode' => '<p class="iee_short_code">[events_display]</p>',
            'action' => '<button class="iee-btn-copy-shortcode button-primary"  data-value="[events_display]">Copy</button>',
        );
        $data[] = array(
            'id' => 2,
            'how_to_use' => 'Display with column',
            'shortcode' => '<p class="iee_short_code">[events_display col="2"]</p>',
            'action' => "<button class='iee-btn-copy-shortcode button-primary' data-value='[events_display col=\"2\"]' >Copy</button>",
        );
        $data[] = array(
            'id' => 3,
            'how_to_use' => 'Limit for display events',
            'shortcode' => '<p class="iee_short_code">[events_display posts_per_page="12"]</p>',
            'action' => "<button class='iee-btn-copy-shortcode button-primary' data-value='[events_display posts_per_page=\"12\"]' >Copy</button>",
        );
        $data[] = array(
            'id' => 4,
            'how_to_use' => 'Display Events based on order',
            'shortcode' => '<p class="iee_short_code">[events_display order="asc"]</p>',
            'action' => "<button class='iee-btn-copy-shortcode button-primary' data-value='[events_display order=\"asc\"]' >Copy</button>",
        );
        $data[] = array(
            'id' => 5,
            'how_to_use' => 'Display events based on category',
            'shortcode' => '<p class="iee_short_code" >[events_display category="cat1"]</p>',
            'action' => "<button class='iee-btn-copy-shortcode button-primary' data-value='[events_display category=\"cat1\"]' >Copy</button>",
        );
        $data[] = array(
            'id' => 6,
            'how_to_use' => 'Display Past events',
            'shortcode' => '<p class="iee_short_code">[events_display past_events="yes"]</p>',
            'action' => "<button class='iee-btn-copy-shortcode button-primary' data-value='[events_display past_events=\"yes\"]' >Copy</button>",
        );
        $data[] = array(
            'id' => 7,
            'how_to_use' => 'Display Events based on orderby',
            'shortcode' => '<p class="iee_short_code">[events_display order="asc" orderby="post_title"]</p>',
            'action' => "<button class='iee-btn-copy-shortcode button-primary' data-value='[events_display order=\"asc\" orderby=\"post_title\"]' >Copy</button>",
        );
        $data[] = array(
            'id' => 8,
            'how_to_use' => 'Full Short-code',
            'shortcode' => '<p class="iee_short_code">[events_display  col="2" posts_per_page="12" category="cat1" past_events="yes" order="desc" orderby="post_title" start_date="YYYY-MM-DD" end_date="YYYY-MM-DD"]</p>',
            'action' => "<button class='iee-btn-copy-shortcode button-primary' data-value='[events_display col=\"2\" posts_per_page=\"12\" category=\"cat1\" past_events=\"yes\" order=\"desc\" orderby=\"post_title\" start_date=\"YYYY-MM-DD\" end_date=\"YYYY-MM-DD\"]' >Copy</button>",
        );
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'how_to_use':
            case 'shortcode':
            case 'action':
                return $item[$column_name];

            default:
                return print_r($item, true);
        }
    }
}