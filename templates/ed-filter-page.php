<?php

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
                $start_date_formated = date_i18n('F j', $start_date_str);
                if ($start_date_formated && !in_array($start_date_formated, $date_array)) {
                    $date_array[] = $start_date_formated;
                    ?>
                  <option value="<?php echo $start_date; ?>"><?php echo $start_date_formated; ?></option>
                  <?php
    }
                wp_reset_postdata();
            endwhile;
        }?>
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
            $start_date_str = get_post_meta(get_the_ID(), 'start_ts', true);
            $start_date_formated = date_i18n('F j Y', $start_date_str);
            $event_url = get_post_meta(get_the_ID(), 'ed_event_link', true);
            $ed_options = get_option(ED_OPTIONS);
            $time_format = isset($ed_options['time_format']) ? $ed_options['time_format'] : '12hours';
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
                <a href="<?php echo $event_url; ?>">
                <img
                    src="<?php echo ED_PLUGIN_URL . 'assets/images/dummy-event.png'; ?>" width="408" height="209" alt=""></a>
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
        ?>
        </div>
      </div>
    </div>
  </section>
