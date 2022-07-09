<?php

add_action('init', 'gb_block');

function gb_block() {


	wp_register_script(
		'ep-block-slider-js',
		ED_PLUGIN_URL .'/assets/js/blocks/gutenberg.js',
		['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data']
	);


	register_block_type('ep/slider', array(
		'editor_script' => 'ep-block-slider-js',
		'render_callback' => 'ep_gutenberg_slider_render',
		'attributes' => [
			'align' => ['type' => 'string', 'default' => 'center'],
			'numSlides' => ['type' => 'number', 'default' => 3]
		]
	));
}


function ep_gutenberg_slider_render($attributes, $content) {



	$postQuery = new WP_Query([
		'post_type' => 'events',
		'posts_per_page' => $attributes['numSlides'],
		'orderby'   => array(
			'date' =>'DESC',
			'menu_order'=>'ASC',
		)

	]);




	if ($postQuery->have_posts()) {
		$output = '<div class="wp-block-ep-slider align' . $attributes['align'] . '">';
		$output .= '<div class="cycle-slideshow" data-cycle-fx="scrollHorz"
    data-cycle-timeout="3000"
  data-cycle-caption="#alt-caption"
    data-cycle-caption-template="{{alt}}">';


		while ($postQuery->have_posts()) {
			$postQuery->the_post();
			if (has_post_thumbnail()) {
				$img_url = get_the_post_thumbnail_url(get_the_ID(), 'loop-thumbnail');
				$title = get_the_title();
				$output .= '<img src="' . $img_url . '" alt="'.$title.'" />';
			}

		}





		wp_reset_postdata();
		$output .= '<div id="alt-caption" class="center"></div>  </div>';
		$output .= '</div>';

		return $output;

	} else {
		return '';
	}
}
