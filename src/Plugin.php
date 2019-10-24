<?php

namespace Innocode\CDN;

/**
 * Class Plugin
 * @package Innocode\CDN
 */
final class Plugin
{
    /**
     * @var string
     */
    private $_url;
    /**
     * @var array
     */
    private $_hosts = [];
    /**
     * @var array
     */
    private $_extensions = [
        // images
        'png', 'jpg', 'jpeg', 'gif', 'tif', 'bmp', 'svg', 'webp',
        // assets
        'css', 'js',
        // archives
        'zip', 'gz', 'tar',
        // fonts
        'ttf', 'eot', 'woff', 'woff2',
        // video
        'webm', 'mp4', 'm4v',
        //audio
        'mp3', 'wav',
    ];

    public function __construct()
    {
        $this->_url = defined( 'CDN_DOMAIN' ) ? 'https://' . CDN_DOMAIN : '';
        $this->_init_hosts();
    }

    public function run()
    {
        $filters = [
            'script_loader_src',
            'style_loader_src',
            'wp_get_attachment_url',
            'theme_file_uri',
            'parent_theme_file_uri',
            'the_content',
        ];

        foreach ( $filters as $filter ) {
            add_filter( $filter, $this, 999 );
        }

        add_filter( 'wp_calculate_image_srcset', [ $this, 'filter_srcset' ], 999 );
    }

    /**
     * @return string
     */
    public function get_url() : string
    {
        return $this->_url;
    }

    /**
     * @return array
     */
    public function get_hosts() : array
    {
        return apply_filters( 'innocode_cdn_hosts', $this->_hosts );
    }

    /**
     * @return array
     */
    public function get_extensions() : array
    {
        return apply_filters( 'innocode_cdn_extensions', $this->_extensions );
    }

    /**
     * @return string
     */
    public function get_hosts_regex() : string
    {
        $hosts = array_map( function ( $host ) {
            return preg_quote( $host, '/' );
        }, $this->get_hosts() );

        return implode( '|', $hosts );
    }

    /**
     * @return string
     */
    public function get_extensions_regex() : string
    {
        $extensions = array_map( 'preg_quote', $this->get_extensions() );

        return implode( '|', $extensions );
    }

    /**
     * @return string
     */
    public function get_regex() : string
    {
        $hosts_regex = $this->get_hosts_regex();
        $extensions_regex = $this->get_extensions_regex();

        return "https?:\/\/($hosts_regex)\/([^\"^']+\.[$extensions_regex])";
    }

    /**
     * @param array $sources
     * @return array
     */
    public function filter_srcset( array $sources )
    {
        foreach ( $sources as $key => $source ) {
            $sources[ $key ]['url'] = $this( $source['url'] );
        }

        return $sources;
    }

    private function _init_hosts()
    {
        if ( false !== ( $hosts = wp_cache_get( 'site-hosts', 'innocode_cdn' ) ) ) {
            $this->_hosts = $hosts;

            return;
        }

        foreach ( [
            home_url(),
            site_url(),
        ] as $url ) {
            $url = wp_parse_url( $url );

            if ( ! isset( $url['host'] ) ) {
                continue;
            }

            $host = $url['host'];

            if ( isset( $url['path'] ) ) {
                $path = trim( $url['path'], '/' );

                if ( $path ) {
                    $host .= "/$path";
                }
            }

            $this->_hosts[] = $host;
        }

        $blog_id = get_current_blog_id();

        if ( is_multisite() ) {
            $this->_hosts = array_merge( $this->_hosts, Helpers::get_blog_domains( $blog_id ) );
        }

        if ( defined( 'DOMAIN_MAPPING' ) ) {
            $this->_hosts = array_merge( $this->_hosts, Helpers::get_blog_domain_mapping( $blog_id ) );
        }

        $this->_hosts = array_unique( $this->_hosts );

        wp_cache_set( 'site-hosts', $this->_hosts, 'innocode_cdn', HOUR_IN_SECONDS );
    }

    /**
     * @param string $uri
     * @return string
     */
    public function __invoke( string $uri ) : string
    {
        if ( ! apply_filters( 'innocode_cdn_should_replace_url', true, $uri ) ) {
            return $uri;
        }

        $regex = $this->get_regex();
        $url = $this->get_url();

        return preg_replace( "/$regex/Ui", "$url/$2", $uri );
    }
}
