<?php

add_action('rest_api_init', function () {
    register_rest_route('event-plugin/v1', 'posts', [
        'methods' => 'GET',
        'callback' => 'wl_posts',
        'args' => [
            'page' => [
                'description' => 'Current page',
                'type' => "integer",
            ],
            'per_page' => [
                'description' => 'Items per page',
                'type' => "integer",
            ],
        ],
    ]);
});

function wl_posts()
{
    $args = [];

    $big = 999999999; // need an unlikely integer

    if (isset($_REQUEST['per_page'])) {
        $args['posts_per_page'] = (int) $_REQUEST['per_page'];
    } else {
        $args['posts_per_page'] = 10;
    }
    if (isset($_REQUEST['page'])) {
        $args['page'] = (int) $_REQUEST['page'];
    }

    //Protect against arbitrary paged values
    $paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

    $args = array(
        'paged' => $paged,
    );

    $args['post_type'] = 'events';

    $get_posts = new WP_Query;
    $posts = $get_posts->query($args);

    $data = [];
    $i = 0;

    foreach ($posts as $post) {
        $data[$i]['id'] = $post->ID;
        $data[$i]['title'] = $post->post_title;
        $data[$i]['content'] = $post->post_content;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['featured_image']['thumbnail'] =
            get_the_post_thumbnail_url($post->ID, 'thumbnail');

        $i++;

    }

    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $get_posts->max_num_pages,
    ));

    return $data;
}