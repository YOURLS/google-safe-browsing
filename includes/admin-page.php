<?php
/**
 * Google Safe Browsing Lookup admin page
 *
 */

// Display admin page
function ozh_yourls_gsb_display_page() {

	// Check if a form was submitted
	if( isset( $_POST['ozh_yourls_gsb'] ) ) {
		// Check nonce
		yourls_verify_nonce( 'gsb_page' );

		// Process form
		ozh_yourls_gsb_update_option();
	}

	// Get value from database
	$ozh_yourls_gsb = yourls_get_option( 'ozh_yourls_gsb' );

	// Create nonce
	$nonce = yourls_create_nonce( 'gsb_page' );

	echo <<<HTML
		<h2>Google Safe Browsing API Key</h2>

		<p>Google requires you to have a <strong>Google account</strong> and a Safe Browsing <strong>API key</strong>
        to use their <a href="https://developers.google.com/safe-browsing/v4/lookup-api">Safe Browsing Lookup Service</a>.</p>
        <p>Get your API key here: <a href="https://console.cloud.google.com/apis/credentials">https://developers.google.com/safe-browsing/key_signup</a></p>

        <h3>Disclaimer from Google</h3>
        <p>Google works to provide the most accurate and up-to-date phishing and malware information. However, it cannot
        guarantee that its information is comprehensive and error-free: some risky sites may not be identified, and some safe
        sites may be identified in error.</p>

        <h3>Configure the plugin</h3>
		<form method="post">
		<input type="hidden" name="nonce" value="$nonce" />
		<p><label for="ozh_yourls_gsb">API Key</label> <input type="text" id="ozh_yourls_gsb" name="ozh_yourls_gsb" value="$ozh_yourls_gsb" size="70" /></p>
		<p><input type="submit" value="Update value" /></p>
		</form>
HTML;
}

// Update option in database
function ozh_yourls_gsb_update_option() {
	$in = $_POST['ozh_yourls_gsb'];

	if( $in ) {
		// Validate ozh_yourls_gsb: alpha & digits
		$in = preg_replace( '/[^a-zA-Z0-9-_]/', '', $in );

		// Update value in database
		yourls_update_option( 'ozh_yourls_gsb', $in );

        yourls_redirect( yourls_admin_url( 'plugins.php?page=ozh_yourls_gsb' ) );
	}
}
