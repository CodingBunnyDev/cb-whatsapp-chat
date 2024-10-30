<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to enqueue admin styles for the WhatsApp settings page
function coding_bunny_enqueue_admin_styles() {
    // Check if we're on the settings page for the WhatsApp plugin
    if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'coding-bunny-whatsapp-settings' || $_GET['page'] === 'coding-bunny-whatsapp-licence' ) ) {
        // Get the version of the CSS file based on its last modified time
        $version = filemtime( plugin_dir_path( __FILE__ ) . '../css/coding-bunny-whatsapp-chat.css' );

        // Enqueue the CSS file for admin styles
        wp_enqueue_style( 'coding-bunny-admin-styles', plugin_dir_url( __FILE__ ) . '../css/coding-bunny-whatsapp-chat.css', [], $version );
    }
}

// Hook the dmm_enqueue_admin_styles function into the admin_enqueue_scripts action
add_action( 'admin_enqueue_scripts', 'coding_bunny_enqueue_admin_styles' );

// Function to enqueue additional admin scripts for the WhatsApp settings page
function coding_bunny_enqueue_admin_scripts_2( $hook ) {
    // Check if the current page is the settings page for the WhatsApp plugin
    if ( $hook !== 'settings_page_coding-bunny-whatsapp-chat' ) {
        return; // Exit if not on the correct settings page
    }

    // Enqueue the JavaScript file for admin functionality
    wp_enqueue_script(
        'coding-bunny-admin-js', // Script handle
        plugin_dir_url( __FILE__ ) . '../js/coding-bunny-admin.js', // Path to the script
        array( 'jquery' ), // Dependencies (jQuery)
        '1.0', // Version of the script
        true // Load in footer
    );
}

// Hook the coding_bunny_enqueue_admin_scripts_2 function into the admin_enqueue_scripts action
add_action( 'admin_enqueue_scripts', 'coding_bunny_enqueue_admin_scripts_2' );