<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
$ShortcodeTable = new Shortcode_Listing_Table();
$ShortcodeTable->prepare_items();

?>
<div class="iee_container">
  <div class="iee_row">
    <h3 class="setting_bar"><?php esc_attr_e('Eventdisplay Shortcodes', 'import-events');?></h3>
    <?php $ShortcodeTable->display();?>
  </div>
</div>