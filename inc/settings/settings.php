<?php 

//  no direct access 
if( !defined('ABSPATH') ) : exit(); endif;


// Create Settings Menu Page Item 
add_action('admin_menu', 'woocommerce_picqer_api_plugin_settings_menu');
function woocommerce_picqer_api_plugin_settings_menu() {

    add_menu_page(
        __( 'Woocommerce Picqer API Settings', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        __( 'Woocommerce Picqer API Settings', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'manage_options',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        'woocommerce_picqer_api_plugin_settings_template_callback',
        'dashicons-rest-api',
        10
    );

    add_submenu_page(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        __( 'Import Picqer Products To WooCommerce', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        __( 'Import Picqer Products To WooCommerce', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'manage_options',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_import_page',
        'woocommerce_picqer_api_import_template_callback',
    );

    add_submenu_page(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        __( 'Woocommerce Picqer API Cron Status', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        __( 'Woocommerce Picqer API Cron Status', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'manage_options',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_cron_page',
        'woocommerce_picqer_api_cron_template_callback',
    );

}


// Settings Template Page 
function woocommerce_picqer_api_plugin_settings_template_callback() {
    
    // adding bootstrap css
    echo '<link rel="stylesheet" href="' . WOOCOMMERCE_PICQER_API_PLUGIN_URL . 'assets/css/bootstrap.min.css">';

    ?>

    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <div class="row">
            <form action="options.php" method="post">

                <?php 
                    // security field
                    settings_fields( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page' );

                    // save settings button 
                    submit_button( 'Save Settings' );

                    // output settings section here
                    do_settings_sections(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page');

                    ?>
                    <h6 class="text-center">
                        Star (*) marked ones are required
                    </h6>
                    <div class="text-right" style="text-align: right;">
                    <?php 
                        // save settings button
                        submit_button( 'Save Settings', 'primary', '', false );
                        ?>
                    </div>
            </form>
        </div>


        <div class="row my-5">

            <div class="col-md-6">
                <h3 class="text-center">
                    Indications On The Cron: 
                </h3>
                <h6 class="text-primary text-center">
                    How to enable the WordPress cron?
                    <br>
                    To enable the WordPress cron job, open your wp-config.php file and locate the line:
                    <br>
                    define('DB_COLLATE', '');
                    <br>
                    Under it, add the following line:
                    <br>
                    define('DISABLE_WP_CRON', false);
                </h6>
                <h6 class="text-primary text-center">
                    Either you can turn the above WP Cron on.
                    <br>
                    Or can add this following path to your hosted server's Cron:
                    <br>
                    <?php echo WOOCOMMERCE_PICQER_API_PLUGIN_PATH . 'cron.php'; ?>
                </h6>
            </div>

            <div class="col-md-6">
                <h3 class="text-center">Note:</h3>
                <h6 class="text-primary text-center">
                    You can modify any external WordPress website from here.
                    <br>
                    Just need to put website URL and WC Credentials correctly for that website.
                </h6>
            </div>

        </div>

    </div>

    <?php 

}


//  Settings Template 
add_action( 'admin_init', 'woocommerce_picqer_api_settings_init' );
function woocommerce_picqer_api_settings_init() {

    // Setup settings section 1
    add_settings_section(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section1',
        'Woocommerce API Credentials',
        '',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        array(
            'before_section' => '<div class="row"><div class="col-md-6"><div>',
            'after_section'  => '</div>',
        )
    );

    // Setup settings section 2
    add_settings_section(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section2',
        'Other Woocommerce Settings',
        '',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        array(
            'before_section' => '<div>',
            'after_section'  => '</div></div>',
        )
    );

    // Setup settings section 3
    add_settings_section(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section3',
        'Picqer API Credentials',
        '',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        array(
            'before_section' => '<div class="col-md-6"><div>',
            'after_section'  => '</div>',
        )
    );

    // Setup settings section 4
    add_settings_section(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section4',
        'OpenAI API Credentials',
        '',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        array(
            'before_section' => '<div>',
            'after_section'  => '</div></div></div>',
        )
    );


// section 1 starts here

    // Register field
    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_website_url',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_website_url',
        __( 'Website URL*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_website_url_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section1'
    );

    // Register radio field
    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_key',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_key',
        __( 'Woocommerce API Consumer Key*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_woocommerce_api_consumer_key_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section1'
    );


    // Register text field
    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret',
        __( 'Woocommerce API Consumer Secret*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_woocommerce_api_consumer_secret_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section1'
    );


// section 1 ends here


// section 2 starts here 

    // Register text field
    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_mul_val',
        array(
            'type' => 'number',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_mul_val',
        __( 'Woocommerce Price Multiplied By', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_woocommerce_api_mul_val_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section2'
    );


    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_wc_prod_tags',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_wc_prod_tags',
        __( 'WooCommerce Product Tags', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_wc_prod_tags_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section2'
    );


    // Register checkbox field
    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_custom_tags_on_off',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_key',
            'default' => ''
        )
    );

    // Add checkbox fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_custom_tags_on_off',
        __( 'Force to use these tags:', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_custom_tags_on_off_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section2'
    );


    // Register checkbox field
    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_on_off',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_key',
            'default' => ''
        )
    );

    // Add checkbox fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_on_off',
        __( 'Turn OpenAI on/off:', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_open_ai_on_off_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section2'
    );


    // Register checkbox field
    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_cron_on_off',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_key',
            'default' => ''
        )
    );

    // Add checkbox fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_cron_on_off',
        __( 'Turn The Wordpress Cron On/Off:', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_cron_on_off_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section2'
    );

// section 2 ends here 


// section 3 starts here 

    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url',
        __( 'Picqer API Base URL*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_picqer_api_base_url_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section3'
    );


    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username',
        __( 'Picqer API Username*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_picqer_api_username_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section3'
    );


    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password',
        __( 'Picqer API Password*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_picqer_api_password_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section3'
    );


    register_setting(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );

    // Add text fields
    add_settings_field(
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language',
        __( 'Picqer API Language*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
        'woocommerce_picqer_api_picqer_api_language_callback',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
        WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section3'
    );

// section 3 ends here


// section 4 starts here

register_setting(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_api_key',
    array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    )
);

// Add text fields
add_settings_field(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_api_key',
    __( 'OpenAI API Key*', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
    'woocommerce_picqer_api_open_ai_api_key_callback',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section4'
);


register_setting(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_model',
    array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    )
);

// Add text fields
add_settings_field(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_model',
    __( 'OpenAI Model', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
    'woocommerce_picqer_api_open_ai_model_callback',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section4'
);


register_setting(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_temperature',
    array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    )
);

// Add text fields
add_settings_field(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_temperature',
    __( 'OpenAI API Temperature', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
    'woocommerce_picqer_api_open_ai_temperature_callback',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section4'
);

register_setting(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_max_tokens',
    array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    )
);

// Add text fields
add_settings_field(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_max_tokens',
    __( 'OpenAI API Maximum Tokens', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
    'woocommerce_picqer_api_open_ai_max_tokens_callback',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section4'
);

register_setting(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_frequency_penalty',
    array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    )
);

// Add text fields
add_settings_field(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_frequency_penalty',
    __( 'OpenAI API Frequency Penalty', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
    'woocommerce_picqer_api_open_ai_frequency_penalty_callback',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section4'
);

register_setting(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_presence_penalty',
    array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    )
);

// Add text fields
add_settings_field(
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_presence_penalty',
    __( 'OpenAI API Presence Penalty', WOOCOMMERCE_PICQER_API_PLUGIN_NAME ),
    'woocommerce_picqer_api_open_ai_presence_penalty_callback',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_page',
    WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_settings_section4'
);

// section 4 ends here




}
// Settings Template ends here 


// Settings Template input fields starts here 

// section 1 starts here
function woocommerce_picqer_api_website_url_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_website_url');
    ?>
    <input type="text" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_website_url" class="regular-text" placeholder='Website URL...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : site_url() . '/'; ?>" />
    <?php 
}

function woocommerce_picqer_api_woocommerce_api_consumer_key_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_key');
    ?>
    <input type="text" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_woocommerce_api_consumer_key" class="regular-text" placeholder='Woocommerce API Consumer Key...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}


function woocommerce_picqer_api_woocommerce_api_consumer_secret_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret');
    ?>
    <input type="password" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_woocommerce_api_consumer_secret" class="regular-text" placeholder='Woocommerce API Consumer Secret...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

// section 1 ends here



// section 2 starts here

function woocommerce_picqer_api_woocommerce_api_mul_val_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_mul_val');
    ?>
    <input type="number" min="0" step="0.01" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_woocommerce_api_mul_val" class="regular-text" placeholder='Default is 1...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

function woocommerce_picqer_api_wc_prod_tags_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_wc_prod_tags');
    ?>
    <textarea name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_wc_prod_tags" placeholder='Comma Separated Tags...' class="regular-text" rows="4">
        <?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ?     esc_textarea( $woocommerce_picqer_api_input_field ) : ''; ?>
    </textarea>
    <?php 
}

function woocommerce_picqer_api_custom_tags_on_off_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_custom_tags_on_off');
    ?>
    <label>
        <input type="checkbox" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_custom_tags_on_off" value="yes" <?php checked( 'yes', $woocommerce_picqer_api_input_field ); ?>/> Please check to force to use these tags on the products!
    </label>
    <?php 
}

function woocommerce_picqer_api_open_ai_on_off_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_on_off');
    ?>
    <label>
        <input type="checkbox" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_open_ai_on_off" value="yes" <?php checked( 'yes', $woocommerce_picqer_api_input_field ); ?>/> Please check to turn on OpenAI!
    </label>
    <?php 
}

function woocommerce_picqer_api_cron_on_off_callback() {
    // if cron file exists
    if(file_exists( WOOCOMMERCE_PICQER_API_PLUGIN_PATH . 'cron.php')){
        $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_cron_on_off');
        ?>
        <label>
            <input type="checkbox" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_cron_on_off" value="yes" <?php checked( 'yes', $woocommerce_picqer_api_input_field ); ?>/> Please check to turn on!
        </label>
        <?php 
    }else{
        ?>
        <p class="description">
            <?php esc_html_e( 'cron.php file is missing!' ); ?>
        </p>
        <?php
    }
}

// section 2 ends here


// section 3 starts here

function woocommerce_picqer_api_picqer_api_base_url_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url');
    ?>
    <input type="text" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_picqer_api_base_url" class="regular-text" placeholder='Picqer API Base URL...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : 'https://api.roerdink.nl/api/v1/'; ?>" />
    <?php 
}

function woocommerce_picqer_api_picqer_api_username_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username');
    ?>
    <input type="text" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_picqer_api_username" class="regular-text" placeholder='Picqer API Username...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

function woocommerce_picqer_api_picqer_api_password_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password');
    ?>
    <input type="password" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_picqer_api_password" class="regular-text" placeholder='Picqer API Password...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

function woocommerce_picqer_api_picqer_api_language_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language');
    ?>
    <select class="regular-text" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_picqer_api_language" placeholder="Picqer API Language...">
        <option value="nl-NL" <?php selected( 'nl-NL', $woocommerce_picqer_api_input_field ); ?> >nl-NL</option>
        <option value="en-GB" <?php selected( 'en-GB', $woocommerce_picqer_api_input_field ); ?> >en-GB</option>
        <option value="de-DE" <?php selected( 'de-DE', $woocommerce_picqer_api_input_field ); ?> >de-DE</option>
    </select>
    <?php 
}

// section 3 ends here


// section 4 starts here

function woocommerce_picqer_api_open_ai_api_key_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_api_key');
    ?>
    <input type="password" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_open_ai_api_key" class="regular-text" placeholder='OpenAI API Key...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

function woocommerce_picqer_api_open_ai_model_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_model');
    ?>
    <input type="text" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_open_ai_model" class="regular-text" list="open_ai_models" placeholder="Double click for dropdown. Default: text-davinci-003" value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>">
    <datalist id="open_ai_models">
        <option value="gpt-4">
        <option value="gpt-3.5-turbo-instruct">
        <option value="gpt-3.5-turbo">
        <option value="text-davinci-003">
        <option value="gpt-3.5-turbo-16k">
    </datalist>
    <?php 
}

function woocommerce_picqer_api_open_ai_temperature_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_temperature');
    ?>
    <input type="number" step="0.01" min="0" max="1" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_open_ai_temperature" class="regular-text" placeholder='Default is 0.9...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

function woocommerce_picqer_api_open_ai_max_tokens_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_max_tokens');
    ?>
    <input type="number" step="1" min="0" max="8000" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_open_ai_max_tokens" class="regular-text" placeholder='Default is 500...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

function woocommerce_picqer_api_open_ai_frequency_penalty_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_frequency_penalty');
    ?>
    <input type="number" step="0.01" min="0" max="1" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_open_ai_frequency_penalty" class="regular-text" placeholder='Default is 0...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

function woocommerce_picqer_api_open_ai_presence_penalty_callback() {
    $woocommerce_picqer_api_input_field = get_option(WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_presence_penalty');
    ?>
    <input type="number" step="0.01" min="0" max="1" name="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_NAME; ?>_open_ai_presence_penalty" class="regular-text" placeholder='Default is 0.6...' value="<?php echo isset($woocommerce_picqer_api_input_field) && $woocommerce_picqer_api_input_field != '' ? $woocommerce_picqer_api_input_field : ''; ?>" />
    <?php 
}

// section 4 ends here

// Settings Template input fields ends here 



// Submenu page 2
function woocommerce_picqer_api_import_template_callback() {

    // adding bootstrap css
    echo '<link rel="stylesheet" href="' . WOOCOMMERCE_PICQER_API_PLUGIN_URL . 'assets/css/bootstrap.min.css">';

    ?>

    <style>
        .picqer_clickable_product_ids{
            cursor: pointer;
        }
        .picqer_product_ids{
            margin-right: 10px;
            display: inline-block;
        }
        .picqer_product_ids:last-child{
            margin-right: 0;
        }
    </style>

    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <button type="submit" id="picqer_submit_all_ids_result" class="btn btn-primary mt-4">Show Product IDs</button>
                    <button type="submit" id="picqer_import_all_products" class="btn btn-primary mt-4">Import All Products</button>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mt-5">
                    <?php 
                        echo do_shortcode('[importPicqerProductsShortcode]');
                    ?>
                </div>
            </div>
        </div>
    </div>


    <script>

    $( document ).ready(function() {

        var post_url = "<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_URL . 'inc/shortcodes/includes/post.php'; ?>";
        
        var woocommerce_picqer_api_submit_button = $("#woocommerce_picqer_api_submit_button").html();
        var picqer_submit_all_ids_result = $("#picqer_submit_all_ids_result").html();
        var picqer_import_all_products = $("#picqer_import_all_products").html();
        var picqer_api_product_id;
        var current_url = $(location).attr("href");
        var returnHTML;

        $("#picqer_submit_all_ids_result").click(function(){
            $("#picqer_submit_all_ids_result").attr("disabled", true);
            $("#woocommerce_picqer_api_submit_button").attr("disabled", true);
            $("#picqer_import_all_products").attr("disabled", true);
            $("#picqer_submit_all_ids_result").html("Please wait...");
            $("#woocommerce_picqer_api_submit_button").html("Please wait...");
            $("#picqer_import_all_products").html("Please wait...");
            $("#result").html("<h6>Please do not refresh or close this window while importing...</h6>");

            $.ajax({
                type: "POST",
                url: post_url,
                data: {all_product: 'yes'}, 
                success: function(result){
                    $("#picqer_submit_all_ids_result").attr("disabled", false);
                    $("#woocommerce_picqer_api_submit_button").attr("disabled", false);
                    $("#picqer_import_all_products").attr("disabled", false);
                    $("#picqer_submit_all_ids_result").html(picqer_submit_all_ids_result);
                    $("#picqer_import_all_products").html(picqer_import_all_products);
                    $("#woocommerce_picqer_api_submit_button").html(woocommerce_picqer_api_submit_button);
                    $("#result").html(result);
                }
            });
        });

        $("#picqer_import_all_products").click(function(){
            if (confirm("Are you sure, you want to import all the products?") == true) {
                $("#picqer_submit_all_ids_result").attr("disabled", true);
                $("#woocommerce_picqer_api_submit_button").attr("disabled", true);
                $("#picqer_import_all_products").attr("disabled", true);
                $("#picqer_submit_all_ids_result").html("Please wait...");
                $("#woocommerce_picqer_api_submit_button").html("Importing...");
                $("#picqer_import_all_products").html("Please wait...");
                $("#result").html("<h6>Please do not refresh or close this window while importing...</h6>");

                $.ajax({
                    type: "POST",
                    url: post_url,
                    data: {import_all_product: 'yes', current_url},
                    timeout: 1200000,
                    success: function(result){
                        $("#picqer_submit_all_ids_result").attr("disabled", false);
                        $("#woocommerce_picqer_api_submit_button").attr("disabled", false);
                        $("#picqer_import_all_products").attr("disabled", false);
                        $("#picqer_submit_all_ids_result").html(picqer_submit_all_ids_result);
                        $("#picqer_import_all_products").html(picqer_import_all_products);
                        $("#woocommerce_picqer_api_submit_button").html(woocommerce_picqer_api_submit_button);
                        $("#result").html("Total Imported = " + result);
                    },
                    error: function(xmlhttprequest, textstatus, message) {
                        $("#picqer_submit_all_ids_result").attr("disabled", false);
                        $("#woocommerce_picqer_api_submit_button").attr("disabled", false);
                        $("#picqer_import_all_products").attr("disabled", false);
                        $("#picqer_submit_all_ids_result").html(picqer_submit_all_ids_result);
                        $("#picqer_import_all_products").html(picqer_import_all_products);
                        $("#woocommerce_picqer_api_submit_button").html(woocommerce_picqer_api_submit_button);
                        
                        if( textstatus === "timeout") {
                            $("#result").html(textstatus + " : " + "Server Timeout");
                        } else {
                            if(message === ""){
                                $("#result").html(textstatus + " : " + "Server Timeout");
                            }else{
                                $("#result").html(textstatus + " : " + message);
                            }
                        }
                    }
                });
            }
            // if confirm ends here
        });

    });

    </script>

    <?php

}



// Submenu page 3
function woocommerce_picqer_api_cron_template_callback() {
    
    // adding bootstrap css
    echo '<link rel="stylesheet" href="' . WOOCOMMERCE_PICQER_API_PLUGIN_URL . 'assets/css/bootstrap.min.css">';

    // initializing
    $outputText = 'Please Add Picqer Products';

    // updated value
    $picqer_cron_list = get_option('picqer_cron_list');
    $picqer_sku_next_to_update = get_option('picqer_sku_next_to_update');

    $total_cron_items = count($picqer_cron_list);

    // if not empty
    if( $picqer_sku_next_to_update != '' && $total_cron_items  > 0 ){

        $key6 = array_search($picqer_sku_next_to_update, $picqer_cron_list);

        $keys = array_keys($picqer_cron_list);

        $key7 =  array_search($key6, $keys);

        $outputText = ($key7 + 1) . ' => ' . $key6 . ' => ' . $picqer_sku_next_to_update;

    }


?>


    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <div class="row">

            <div class="col-md-12">
                <h3 class="mt-5 text-center">
                    Next to update : 
                </h3>
                <h6 class="text-danger text-center">
                    Item Position => Product ID => Product SKU
                </h6>
                <h6 class="text-danger text-center">
                    <?php echo $outputText; ?>
                </h6>
                <div class="d-flex justify-content-center align-items-center">
                    <a target="_blank" href="<?php echo WOOCOMMERCE_PICQER_API_PLUGIN_URL . 'cron.php'; ?>">
                        <button id='woocommerce_picqer_api_submit_button' class='btn btn-success mt-3' type='submit' name='woocommerce_picqer_api_submit_button'>Run The Cron Manually</button>
                    </a>
                </div>
            </div>

        </div>
    </div>


<?php

// if not empty
if( $picqer_cron_list != '' && count($picqer_cron_list) > 0 ){
    $iteration = 1;
    $resultHTML = '<div class="mt-5 w-100">';
    $resultHTML .= '<span><b>Items in the Cron:</b> </span>';
    $resultHTML .= '<span>(Position) => WC Product ID => WC Product SKU</span>';
    $resultHTML .= '<br>';
    $resultHTML .= '<span>Total <b>'.$total_cron_items.'</b> items</span>';
    $resultHTML .= '<br>';
    foreach($picqer_cron_list as $key => $value){
        $resultHTML .= '<span class=""> ('.$iteration.') => '.$key.' => '.$value.', </span>';
        $iteration++;
    }
    $resultHTML .= '</div>';
    echo $resultHTML;
}

}