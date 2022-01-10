<?php
// If this file is called directly, abort.
// Icon Credit: Icon made by Freepik and Vectors Market from www.flaticon.com
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="ed-support-features">
  <div class="ed-support-features-card">
    <div class="ed-support-features-img">
    </div>
    <div class="ed-support-features-text">
      <h3 class="ed-support-features-title">
        <?php esc_attr_e('End Point Url:', 'import-events');?>
        <a href="<?php echo home_url() . '/wp-json/event-plugin/v1/posts'; ?>">
          <?php echo home_url() . '/wp-json/event-plugin/v1/posts'; ?></a>
      </h3>
      <h3 class="ed-support-features-title">
        <?php esc_attr_e('Parameter - page:', 'import-events');?>
        <a href="<?php echo home_url() . '/wp-json/event-plugin/v1/posts?page=2'; ?>">
          <?php echo home_url() . '/wp-json/event-plugin/v1/posts?page=2'; ?></a>
      </h3>
    </div>
  </div>
</div>