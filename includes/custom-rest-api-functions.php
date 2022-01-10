<?php

add_action('rest_api_init', function () {
    register_rest_route('event-plugin/v1', 'posts', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'wl_posts',
    ]);
});

function wl_posts($request)
{
    $args = [];
    if (isset($request['per_page'])) {
        $args['posts_per_page'] = (int) $request['per_page'];
    } else {
        $args['posts_per_page'] = 3;
    }
    if (isset($request['page'])) {
        $args['page'] = (int) $request['page'];
    } else {
        $args['page'] = 1;
    }

    $args['post_type'] = 'events';
    if ($args['page']) {
        $args['offset'] = ($args['page'] - 1) * $args['posts_per_page'];
    }

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

    return $data;

    $json_data = array();

    foreach ($posts as $post) {

    }

    $pagination_output = paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $get_posts->max_num_pages,
    ));

}