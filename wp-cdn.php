<?php
/**
 * Plugin Name: CDN
 * Description: Change all static files URLs to CDN domain.
 * Version: 3.3.0
 * Author: Innocode
 * Author URI: https://innocode.com
 * Tested up to: 5.4.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

use Innocode\CDN;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

if ( defined( 'CDN_DOMAIN' ) ) {
    $GLOBALS['innocode_cdn'] = new CDN\Plugin();
    $GLOBALS['innocode_cdn']->run();
}

if ( ! function_exists( 'get_cdn_attachment_url' ) ) {
    function get_cdn_attachment_url( $uri ) {
        /**
         * @var CDN\Plugin $innocode_cdn
         */
        global $innocode_cdn;

        if ( is_null( $innocode_cdn ) ) {
            trigger_error(
                'Missing required constant CDN_DOMAIN.',
                E_USER_ERROR
            );
        }

        return $innocode_cdn( $uri );
    }
}
