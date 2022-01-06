<?PHP

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Import_Events_Common
{
}
function get_ed_template($template_name, $args = array(), $template_path = 'event-plugin', $default_path =
    '') {
    if ($args && is_array($args)) {
        extract($args);
    }
    include locate_ed_template($template_name, $template_path, $default_path);
}

function locate_ed_template($template_name, $template_path = 'event-plugin', $default_path = '')
{
    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            trailingslashit($template_path) . $template_name,
            $template_name,

        )
    );
    // Get default template
    if (!$template && $default_path !== false) {
        $default_path = $default_path ? $default_path : ED_PLUGIN_DIR . '/templates/';
        if (file_exists(trailingslashit($default_path) . $template_name)) {
            $template = trailingslashit($default_path) . $template_name;
        }
    }
    // Return what we found
    return apply_filters('ed_locate_template', $template, $template_name, $template_path);
}