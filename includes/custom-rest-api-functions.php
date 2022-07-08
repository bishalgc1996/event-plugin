<?php

/**
 * This is our callback function that embeds our resource in a WP_REST_Response
 */
function prefix_get_private_data_permissions_check() {

	return true;
}

add_action('rest_api_init', function () {
    register_rest_route('event-plugin/v1', 'posts', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'wl_posts',
	    // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
        'permission_callback' => 'prefix_get_private_data_permissions_check',
    ]);
});

function wl_posts($request)
{
    $args = [];
    if (isset($request['per_page'])) {
        $args['posts_per_page'] = (int) $request['per_page'];
    } else {
        $args['posts_per_page'] = 5;
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
}