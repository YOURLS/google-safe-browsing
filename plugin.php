<?php
/*
Plugin Name: Google Safe Browsing
Plugin URI: https://github.com/yourls/google-safe-browsing/
Description: Check new links against Google's Safe Browsing service
Version: 1.2
Author: Ozh
Author URI: http://ozh.org/
*/


// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

yourls_add_filter( 'shunt_add_new_link', 'ozh_yourls_gsb_check_add' );

/**
 * Check for spam when someone adds a new link
 *
 * The filter used here is 'shunt_add_new_link', which passes in false as first argument. See
 * https://github.com/YOURLS/YOURLS/blob/1.7/includes/functions.php#L192-L194
 *
 * @param bool $false bool false is passed in by the filter 'shunt_add_new_link'
 * @param string $url URL to check, as passed in by the filter
 * @return mixed false if nothing to do, anything else will interrupt the flow of events
 */
function ozh_yourls_gsb_check_add( $false, $url ) {

    list( $blacklisted, $desc ) = ozh_yourls_gsb_is_blacklisted( $url );

    // If blacklisted, halt here
	if ( $blacklisted ) {
		return array(
			'status' => 'fail',
			'code'   => 'error:' . $desc,
			'message' => 'This domain is blacklisted by Google Safe Browsing because of ' . $desc . ' suspicion. <a href="http://code.google.com/apis/safebrowsing/safebrowsing_faq.html#whyAdvisory" target="_blank">Read more</a>.',
			'errorCode' => '403',
		);
	}

    // If not blacklisted but still unsure (error message), we should warn the user
    if( $desc ) {
        define( 'OZH_YOURLS_GSB_EXTRA_INFO', $desc );
        yourls_add_filter( 'add_new_link', 'ozh_yourls_gsb_extra_info' );
    }

	// All clear, don't interrupt the normal flow of events
	return $false;
}

yourls_add_action( 'plugins_loaded', 'ozh_yourls_gsb_add_page' );

/**
 * Register our plugin admin page
 */
function ozh_yourls_gsb_add_page() {
	yourls_register_plugin_page( 'ozh_yourls_gsb', 'Google Safe Browsing', 'ozh_yourls_gsb_admin_page' );
    if( ! yourls_get_option( 'ozh_yourls_gsb' ) ) {
        ozh_yourls_gsb_please_configure();
    }
}

/**
 * Add extra information to the notification when a link has been added
 *
 * @param array Array passed in by filter 'add_new_link'
 * @return array
 */
function ozh_yourls_gsb_extra_info( $return ) {
    $return['message'] .= '<br/>(' . OZH_YOURLS_GSB_EXTRA_INFO . ')';
    $return['status']  = 'error';
    return $return;
}

/**
 * Check if a URL is blacklisted by Google Safe Browsing
 *
 * @param string $url URL to check
 * @return array array( (boolean)is_blacklisted, (string)description )
 */
function ozh_yourls_gsb_is_blacklisted( $url ) {
    include_once dirname( __FILE__ ) . '/includes/class-gsb.php';

    $api_key = yourls_get_option( 'ozh_yourls_gsb' );
    if( !$api_key ) {
        ozh_yourls_gsb_please_configure();
        return 'no api key';
    }

    $gsb = new ozh_yourls_GSB( $api_key );

    return $gsb->is_blacklisted( $url );
}

/**
 * Display the admin page
 *
 */
function ozh_yourls_gsb_admin_page() {
    include_once dirname( __FILE__ ) . '/includes/admin-page.php';
    ozh_yourls_gsb_display_page();
}

/**
 * Nag user about missing configuration
 *
 */
function ozh_yourls_gsb_please_configure() {
    yourls_add_notice( 'Plugin <strong>Google Safe Browsing</strong> is not configured' );
}
