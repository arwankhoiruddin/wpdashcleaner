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

/**
 * Callback function to render the "WDC Settings" page.
 */
function wdc_settings_page() {
    ?>
    <div class="wrap">
        <h1>WDC Settings</h1>
        <p>Welcome to the WDC Settings page. Configure your plugin settings here.</p>
        <!-- Add your settings form or content here -->
    </div>
    <?php
}
