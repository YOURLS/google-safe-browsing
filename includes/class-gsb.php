<?php
/**
 * Google Safe Browsing Lookup client for YOURLS
 *
 */

class ozh_yourls_GSB {
    
    const PROTOCOL_VER = '3.0';
    const CLIENT       = 'yourls-plugin';
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
        
        // var_dump( $request );
        
        switch( $request->status_code ) {
            case 200:
                return array( true, $request->body );
                
            case 204:
                return array( false, null );
        
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
        $api_url = sprintf( 'https://sb-ssl.google.com/safebrowsing/api/lookup?client=%s&key=%s&appver=%s&pver=%s&url=%s',
            self::CLIENT,
            $this->api_key,
            self::APP_VER,
            self::PROTOCOL_VER,
            $this->url
        );
        
        // Request options ?
        $options = array(
        );
        
        return yourls_http_get( $api_url, array(), array(), $options );
        
    }

}
