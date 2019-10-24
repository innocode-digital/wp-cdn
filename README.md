# CDN

### Description

Change all static files URLs to CDN domain.

### Install

- Preferable way is to use [Composer](https://getcomposer.org/):

    ````
    composer require innocode-digital/wp-cdn
    ````

    By default it will be installed as [Must Use Plugin](https://codex.wordpress.org/Must_Use_Plugins).
    But it's possible to control with `extra.installer-paths` in `composer.json`.

- Alternate way is to clone this repo to `wp-content/mu-plugins/` or `wp-content/plugins/`:

    ````
    cd wp-content/plugins/
    git clone git@github.com:innocode-digital/wp-cdn.git
    cd wp-cdn/
    composer install
    ````

If plugin was installed as regular plugin then activate **CDN** from Plugins page 
or [WP-CLI](https://make.wordpress.org/cli/handbook/): `wp plugin activate wp-cdn`.

### Usage

Add required constant (usually to `wp-config.php`):

````
define( 'CDN', '' ); // E.g. 'xxxxxx.cloudfront.net'
````

### Documentation

It's possible to disable URL replace at all or for certain cases:

````
add_filter( 'innocode_cdn_should_replace_url', function ( $should_replace_url, $uri ) {
    return $should_replace_url;
}, 10, 2 );
````

---

It's possible to control a list of extensions:

````
add_filter( 'innocode_cdn_extensions', function ( array $extensions ) {
    return $extensions;
} );
````

---

It's possible to control a list of hosts:

````
add_filter( 'innocode_cdn_hosts', function ( array $hosts ) {
    return $hosts;
} );
````

---
