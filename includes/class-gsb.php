<?php
/**
 * Google Safe Browsing Lookup client for YOURLS
 *
 */

class ozh_yourls_GSB {
    
    const PROTOCOL_VER = '4.0';
    const CLIENT       = 'yourls-plugin-gsb';
    const APP_VER      = '1.0';
    
    private $url       = '';
    private $api_key   = false;

    /**
     * Constructor : checks that plugin is properly configured
     *
     */
    public function __construct( $api_key ) {
        $this->api_key = $api_key;
    }

    /**
     * Check if a URL is blacklisted against GSB Lookup API
     *
     * The function returns an array of a boolean and a string.
     * The boolean indicates whether $this->url is blacklisted (true) or not blacklisted (false)
     * The string gives diagnosis details: reason of blacklisting, null if clear, or an error message if applicable
     *
     * @return array array of boolean ( is blacklisted, description ) 
     */
    public function is_blacklisted( $url ) {
        if( !$this->api_key ) {
            return false;
        }
    
        $this->url = urlencode( yourls_sanitize_url( $url ) );
        if( !$this->url ) {
            return false;
        }
        
        $request = $this->request();
        
        switch( $request->status_code ) {
            case 200:
                $response = json_decode($request->body);
                $blacklisted = true;
                if (!isset($response->matches))
                    $blacklisted = false;
                return array($blacklisted, ($blacklisted ? $response->matches[0]->threatType : null));
        
            case 400:
                return array( false, 'Could not check Google Safe Browsing: Bad Request' );
        
            case 403:
                return array( false, 'Could not check Google Safe Browsing: API key not authorized' );
        
            case 503:
                return array( false, 'Could not check Google Safe Browsing: service unavailable' );
        
        }
    }
    
    /**
     * HTTP request wrapper
     *
     * @return Request request object
     */
    private function request() {
        $api_url = sprintf( 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=%s',
            $this->api_key
        );

        // Request headers
        $headers = array(
            'Content-Type' => 'application/json'
        );

        // Request data
        $data = array(
            'client' => array(
                'clientId' => self::CLIENT,
                'clientVersion' => self::APP_VER
            ),
            'threatInfo' => array(
                'threatTypes' => array('MALWARE', 'SOCIAL_ENGINEERING', 'POTENTIALLY_HARMFUL_APPLICATION', 'UNWANTED_SOFTWARE'),
                'platformTypes' => array('ANY_PLATFORM'),
                'threatEntryTypes' => array('URL'),
                'threatEntries' => array(
                    array(
                        'url' => $this->url
                    )
                )
            )
        );
        
        // Request options ?
        $options = array(
        );
        
        return yourls_http_post( $api_url, $headers, json_encode($data), $options );
        
    }

}
