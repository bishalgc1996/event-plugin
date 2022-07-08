<?php
add_action( 'wp_ajax_nopriv_filter', 'filter_ajax' );
add_action( 'wp_ajax_filter', 'filter_ajax' );
function filter_ajax() {
	$cf  = $_POST['customfield'];
	$cfd = $_POST['customfieldone'];


	$args = array(
		'post_type'     => 'events',
		'post_status'   => 'publish',
		'post_per_page' => - 1,
		'orderby'       => 'date',
		'order'         => 'ASC',
	);
	if ( ( $cf && $cf != 'all' ) || ( $cfd && $cfd != 'all' ) ) {
		$args['relation'] = 'AND';

		if ( $cf != 'all' ) {
			$args['tax_query'][] = array(
				array(
					'taxonomy' => 'events_type',
					'field'    => 'slug',
					'terms'    => $cf,
				),
			);

		}
		if ( $cfd != 'all' ) {
			$args['meta_query']['cfd'] = array(
				'key'   => 'event_start_date',
				'value' => $cfd,
			);

		}
	}

	$query = new WP_Query( $args );
	while ( $query->have_posts() ): $query->the_post();
		$venue_address       = get_post_meta( get_the_ID(), 'venue_address',
			true );
		$start_date_str      = get_post_meta( get_the_ID(), 'start_ts', true );
		$start_date_formated = date_i18n( 'F j Y', $start_date_str );
		$start_time          = date_i18n( 'G:i', $start_date_str );
		$website             = get_post_meta( get_the_ID(), 'iee_event_link',
			true );
		$event_url           = get_post_meta( get_the_ID(), 'iee_event_link',
			true );
		$iee_options         = get_option( ED_OPTIONS );
		$time_format         = isset( $iee_options['time_format'] )
			? $iee_options['time_format'] : '12hours';
		if ( $time_format == '12hours' ) {
			$start_time = date_i18n( 'h:i a', $start_date_str );
		} elseif ( $time_format == '24hours' ) {
			$start_time = date_i18n( 'G:i', $start_date_str );
		} else {
			$start_time = date_i18n( get_option( 'time_format' ),
				$start_date_str );
		}
		?>
        <div class="col card-event">
            <div class="card-wrap">
                <div class="img-wrap">
					<?php if ( get_the_post_thumbnail() ) { ?>
                        <a href="<?php echo $event_url; ?>"> <?php the_post_thumbnail( 'eventlisting-size' ); ?>
                        </a>
						<?php
					} else { ?>
                        <a href="<?php echo $event_url; ?>"><img
                                    src="<?php echo ED_PLUGIN_URL
									                . 'assets/images/dummy-event.png'; ?>"
                                    width="408" height="209"></a>
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

                </div>
            </div>

        </div>
	<?php

	endwhile;
	wp_reset_postdata();
	die();
}


