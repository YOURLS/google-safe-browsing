<?php

/**
 * Test that everything is initialized properly.
 */

class TestInit extends PHPUnit\Framework\TestCase {

    /**
     * Check that the plugin hooks are registered
     */
    function test_plugin_hooks_are_registered() {
        $this->assertSame( 10, yourls_has_filter('shunt_add_new_link', 'ozh_yourls_gsb_check_add') );
        $this->assertSame( 10, yourls_has_action('plugins_loaded', 'ozh_yourls_gsb_add_page') );
    }

    /**
     * Check that the plugin page is added
     */
    function test_plugin_page_is_added() {
        // the plugin page is added on the plugins_loaded hook
        yourls_do_action('plugins_loaded');

        // check that array yourls_get_db()->get_plugin_pages() has key 'ozh_yourls_gsb'
        $this->assertTrue(array_key_exists('ozh_yourls_gsb', yourls_get_db()->get_plugin_pages()));
    }

    /**
     * Test admin notice is displayed when no option set
     *
     * @depends test_plugin_page_is_added
     */
    function test_admin_notice_is_added() {
        yourls_delete_option('ozh_yourls_gsb');

        // Get all functions hooked to 'admin_notices' with default priority
        global $yourls_filters;
        $hooks = $yourls_filters["admin_notices"][10];

        // get first element of array : the first function hooked to 'admin_notices' (actually the only one)
        $hook = array_shift($hooks);

        // Check that the function hooked to admin_notice contains the right text
        $this->expectOutputRegex('!<strong>Google Safe Browsing</strong> is not configured!');
        $message = $hook['function']();
    }

}
