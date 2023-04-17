<?php
/*
Plugin Name: Very Simple CRM
Description: A very simpkle Customer Relationship Management wordpress plugin.
Version: 1.0
Author: John Jezon Ajias
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// Enqueue custom CSS styles
function enqueue_very_simple_crm_styles() {
    wp_enqueue_script( 'very-simple-crm', plugin_dir_url( __FILE__ ) . 'js/very-simple-crm-script.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'very-simple-crm', 'crm', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ), // This is the URL for admin-ajax.php
        'nonce'   => wp_create_nonce( 'customer_submission_nonce' )
    ) );

    // Register styles
    wp_enqueue_style( 'very-simple-crm-styles', plugins_url( 'css/very-simple-crm-styles.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'enqueue_very_simple_crm_styles' );


// Form fields markup
function customer_submission_form_shortcode( $atts ) {
    ob_start();

    $atts = shortcode_atts( array(
        'name_label'          => 'Name:',           // Default label for name field
        'phone_label'         => 'Phone Number:',   // Default label for phone field
        'email_label'         => 'Email Address:',  // Default label for email field
        'budget_label'        => 'Desired Budget:', // Default label for budget field
        'message_label'       => 'Message:',        // Default label for message field
        'name_max_length'     => '40',              // Default max length for name field
        'phone_max_length'    => '12',              // Default max length for phone field
        'email_max_length'    => '30',              // Default max length for emai field
        'budget_max_length'   => '10',              // Default max length for budget field
        'message_max_length'  => '360',             // Default max length for message field
        'message_rows_length' => '10',              // Default max rows for message field
        'message_cols_length' => '20',              // Default max cols for message field
    ), $atts );


    // Display the form
    echo '<div id="very-simple-crm">';
    echo '<form id="very-simple-crm-form" action="' . admin_url( 'admin-ajax.php' ) . '">';
    wp_nonce_field( 'customer_submission', 'customer_submission_nonce' );
    echo '<input type="hidden" name="action" value="customer_submission">';
    echo '<label for="customer_name">'. esc_html( $atts['name_label' ] ) .'</label>';
    echo '<input type="text" name="customer_name" id="customer_name" maxlength="'. esc_attr( $atts[ 'name_max_length' ] ) .'" required>';
    echo '<label for="customer_phone">'. esc_html( $atts['phone_label' ] ) .'</label>';
    echo '<input type="text" name="customer_phone" id="customer_phone" maxlength="'. esc_attr( $atts[ 'phone_max_length' ] ) .'" >';
    echo '<label for="customer_email">'. esc_html( $atts['email_label' ] ) .'</label>';
    echo '<input type="email" name="customer_email" id="customer_email" maxlength="'. esc_attr( $atts[ 'email_max_length' ] ) .'" required>';
    echo '<label for="customer_budget">'. esc_html( $atts['budget_label' ] ) .'</label>';
    echo '<input type="text" name="customer_budget" id="customer_budget" maxlength="'. esc_attr( $atts[ 'budget_max_length' ] ) .'" required>';
    echo '<label for="customer_message">'. esc_html( $atts['message_label' ] ) .'</label>';
    echo '<textarea name="customer_message" id="customer_message" cols="'. esc_attr( $atts['message_cols_length']) .'" rows="'. esc_attr( $atts[ 'message_rows_length' ] ) .'" maxlength="'. esc_attr( $atts[ 'message_max_length' ] ) .'" required></textarea>';
    echo '<input type="submit" name="customer_submit" value="Submit">';
    echo '<input type="button" value="Clear" id="customer-form-clear">';
    echo '</form>';
    echo '</div>';

    return ob_get_clean();
}
add_shortcode( 'customer_form', 'customer_submission_form_shortcode' );

// AJAX handler to save form data
function customer_submission_ajax_handler() {
    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['customer_submission'], 'customer_submission_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
    }

    // Retrieve form data
    $customer_name    = sanitize_text_field( $_POST['customer_name'] );
    $customer_phone   = sanitize_text_field( $_POST['customer_phone'] );
    $customer_email   = sanitize_email( $_POST['customer_email'] );
    $customer_budget  = sanitize_text_field( $_POST['customer_budget'] );
    $customer_message = sanitize_textarea_field( $_POST['customer_message'] );

    // Create new customer post
    $customer_post = array(
        'post_title'   => $customer_name,
        'post_content' => $customer_message,
        'post_status'  => 'publish',
        'post_type'    => 'customer',
        'meta_input'   => array(
            '_customer_phone'   => $customer_phone,
            '_customer_email'   => $customer_email,
            '_customer_budget'  => $customer_budget
        )
    );

    // Insert customer post
    $customer_post_id = wp_insert_post( $customer_post );

    if ( $customer_post_id ) {
        wp_send_json_success( array( 'message' => 'Customer data saved successfully.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to save customer data.' ) );
    }
}
add_action( 'wp_ajax_customer_submission', 'customer_submission_ajax_handler' );
add_action( 'wp_ajax_nopriv_customer_submission', 'customer_submission_ajax_handler' );

// Register 'customer' custom post type
function very_simple_crm_register_customer_post_type() {
    $labels = array(
        'name'                => 'Customers',
        'singular_name'       => 'Customer',
        'add_new'             => 'Add New',
        'add_new_item'        => 'Add New Customer',
        'edit_item'           => 'Edit Customer',
        'new_item'            => 'New Customer',
        'all_items'           => 'All Customers',
        'view_item'           => 'View Customer',
        'search_items'        => 'Search Customers',
        'not_found'           => 'No customers found',
        'not_found_in_trash'  => 'No customers found in Trash',
        'parent_item_colon'   => '',
        'menu_name'           => 'Customers'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => 'very_simple_crm_admin_page',
        'query_var'           => false,
        'rewrite'             => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'supports'            => array( 'title', 'editor', 'custom-fields' ),
        'taxonomies'          => array( 'category' )
    );

    register_post_type( 'customer', $args );
}
add_action( 'init', 'very_simple_crm_register_customer_post_type' );


// Register simple crm admin menu page for customer submissions
function very_simple_crm_register_admin_menu() {
    add_menu_page (
        'Customers',
        'Customers',
        'manage_options',
        'very_simple_crm_admin_page',
        'very_simple_crm_render_admin_page',
        'dashicons-businessman',
        25,
    );
}
add_action( 'admin_menu', 'very_simple_crm_register_admin_menu' );
