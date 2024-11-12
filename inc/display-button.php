<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to display the WhatsApp button
function coding_bunny_pulsante_whatsapp() {
    // Retrieve settings from the database
    $prefix = sanitize_text_field( get_option( 'coding_bunny_whatsapp_prefix', '+39' ) ); // WhatsApp prefix
    $phone_number = sanitize_text_field( get_option( 'coding_bunny_whatsapp_phone', '1234567890' ) ); // Phone number
    $message = sanitize_text_field( get_option( 'coding_bunny_whatsapp_message', 'Ciao! Vorrei maggiori informazioni su' ) ); // Default message
    $position = sanitize_text_field( get_option( 'coding_bunny_whatsapp_position', 'right' ) ); // Button position
    $icon_size = intval( get_option( 'coding_bunny_whatsapp_icon_size', 48 ) ); // Button size
    $show_on_desktop = sanitize_text_field( get_option( 'coding_bunny_whatsapp_show_on_desktop', 'no' ) ); // Show on desktop setting
    $icon_type = sanitize_text_field( get_option( 'coding_bunny_whatsapp_icon_type', 'dmm-simple-icon.svg' ) ); // Icon type
    $time_settings = get_option( 'coding_bunny_whatsapp_time_settings', [] ); // Time settings for each day
    $current_time = current_time( 'H:i' ); // Get the current time
    $current_day = strtolower( date( 'l' ) ); // Get the current day in lowercase

    // Check if the button should be displayed based on the time for the current day
    if ( isset( $time_settings[$current_day] ) ) {
        $start_time = $time_settings[$current_day]['start'];
        $end_time = $time_settings[$current_day]['end'];
    } else {
        // Default to 00:00 - 23:59 if no specific settings are found
        $start_time = '00:00';
        $end_time = '23:59';
    }

    if ( $current_time < $start_time || $current_time > $end_time ) {
        return; // Exit if not within the specified time for the current day
    }

    // Determine if the button should be displayed on mobile or desktop
    if ( wp_is_mobile() || $show_on_desktop === 'yes' ) {
        if ( ! wp_is_mobile() && $show_on_desktop !== 'yes' ) {
            return; // Exit if not on mobile and not set to show on desktop
        }

        // Set the icon URL based on the selected icon type
        if ( $icon_type === 'custom' && ! empty( get_option( 'coding_bunny_whatsapp_custom_icon_url' ) ) ) {
            $icon_url = esc_url( get_option( 'coding_bunny_whatsapp_custom_icon_url' ) ); // Custom icon URL
        } else {
            $icon_url = esc_url( plugin_dir_url( dirname( __FILE__ ) ) . 'images/' . esc_attr( $icon_type ) ); // Default icon URL
        }

        // Output the styles for the button
        ?>
        <style>
           .coding-bunny-whatsapp {
                display: block; /* Make the button a block element */
                position: fixed; /* Fix the button position */
                bottom: 20px; /* Distance from the bottom */
                <?php echo esc_attr( $position ); ?>: 20px; /* Set the side position (left or right) */
                width: <?php echo esc_attr( $icon_size ); ?>px; /* Width of the button */
                height: <?php echo esc_attr( $icon_size ); ?>px; /* Height of the button */
                border-radius: 50%; /* Make it circular */
                z-index: 99; /* Ensure it appears above other elements */
            }

           .coding-bunny-whatsapp:hover {
                opacity: 0.6; /* Change opacity on hover */
            }
        </style>

        <!-- WhatsApp link with the message -->
        <a href="https://wa.me/<?php echo esc_attr( $prefix ); ?><?php echo esc_attr( $phone_number ); ?>?text=<?php echo urlencode( $message ); ?>" class="coding-bunny-whatsapp" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php esc_attr_e( 'WhatsApp', 'dmm' ); ?>" width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>" />
        </a>
        <?php
    }
}

// Function to enqueue admin scripts
function coding_bunny_enqueue_admin_scripts() {
    // Check if we're on the settings page
    if ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) === 'coding-bunny-whatsapp-settings' ) {
        wp_enqueue_media(); // Load media uploader
        wp_enqueue_script( 'coding-bunny-admin-scripts', plugin_dir_url( dirname( __FILE__ ) ) . 'js/coding-bunny-admin.js', ['jquery'], null, true ); // Load custom admin scripts
    }
}

// Hook the admin script enqueue function to the admin_enqueue_scripts action
add_action( 'admin_enqueue_scripts', 'coding_bunny_enqueue_admin_scripts' );

// Hook the WhatsApp button function to the wp_footer action to display it in the footer
add_action( 'wp_footer', 'coding_bunny_pulsante_whatsapp' );