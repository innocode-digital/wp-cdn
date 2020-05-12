<?php

namespace Innocode\CDN;

/**
 * Class Helpers
 * @package Innocode\CDN
 */
class Helpers
{
    /**
     * @param int $id
     * @return array
     */
    public static function get_blog_domains( int $id ) : array
    {
        global $wpdb;

        return $wpdb->get_col(
            $wpdb->prepare( "SELECT domain FROM $wpdb->blogs WHERE blog_id = %d", $id )
        );
    }

    /**
     * @param int $id
     * @return array
     */
    public static function get_blog_domain_mapping( int $id ) : array
    {
        global $wpdb;

        $table = $wpdb->base_prefix . 'domain_mapping';

        return $wpdb->get_col(
            $wpdb->prepare( "SELECT domain FROM $table WHERE blog_id = %d", $id )
        );
    }
}
