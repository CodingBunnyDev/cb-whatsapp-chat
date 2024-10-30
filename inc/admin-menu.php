<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to add a menu item to the WordPress admin
function coding_bunny_whatsapp_menu() {
    // Add a new top-level menu page
    add_menu_page(
        __( "CodingBunny WhatsApp Chat", 'coding-bunny-whatsapp-chat' ), // Page title
        __( "WhatsApp Chat", 'coding-bunny-whatsapp-chat' ), // Menu title
        'manage_options', // Capability required
        'coding-bunny-whatsapp-settings', // Menu slug
        'coding_bunny_whatsapp_settings_page', // Callback function
        'dashicons-whatsapp', // Menu icon
        20 // Menu position
    );
}

// Hook the dmm_whatsapp_menu function into the admin_menu action
add_action( 'admin_menu', 'coding_bunny_whatsapp_menu' );