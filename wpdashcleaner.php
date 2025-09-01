<?php
/*
Plugin Name: WP Dash Cleaner
Description: A plugin to clean up the WordPress dashboard by removing unnecessary widgets and elements.
Version: 1.0
Author: Your Name
*/

// Hook to add admin menu
add_action('admin_menu', 'wdc_add_admin_menu');

/**
 * Register the "WDC Settings" menu in the WordPress admin.
 */
function wdc_add_admin_menu() {
    add_menu_page(
        'WDC Settings',       // Page title
        'WDC Settings',       // Menu title
        'manage_options',     // Capability
        'wdc-settings',       // Menu slug
        'wdc_settings_page',  // Callback function
        'dashicons-admin-generic', // Icon
        20                    // Position
    );
}

// Hook to initialize settings
add_action('admin_init', 'wdc_register_settings');

/**
 * Register settings for WDC plugin.
 */
function wdc_register_settings() {
    $settings = [
        'wdc_hide_add_post',
        'wdc_hide_comments',
        'wdc_hide_posts',
        'wdc_hide_media',
        'wdc_hide_tools' // Added wdc_hide_tools
    ];

    foreach ($settings as $setting) {
        if (isset($_POST[$setting])) {
            wdc_update_setting($setting, '1');
        } else {
            wdc_update_setting($setting, '0');
        }
    }
}

/**
 * Callback function to render the "WDC Settings" page.
 */
function wdc_settings_page() {
    ?>
    <div class="wrap">
        <h1>WDC Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('wdc_settings_save', 'wdc_settings_nonce'); ?>
            <h2>Top</h2>
            <label>
                <input type="checkbox" name="wdc_hide_add_post" value="1" <?php checked('1', wdc_get_setting('wdc_hide_add_post'), true); ?>>
                Hide new post button
            </label><br>
            <label>
                <input type="checkbox" name="wdc_hide_comments" value="1" <?php checked('1', wdc_get_setting('wdc_hide_comments'), true); ?>>
                Hide comments button
            </label>
            <h2>Side</h2>
            <label>
                <input type="checkbox" name="wdc_hide_posts" value="1" <?php checked('1', wdc_get_setting('wdc_hide_posts'), true); ?>>
                Hide posts
            </label><br>
            <label>
                <input type="checkbox" name="wdc_hide_comments" value="1" <?php checked('1', wdc_get_setting('wdc_hide_comments'), true); ?>>
                Hide comments
            </label><br>
            <label>
                <input type="checkbox" name="wdc_hide_tools" value="1" <?php checked('1', wdc_get_setting('wdc_hide_tools'), true); ?>>
                Hide tools
            </label><br>
            <label>
                <input type="checkbox" name="wdc_hide_media" value="1" <?php checked('1', wdc_get_setting('wdc_hide_media'), true); ?>>
                Hide media
            </label><br><br>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('wdc_settings_save', 'wdc_settings_nonce')) {
        wdc_register_settings();
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
}

// Hook for plugin activation
register_activation_hook(__FILE__, 'wdc_create_settings_table');

/**
 * Create the "wdc_settings" table during plugin activation.
 */
function wdc_create_settings_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wdc_settings';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        setting_key VARCHAR(255) NOT NULL,
        setting_value VARCHAR(255) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY setting_key (setting_key)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Get a setting value from the "wdc_settings" table.
 */
function wdc_get_setting($key) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wdc_settings';
    $result = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $table_name WHERE setting_key = %s", $key));
    return $result;
}

/**
 * Update or insert a setting in the "wdc_settings" table.
 */
function wdc_update_setting($key, $value) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wdc_settings';
    $wpdb->replace($table_name, ['setting_key' => $key, 'setting_value' => $value], ['%s', '%s']);
}

// Hook to modify admin bar and menu
add_action('admin_bar_menu', 'wdc_modify_admin_bar', 999);
add_action('admin_menu', 'wdc_modify_admin_menu', 999);

/**
 * Hide elements in the admin bar based on settings for non-admin users.
 */
function wdc_modify_admin_bar($wp_admin_bar) {
    if (current_user_can('manage_options')) {
        return; // Do nothing for admin users
    }
    if (wdc_get_setting('wdc_hide_add_post') === '1') {
        $wp_admin_bar->remove_node('new-content');
        remove_menu_page('post-new.php');
    }
    if (wdc_get_setting('wdc_hide_comments') === '1') {
        $wp_admin_bar->remove_node('comments');
        remove_menu_page('edit-comments.php');
    }
    if (wdc_get_setting('wdc_hide_tools') === '1') {
        remove_menu_page('tools.php');
    }
}

/**
 * Hide elements in the admin menu based on settings for non-admin users.
 */
function wdc_modify_admin_menu() {
    if (current_user_can('manage_options')) {
        return; // Do nothing for admin users
    }
    if (wdc_get_setting('wdc_hide_posts') === '1') {
        remove_menu_page('edit.php');
    }
    if (wdc_get_setting('wdc_hide_media') === '1') {
        remove_menu_page('upload.php');
    }
    if (wdc_get_setting('wdc_hide_tools') === '1') { // Added handling for wdc_hide_tools
        remove_menu_page('tools.php');
    }
}
