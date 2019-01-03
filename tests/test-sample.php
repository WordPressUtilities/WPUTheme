<?php
/**
 * Class WPUThemeTest
 *
 * @package WPUTheme
 */

/**
 * WPU Theme test case.
 */
class WPUThemeTest extends WP_UnitTestCase {

    /**
     * Test if theme is active
     */
    public function test_activation() {
        $my_theme = wp_get_theme();
        $this->assertEquals($my_theme->get('Name'), 'WPUTheme');
    }

    /**
     * Test truncate
     */
    public function test_truncate() {
        $_srcText = 'Quality is much better than quantity. One home run is much better than two doubles.';

        $test50Result = 'Quality is much better than quantity. One home...';
        $this->assertEquals(wputh_truncate($_srcText, 50, '...'), $test50Result);

        $test11Result = 'Quality...';
        $this->assertEquals(wputh_truncate($_srcText, 11, '...'), $test11Result);
    }

    /**
     * Test clean AJAX Param from URL
     */
    public function test_clean_ajax_param() {
        $base_url = 'https://darklg.me/test/';

        $testBaseResult = $base_url;
        $this->assertEquals(wputh_clean_ajax_parameter_from_url($testBaseResult . '?ajax=1'), $testBaseResult);

        $testBaseResult = $base_url . '?test=internet';
        $this->assertEquals(wputh_clean_ajax_parameter_from_url($testBaseResult . '&ajax=1'), $testBaseResult);

        $testBaseResult = $base_url;
        $this->assertEquals(wputh_clean_ajax_parameter_from_url($testBaseResult . '?ajax=1&test=internet'), $testBaseResult . '?test=internet');
    }
}
