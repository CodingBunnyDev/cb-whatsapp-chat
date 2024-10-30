<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the plugin version
define( 'CODING_BUNNY_WHATSAPP_CHAT_VERSION', '1.0.0' );

// Function to add a submenu item for licence validation
function coding_bunny_whatsapp_submenu() {
    add_submenu_page(
        'coding-bunny-whatsapp-settings', // Parent slug
        __( "Manage Licence", 'coding-bunny-whatsapp-chat' ), // Page title
        __( "Manage Licence", 'coding-bunny-whatsapp-chat' ), // Menu title
        'manage_options', // Capability required to access this menu
        'coding-bunny-whatsapp-licence', // Menu slug
        'coding_bunny_whatsapp_licence_page' // Function to display the page content
    );

    // Check if the licence is inactive
    $licence_data = get_option( 'coding_bunny_whatsapp_licence_data', [ 'key' => '', 'email' => '' ] );
    $licence_key = esc_attr( $licence_data['key'] );
    $licence_email = esc_attr( $licence_data['email'] );
    $licence_active = coding_bunny_validate_licence( $licence_key, $licence_email );

    // Add "Go Pro" menu item if the licence is inactive
    if ( !$licence_active['success'] ) {
		add_submenu_page(
		    'coding-bunny-whatsapp-settings', // Usa lo stesso slug del parent
		    __( "Go Pro", 'coding-bunny-whatsapp-settings' ), // Titolo della pagina
		    __( "Go Pro", 'coding-bunny-whatsapp-settings' ), // Titolo del menu
		    'manage_options', // Capacità richiesta
		    'coding-bunny-whatsapp-chat-pro', // Slug del menu
		    'coding_bunny_whatsapp_chat_pro_redirect' // Funzione di reindirizzamento
		);
    }
}

// Hook the coding_bunny_whatsapp_submenu function into the admin_menu action
add_action( 'admin_menu', 'coding_bunny_whatsapp_submenu' );

// Function to handle redirection to external URL
function coding_bunny_whatsapp_chat_pro_redirect() {
    if (!headers_sent()) {
        wp_safe_redirect( 'https://www.coding-bunny.com/whatsapp-chat/' );
        exit;
    }
}

// Function to add custom CSS to highlight the "Passa a Pro" menu item
function coding_bunny_whatsapp_chat_admin_styles() {
    ?>
    <style>
        #toplevel_page_coding-bunny-whatsapp-settings .wp-submenu li a[href*='coding-bunny-whatsapp-chat-pro'] {
            background-color: #00a22a;
            color: #fff;
			font-weight: bold;
        }
        #toplevel_page_coding-bunny-whatsapp-settings .wp-submenu li a[href*='coding-bunny-whatsapp-chat-pro']:hover {
            background-color: #00a22a;
            color: #fff;
        }
    </style>
    <?php
}

// Hook to add custom styles in admin
add_action( 'admin_head', 'coding_bunny_whatsapp_chat_admin_styles' );


// Function to display the licence validation page content
function coding_bunny_whatsapp_licence_page() {
    $licence_data = get_option('coding_bunny_whatsapp_licence_data', ['key' => '', 'email' => '']);
    $licence_key = $licence_data['key'];
    $licence_email = $licence_data['email'];
	$licence_active = coding_bunny_validate_licence( $licence_key, $licence_email );

    // Handle the licence validation
    if ( isset( $_POST['validate_licence'] ) ) {
        $licence_key = sanitize_text_field( $_POST['licence_key'] );
        $licence_email = sanitize_email( $_POST['licence_email'] );
        $response = coding_bunny_validate_licence( $licence_key, $licence_email );

        if ( $response['success'] ) {
            // Save the valid licence key and email in the database
            update_option( 'coding_bunny_whatsapp_licence_data', [ 'key' => $licence_key, 'email' => $licence_email ] );
            echo '<div class="notice notice-success"><p>' . __( "Licence successfully validated!", 'coding-bunny-whatsapp-chat' ) . '</p></div>';
            echo '<script>setTimeout(function(){ location.reload(); }, 1000);</script>'; // Reload the page after 1 second
        } else {
            echo '<div class="notice notice-error"><p>' . __( "Incorrect licence key or email: ", 'coding-bunny-whatsapp-chat' ) . esc_html( $response['error'] ) . '</p></div>';
        }
    }

    // Handle the licence deactivation
    if ( isset( $_POST['deactivate_licence'] ) ) {
        delete_option( 'coding_bunny_whatsapp_licence_data' );
        $licence_key = '';
        $licence_email = '';
        echo '<div class="notice notice-success"><p>' . __( "Licence successfully deactivated!", 'coding-bunny-whatsapp-chat' ) . '</p></div>';
        echo '<script>setTimeout(function(){ location.reload(); }, 1000);</script>'; // Reload the page after 1 second
    }

    ?>
  <div class="wrap coding-bunny-whatsapp-chat-wrap">
    <h1><?php esc_html_e( 'CodingBunny WhatsApp Chat', 'coding-bunny-whatsapp-chat' ); ?> 
       <span style="font-size: 10px;">v<?php echo CODING_BUNNY_WHATSAPP_CHAT_VERSION; ?></span></h1>
    <h3><?php esc_html_e( "Manage Licence", 'coding-bunny-whatsapp-chat' ); ?></h3>
    <form method="post" action="">
        <div class="coding-bunny-flex-container-licence">
            <div class="coding-bunny-flex-item-licence">
                <label for="licence_email"><?php _e( "Email account:", 'coding-bunny-whatsapp-chat' ); ?></label>
            </div>
            <div class="coding-bunny-flex-item-licence">
                <input type="email" id="licence_email" name="licence_email" value="<?php echo esc_attr( $licence_email ); ?>" required />
            </div>
            <div class="coding-bunny-flex-item-licence">
                <label for="licence_key"><?php _e( "Licence Key:", 'coding-bunny-whatsapp-chat' ); ?></label>
            </div>
            <div class="coding-bunny-flex-item-licence">
                <input type="text" id="licence_key" name="licence_key" 
                    value="<?php echo $licence_active['success'] ? str_repeat('*', strlen( $licence_key )) : esc_attr( $licence_key ); ?>" 
                    required />   
            </div>
            <div class="coding-bunny-flex-item-licence">
                <?php if ( $licence_active['success'] ) : ?>
                    <button type="submit" name="deactivate_licence" class="button button-primary">
                        <?php _e( "Deactivate licence", 'coding-bunny-whatsapp-chat' ); ?>
                    </button>
                <?php else : ?>
                    <button type="submit" name="validate_licence" class="button button-primary">
                        <?php _e( "Activate licence", 'coding-bunny-whatsapp-chat' ); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php if ( $licence_active['success'] ) : ?>
            <div style="margin-top: 20px;">
                <div style="margin-top: 20px; font-weight: bold;">
                    <span style="color: green;">&#x25CF;</span> <?php esc_html_e( "Licence active", 'coding-bunny-whatsapp-chat' ); ?>
                </div><br>
                <?php esc_html_e( "Your licence expires on:", 'coding-bunny-whatsapp-chat' ); ?>
                <span style="font-weight: bold;">
                    <?php 
                        // Format the expiration date
                        $expiration_date = DateTime::createFromFormat( 'Y-m-d', $licence_active['expiration'] );
                        echo esc_html( $expiration_date->format( 'd-m-Y' ) ); 
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </form>
    <p>
        <?php esc_html_e( "Having problems with your licence? Contact our support: ", 'coding-bunny-whatsapp-chat' ); ?>
        <a href="mailto:support@coding-bunny.com">support@coding-bunny.com</a>
    </p>
    <hr>
    <p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> - <?php esc_html_e( 'Powered by CodingBunny', 'coding-bunny-whatsapp-chat' ); ?></p>
</div>
    <?php
}

// Function to validate the licence key
function coding_bunny_validate_licence( $licence_key, $licence_email ) {
    $url = 'https://www.coding-bunny.com/plugins-licence/wc-active-licence.php';

    $response = wp_remote_post( $url, array(
        'body' => json_encode( array( 'licence_key' => $licence_key, 'email' => $licence_email ) ),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'timeout' => 15,
        'sslverify' => true,
    ));

    if ( is_wp_error( $response ) ) {
        return array( 'success' => false, 'error' => $response->get_error_message() );
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( isset( $body['success'] ) && $body['success'] ) {
        return array( 'success' => true, 'expiration' => $body['expiration'] ); // Get expiration date from server response
    } else {
        return array( 'success' => false, 'error' => isset( $body['error'] ) ? $body['error'] : __( "Incorrect licence key or email", 'coding-bunny-whatsapp-chat' ) );
    }
}

// Function to show the warning notice on the dashboard
function coding_bunny_licence_expiration_notice() {
    $licence_data = get_option('coding_bunny_whatsapp_licence_data', ['key' => '', 'email' => '']);
    $licence_key = $licence_data['key'];
    $licence_email = $licence_data['email'];
    $licence_active = coding_bunny_validate_licence( $licence_key, $licence_email );

    if ( $licence_active['success'] ) {
        $expiration_date = DateTime::createFromFormat('Y-m-d', $licence_active['expiration']);
        $current_date = new DateTime();
        $days_until_expiration = $expiration_date->diff( $current_date )->days;

        if ( $days_until_expiration <= 30 && $days_until_expiration > 0 ) {
    add_action( 'admin_notices', function() use ( $days_until_expiration ) {
        echo '<div class="notice notice-warning is-dismissible"><p>' . 
            sprintf( 
                __( 'Your <b>CodingBunny WhatsApp Chat</b> licence expires in <b>%d days</b>! <a href="%s">Renew now.</a>', 'coding-bunny-whatsapp-chat' ), 
                $days_until_expiration, 
                esc_url( 'mailto:support@coding-bunny.com' ) 
            ) . 
        '</p></div>';
            });
        }
    }
}

// Hook the coding_bunny_licence_expiration_notice function into the admin_init action
add_action( 'admin_init', 'coding_bunny_licence_expiration_notice' );