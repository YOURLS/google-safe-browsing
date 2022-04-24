<?php

/**
 * Test that the plugin is actually working
 */

class TestRun extends PHPUnit\Framework\TestCase
{

    protected function tearDown(): void {
        // remove all filters
        yourls_remove_all_filters('is_admin');
    }

    /**
     * Get API key, either from environment variable or from ./api_key.txt
     *
     * When testing locally, write your api key to <plugin_dir>/api_key.txt
     * When testing on GITHUB, set the environment variable API_KEY
     */
    private function getApiKey() {
        $api_key = getenv('API_KEY');
        if ($api_key == false) {
            $api_key = file_get_contents(dirname(__DIR__) . '/api_key.txt');
        }
        return $api_key;
    }

    /**
     * Internal : check we have an API key - otherwise we won't be able to test
     */
    function test_get_api_key() {
        $this->assertNotEmpty( $this->getApiKey() );
    }

    /**
     * Test that ozh_yourls_gsb_is_blacklisted() returns a message when no option is set
     */
    function test_ozh_yourls_gsb_is_blacklisted_with_no_option() {
        yourls_delete_option('ozh_yourls_gsb');
        $this->assertEquals('no api key', ozh_yourls_gsb_is_blacklisted('https://example.com'));
    }

    /**
     * Test ozh_yourls_gsb_is_blacklisted() with a valid URL
     *
     * @depends test_get_api_key
     */
    function test_ozh_yourls_gsb_is_blacklisted_with_valid_url() {
        yourls_update_option('ozh_yourls_gsb', $this->getApiKey());
        list( $blacklisted, $desc ) = ozh_yourls_gsb_is_blacklisted('https://example.com');
        $this->assertFalse($blacklisted);
        $this->assertNull($desc);
    }

    /**
     * Test ozh_yourls_gsb_is_blacklisted() with a suspicious URL
     *
     * @depends test_get_api_key
     */
    function test_ozh_yourls_gsb_is_blacklisted_with_suspicious_url() {
        yourls_update_option('ozh_yourls_gsb', $this->getApiKey());
        list( $blacklisted, $desc ) = ozh_yourls_gsb_is_blacklisted('https://testsafebrowsing.appspot.com/s/phishing.html');
        $this->assertTrue($blacklisted);
        $this->assertNotNull($desc);
    }

}
