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
    register_setting('wdc_settings_group', 'wdc_hide_add_post');
    register_setting('wdc_settings_group', 'wdc_hide_comments');
    register_setting('wdc_settings_group', 'wdc_hide_posts');
    register_setting('wdc_settings_group', 'wdc_hide_pages');
    register_setting('wdc_settings_group', 'wdc_hide_media');
}

/**
 * Callback function to render the "WDC Settings" page.
 */
function wdc_settings_page() {
    ?>
    <div class="wrap">
        <h1>WDC Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wdc_settings_group'); ?>
            <?php do_settings_sections('wdc_settings_group'); ?>
            <h2>Top</h2>
            <label>
                <input type="checkbox" name="wdc_hide_add_post" value="1" <?php checked(1, get_option('wdc_hide_add_post'), true); ?>>
                Hide new post button
            </label><br>
            <label>
                <input type="checkbox" name="wdc_hide_comments" value="1" <?php checked(1, get_option('wdc_hide_comments'), true); ?>>
                Hide comments button
            </label>
            <h2>Side</h2>
            <label>
                <input type="checkbox" name="wdc_hide_posts" value="1" <?php checked(1, get_option('wdc_hide_posts'), true); ?>>
                Hide posts
            </label><br>
            <label>
                <input type="checkbox" name="wdc_hide_pages" value="1" <?php checked(1, get_option('wdc_hide_pages'), true); ?>>
                Hide pages
            </label><br>
            <label>
                <input type="checkbox" name="wdc_hide_media" value="1" <?php checked(1, get_option('wdc_hide_media'), true); ?>>
                Hide media
            </label><br><br>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
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
    if (get_option('wdc_hide_add_post')) {
        $wp_admin_bar->remove_node('new-content');
    }
    if (get_option('wdc_hide_comments')) {
        $wp_admin_bar->remove_node('comments');
    }
}

/**
 * Hide elements in the admin menu based on settings for non-admin users.
 */
function wdc_modify_admin_menu() {
    if (current_user_can('manage_options')) {
        return; // Do nothing for admin users
    }
    if (get_option('wdc_hide_posts')) {
        remove_menu_page('edit.php');
    }
    if (get_option('wdc_hide_pages')) {
        remove_menu_page('edit.php?post_type=page');
    }
    if (get_option('wdc_hide_media')) {
        remove_menu_page('upload.php');
    }
}
