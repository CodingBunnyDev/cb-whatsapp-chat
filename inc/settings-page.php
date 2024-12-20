<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to check if the licence is valid
function wc_is_licence_active() {
    $licence_data = get_option( 'coding_bunny_whatsapp_licence_data', ['key' => '', 'email' => ''] );
    $licence_key = $licence_data['key'];
    $licence_email = $licence_data['email'];

    if ( empty( $licence_key ) || empty( $licence_email ) ) {
        return false;
    }

    $response = coding_bunny_validate_licence( $licence_key, $licence_email );
    return $response['success'];
}

// Function to render the WhatsApp settings page
function coding_bunny_whatsapp_settings_page() {
    // Check if the licence is active
    $licence_active = wc_is_licence_active();

    // Check if the form has been submitted
    if ( isset( $_POST['coding_bunny_whatsapp_settings'] ) ) {
        // Verify the nonce for security
        if ( ! isset( $_POST['coding_bunny_whatsapp_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coding_bunny_whatsapp_nonce'] ) ), 'coding_bunny_whatsapp_action' ) ) {
            wp_die( esc_html__( 'Security check failed', 'coding-bunny-whatsapp-chat' ) );
        }

        // Retrieve and sanitize form settings
        $settings = isset( $_POST['coding_bunny_whatsapp_settings'] ) ? wp_unslash( $_POST['coding_bunny_whatsapp_settings'] ) : [];
        $prefix = isset( $settings['prefix'] ) ? sanitize_text_field( $settings['prefix'] ) : '';
        $phone_number = isset( $settings['phone_number'] ) ? sanitize_text_field( $settings['phone_number'] ) : '';
        $message = isset( $settings['message'] ) ? sanitize_textarea_field( $settings['message'] ) : '';
        $position = isset( $settings['position'] ) ? sanitize_text_field( $settings['position'] ) : 'right';
		$vertical_position = isset( $settings['vertical_position'] ) ? absint( $settings['vertical_position'] ) : 20;
        $icon_size = isset( $settings['icon_size'] ) ? absint( $settings['icon_size'] ) : 48;
		$icon_shadow = isset( $settings['icon_shadow'] ) ? floatval( $settings['icon_shadow'] ) : 0;
        $show_on_desktop = isset( $settings['show_on_desktop'] ) ? sanitize_text_field( $settings['show_on_desktop'] ) : 'yes';
        $icon_type = isset( $settings['icon_type'] ) ? sanitize_text_field( $settings['icon_type'] ) : 'coding-bunny-simple-icon.svg';
        $custom_icon_url = isset( $settings['custom_icon_url'] ) ? esc_url_raw( $settings['custom_icon_url'] ) : '';

        // Retrieve and sanitize time settings for each day of the week
        $time_settings = [];
        $days_of_week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ( $days_of_week as $day ) {
            $start_time = isset( $settings["start_time_$day"] ) ? sanitize_text_field( $settings["start_time_$day"] ) : '00:00';
            $end_time = isset( $settings["end_time_$day"] ) ? sanitize_text_field( $settings["end_time_$day"] ) : '23:59';
            $time_settings[$day] = ['start' => $start_time, 'end' => $end_time];
        }

        // Update options in the database
        update_option( 'coding_bunny_whatsapp_time_settings', $time_settings );
        update_option( 'coding_bunny_whatsapp_custom_icon_url', $custom_icon_url );
        update_option( 'coding_bunny_whatsapp_prefix', $prefix );
        update_option( 'coding_bunny_whatsapp_phone', $phone_number );
        update_option( 'coding_bunny_whatsapp_message', $message );
        update_option( 'coding_bunny_whatsapp_position', $position );
		update_option( 'coding_bunny_whatsapp_vertical_position', $vertical_position );
        update_option( 'coding_bunny_whatsapp_icon_size', $icon_size );
		update_option( 'coding_bunny_whatsapp_icon_shadow', $icon_shadow );
        update_option( 'coding_bunny_whatsapp_show_on_desktop', $show_on_desktop );
        update_option( 'coding_bunny_whatsapp_icon_type', $icon_type );
    }

    // Retrieve current settings from the database
    $prefix = get_option( 'coding_bunny_whatsapp_prefix', '+39' );
    $phone_number = get_option( 'coding_bunny_whatsapp_phone', '1234567890' );
    $message = get_option( 'coding_bunny_whatsapp_message', 'Ciao!' );
    $position = get_option( 'coding_bunny_whatsapp_position', 'right' );
	$vertical_position = get_option( 'coding_bunny_whatsapp_vertical_position', 20 );
    $icon_size = get_option( 'coding_bunny_whatsapp_icon_size', 48 );
	$icon_shadow = get_option( 'coding_bunny_whatsapp_icon_shadow', 0 );
    $show_on_desktop = get_option( 'coding_bunny_whatsapp_show_on_desktop', 'yes' );
    $icon_type = get_option( 'coding_bunny_whatsapp_icon_type', 'coding-bunny-simple-icon.svg' );
    $time_settings = get_option( 'coding_bunny_whatsapp_time_settings', [] );

    // Reset time settings to default if the licence is not active
    if ( !$licence_active ) {
        $time_settings = [
            'monday'    => ['start' => '00:00', 'end' => '23:59'],
            'tuesday'   => ['start' => '00:00', 'end' => '23:59'],
            'wednesday' => ['start' => '00:00', 'end' => '23:59'],
            'thursday'  => ['start' => '00:00', 'end' => '23:59'],
            'friday'    => ['start' => '00:00', 'end' => '23:59'],
            'saturday'  => ['start' => '00:00', 'end' => '23:59'],
            'sunday'    => ['start' => '00:00', 'end' => '23:59'],
        ];
        update_option( 'coding_bunny_whatsapp_time_settings', $time_settings );
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'CodingBunny WhatsApp Chat', 'coding-bunny-whatsapp-chat' ); ?> 
           <span style="font-size: 10px;">v<?php echo CODING_BUNNY_WHATSAPP_CHAT_VERSION; ?></span></h1>
        <h3><b><?php esc_html_e( "Settings Chat", 'coding-bunny-whatsapp-chat' ); ?></b></h3>
        <form method="post" action="">
            <?php wp_nonce_field( 'coding_bunny_whatsapp_action', 'coding_bunny_whatsapp_nonce' ); ?>
            <table class="form-table">

                <!-- WhatsApp Number Input -->
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( "WhatsApp Number", 'coding-bunny-whatsapp-chat' ); ?></th>
                    <td>
                        <div class="coding-bunny-whatsapp-number">
                            <input type="text" name="coding_bunny_whatsapp_settings[prefix]" value="<?php echo esc_attr( $prefix ); ?>" placeholder="<?php esc_html_e( 'Prefix', 'coding-bunny-whatsapp-chat' ); ?>" class="coding-bunny-input-prefix" />
                            <input type="number" name="coding_bunny_whatsapp_settings[phone_number]" value="<?php echo esc_attr( $phone_number ); ?>" placeholder="<?php esc_html_e( 'Phone Number', 'coding-bunny-whatsapp-chat' ); ?>" class="coding-bunny-input-phone" />
                        </div>
                        <p class="description"><?php esc_html_e( 'Enter the country code and your full phone number.', 'coding-bunny-whatsapp-chat' ); ?></p>
                    </td>
                </tr>

                <!-- Pre-filled Message Input -->
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( "Pre-filled Message", 'coding-bunny-whatsapp-chat' ); ?></th>
                    <td>
                        <input type="text" name="coding_bunny_whatsapp_settings[message]" value="<?php echo esc_attr( $message ); ?>" class="coding-bunny-input-message"/>
                        <p class="description"><?php esc_html_e( 'The message will be displayed when the user clicks on the WhatsApp button.', 'coding-bunny-whatsapp-chat' ); ?></p>
                    </td>
                </tr>
            </table>

            <!-- Visibility Settings -->
            <table class="form-table">
                <hr>
                <h3><b><?php esc_html_e( "Visibility", 'coding-bunny-whatsapp-chat' ); ?></b></h3>
                <?php
                $days = [
                    'monday'    => esc_html__( 'Monday', 'coding-bunny-whatsapp-chat' ),
                    'tuesday'   => esc_html__( 'Tuesday', 'coding-bunny-whatsapp-chat' ),
                    'wednesday' => esc_html__( 'Wednesday', 'coding-bunny-whatsapp-chat' ),
                    'thursday'  => esc_html__( 'Thursday', 'coding-bunny-whatsapp-chat' ),
                    'friday'    => esc_html__( 'Friday', 'coding-bunny-whatsapp-chat' ),
                    'saturday'  => esc_html__( 'Saturday', 'coding-bunny-whatsapp-chat' ),
                    'sunday'    => esc_html__( 'Sunday', 'coding-bunny-whatsapp-chat' ),
                ];

                foreach ( $days as $key => $day ) {
                    $start_time = isset( $time_settings[$key]['start'] ) ? $time_settings[$key]['start'] : '00:00';
                    $end_time = isset( $time_settings[$key]['end'] ) ? $time_settings[$key]['end'] : '23:59';
                    ?>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html( $day ); ?></th>
                        <td>
                            <input type="time" name="coding_bunny_whatsapp_settings[start_time_<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $start_time ); ?>" class="coding-bunny-input-time" <?php if (!$licence_active) echo 'disabled'; ?> />
                            <input type="time" name="coding_bunny_whatsapp_settings[end_time_<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $end_time ); ?>" class="coding-bunny-input-time" <?php if (!$licence_active) echo 'disabled'; ?> />
                        </td>
                    </tr>
                    <?php
                }
                ?>

            <p class="description"><?php esc_html_e( 'Sets the start and end times for each day of the week to display the WhatsApp button.', 'coding-bunny-whatsapp-chat' ); ?></p>

				<!-- Show on All Devices Settings -->
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( "Display on all devices", 'coding-bunny-whatsapp-chat' ); ?></th>
                    <td>
                        <select name="coding_bunny_whatsapp_settings[show_on_desktop]" class="coding-bunny-select-show-desktop">
                            <option value="yes" <?php selected( $show_on_desktop, 'yes' ); ?>><?php esc_html_e( 'Yes', 'coding-bunny-whatsapp-chat' ); ?></option>
                            <option value="no" <?php selected( $show_on_desktop, 'no' ); ?>><?php esc_html_e( 'No', 'coding-bunny-whatsapp-chat' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Choose whether to display the button only on smartphones and tablets or on all devices.', 'coding-bunny-whatsapp-chat' ); ?></p>
                    </td>
                </tr>
            </table>

            <hr>
            <h3><b><?php esc_html_e( "Appearance", 'coding-bunny-whatsapp-chat' ); ?></b></h3>
            <table class="form-table">

                <!-- Button Position Settings -->
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( "Position", 'coding-bunny-whatsapp-chat' ); ?></th>
                    <td>
                        <select name="coding_bunny_whatsapp_settings[position]" class="coding-bunny-select-position">
                            <option value="right" <?php selected( $position, 'right' ); ?>><?php esc_html_e( 'Right', 'coding-bunny-whatsapp-chat' ); ?></option>
                            <option value="left" <?php selected( $position, 'left' ); ?>><?php esc_html_e( 'Left', 'coding-bunny-whatsapp-chat' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Choose where to display the button on the screen.', 'coding-bunny-whatsapp-chat' ); ?></p>
                    </td>
                </tr>

				<!-- Button Vertical Position Settings -->
<tr valign="top">
    <th scope="row"><?php esc_html_e( "Vertical Offset (px)", 'coding-bunny-whatsapp-chat' ); ?></th>
    <td>
        <input 
            type="number" 
            name="coding_bunny_whatsapp_settings[vertical_position]" 
            value="<?php echo esc_attr( $vertical_position ); ?>" 
            min="10" 
            max="300" 
            class="coding-bunny-input-icon-size" 
            <?php echo !$licence_active ? 'disabled' : ''; ?> 
        />
        <p class="description">
            <?php esc_html_e( 'Sets button vertical offset (min: 10px, max: 300px).', 'coding-bunny-whatsapp-chat' ); ?>
        </p>
    </td>
</tr>
				
                <!-- Button Style Settings -->
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( "Style", 'coding-bunny-whatsapp-chat' ); ?></th>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <select name="coding_bunny_whatsapp_settings[icon_type]" class="coding-bunny-select-icon-type" onchange="updateIconPreview(this)">
                                <option value="coding-bunny-simple-icon.svg" <?php selected( $icon_type, 'coding-bunny-simple-icon.svg' ); ?>><?php esc_html_e( 'Simple', 'coding-bunny-whatsapp-chat' ); ?></option>
                                <option value="coding-bunny-round-icon.svg" <?php selected( $icon_type, 'coding-bunny-round-icon.svg' ); ?>><?php esc_html_e( 'Round', 'coding-bunny-whatsapp-chat' ); ?></option>
                                <option value="coding-bunny-square-icon.svg" <?php selected( $icon_type, 'coding-bunny-square-icon.svg' ); ?>><?php esc_html_e( 'Square', 'coding-bunny-whatsapp-chat' ); ?></option>
                                <option value="coding-bunny-chat-icon-01.svg" <?php selected( $icon_type, 'coding-bunny-chat-icon-01.svg' ); ?>><?php esc_html_e( 'Chat 1', 'coding-bunny-whatsapp-chat' ); ?></option>
								<option value="coding-bunny-chat-icon-02.svg" <?php selected( $icon_type, 'coding-bunny-chat-icon-02.svg' ); ?>><?php esc_html_e( 'Chat 2', 'coding-bunny-whatsapp-chat' ); ?></option>
								<option value="coding-bunny-chat-icon-03.svg" <?php selected( $icon_type, 'coding-bunny-chat-icon-03.svg' ); ?>><?php esc_html_e( 'Chat 3', 'coding-bunny-whatsapp-chat' ); ?></option>
								<option value="coding-bunny-info-icon.svg" <?php selected( $icon_type, 'coding-bunny-info-icon.svg' ); ?>><?php esc_html_e( 'Info', 'coding-bunny-whatsapp-chat' ); ?></option>
                                <option value="custom" <?php selected( $icon_type, 'custom' ); ?> <?php echo !$licence_active ? 'disabled' : ''; ?>><?php esc_html_e( 'Custom', 'coding-bunny-whatsapp-chat' ); ?></option>
                            </select>
                            <img id="icon-preview" src="<?php echo esc_url( $icon_type === 'custom' && ! empty( get_option( 'coding_bunny_whatsapp_custom_icon_url' ) ) ? get_option( 'coding_bunny_whatsapp_custom_icon_url' ) : plugin_dir_url( dirname( __FILE__ ) ) . 'images/' . esc_attr( $icon_type ) ); ?>" alt="<?php esc_attr_e( 'Preview Button', 'coding-bunny-whatsapp-chat' ); ?>" style="width: 40px; height: auto; margin-left: 10px;" />
                        </div>
                        <div id="custom-icon-upload" style="display: <?php echo $icon_type === 'custom' ? 'block' : 'none'; ?>;">
                            <input type="hidden" id="coding_bunny_custom_icon_url" name="coding_bunny_whatsapp_settings[custom_icon_url]" value="<?php echo esc_url( get_option( 'coding_bunny_whatsapp_custom_icon_url', '' ) ); ?>" />
                            <br/>
                            <button type="button" class="button" id="coding_bunny_upload_custom_icon" <?php echo !$licence_active ? 'disabled' : ''; ?>><?php esc_html_e( 'Select Image', 'coding-bunny-whatsapp-chat' ); ?></button>
                            <button type="button" class="button" id="coding_bunny_remove_custom_icon" style="<?php echo empty( get_option( 'coding_bunny_whatsapp_custom_icon_url', '' ) ) ? 'display:none;' : ''; ?>" <?php echo !$licence_active ? 'disabled' : ''; ?>><?php esc_html_e( 'Remove Image', 'coding-bunny-whatsapp-chat' ); ?></button>
                            <p class="description"><?php esc_html_e( 'Upload or select an image from the library.', 'coding-bunny-whatsapp-chat' ); ?></p>
                        </div>
                        <p class="description"><?php esc_html_e( 'Select the type of button you wish to display.', 'coding-bunny-whatsapp-chat' ); ?></p>
                    </td>
                </tr>

                <!-- Button Size Settings -->
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( "Dimension (px)", 'coding-bunny-whatsapp-chat' ); ?></th>
                    <td>
                        <input type="number" name="coding_bunny_whatsapp_settings[icon_size]" value="<?php echo esc_attr( $icon_size ); ?>" min="20" max="60" class="coding-bunny-input-icon-size" />
                        <p class="description"><?php esc_html_e( 'Sets button size (min: 20px, max: 60px).', 'coding-bunny-whatsapp-chat' ); ?></p>
                    </td>
                </tr>
				<!-- Button Shadow Settings -->
<tr valign="top">
    <th scope="row"><?php esc_html_e( "Shadow", 'coding-bunny-whatsapp-chat' ); ?></th>
    <td>
        <input 
            type="number" 
            name="coding_bunny_whatsapp_settings[icon_shadow]" 
            value="<?php echo esc_attr( $icon_shadow ); ?>" 
            min="0" 
            max="1" 
            step="0.1" 
            class="coding-bunny-input-icon-size"
			<?php echo !$licence_active ? 'disabled' : ''; ?>    
        />
        <p class="description"><?php esc_html_e( 'Sets button shadow intensity (min: 0, max: 1).', 'coding-bunny-whatsapp-chat' ); ?></p>
    </td>
</tr>

            </table>

            <!-- Submit Button -->
            <p>
                <input type="submit" value="<?php esc_attr_e( 'Save Settings', 'coding-bunny-whatsapp-chat' ); ?>" class="button-primary"/>
            </p>
            <hr>
            <p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> - <?php esc_html_e( 'Powered by CodingBunny', 'coding-bunny-image-optimizer' ); ?></p>
        </form>
    </div>

    <script>
    // Function to update icon preview based on selected icon type
    function updateIconPreview(select) {
        var iconPreview = document.getElementById("icon-preview");
        var customUpload = document.getElementById("custom-icon-upload");
        var customIconUrl = document.getElementById("coding_bunny_custom_icon_url").value;
        
        // Check if custom icon is selected
        if (select.value === 'custom') {
            customUpload.style.display = 'block';
            iconPreview.src = customIconUrl !== '' ? customIconUrl : '';
        } else {
            customUpload.style.display = 'none';
            // Set icon preview to default icons
            iconPreview.src = "<?php echo esc_url( plugin_dir_url( dirname( __FILE__ ) ) ); ?>images/" + select.value;
        }
    }

    // jQuery document ready function
    jQuery(document).ready(function($) {
        var mediaUploader;

        // Click event to upload custom icon
        $('#coding_bunny_upload_custom_icon').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // Create media uploader
            mediaUploader = wp.media({
                title: '<?php esc_html_e( 'Select image', 'coding-bunny-whatsapp-chat' ); ?>',
                button: {
                    text: '<?php esc_html_e( 'Use this image', 'coding-bunny-whatsapp-chat' ); ?>'
                },
                multiple: false // Set to false for single image upload
            });

            // On image select
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#coding_bunny_custom_icon_url').val(attachment.url); // Set the custom icon URL
                $('#icon-preview').attr('src', attachment.url); // Update the icon preview
                $('#coding_bunny_remove_custom_icon').show(); // Show remove button
            });

            mediaUploader.open(); // Open the media uploader
        });

        // Click event to remove custom icon
        $('#coding_bunny_remove_custom_icon').click(function(e) {
            e.preventDefault();
            $('#coding_bunny_custom_icon_url').val(''); // Clear the custom icon URL
            $('#icon-preview').attr('src', ''); // Reset the icon preview
            $(this).hide(); // Hide the remove button
        });
    });
    </script>

    <?php
}

function coding_bunny_enqueue_scripts() {
    wp_enqueue_script(
        'coding-bunny-script', // Nome unico per lo script
        plugin_dir_url( dirname( __FILE__ ) ) . 'js/coding-bunny-admin.js',
        array(),
        '1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'coding_bunny_enqueue_scripts');