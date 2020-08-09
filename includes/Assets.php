<?php
namespace App;

/**
 * Scripts and Styles Class
 */
class Assets {

    function __construct() {

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'register' ], 5 );
        } else {
            add_action( 'wp_enqueue_scripts', [ $this, 'register' ], 5 );
        }
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register() {
        $this->register_scripts( $this->get_scripts() );
        $this->register_styles( $this->get_styles() );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function register_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : PLUSADMIN_VERSION;

            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, PLUSADMIN_VERSION );
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts() {
        $prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

        $scripts = [
            'plusadmin-runtime' => [
                'src'       => PLUSADMIN_ASSETS . '/js/runtime.js',
                'version'   => filemtime( PLUSADMIN_PATH . '/assets/js/runtime.js' ),
                'in_footer' => true
            ],
            'plusadmin-vendor' => [
                'src'       => PLUSADMIN_ASSETS . '/js/vendors.js',
                'version'   => filemtime( PLUSADMIN_PATH . '/assets/js/vendors.js' ),
                'in_footer' => true
            ],
            'plusadmin-frontend' => [
                'src'       => PLUSADMIN_ASSETS . '/js/frontend.js',
                'deps'      => [ 'jquery', 'plusadmin-vendor', 'plusadmin-runtime' ],
                'version'   => filemtime( PLUSADMIN_PATH . '/assets/js/frontend.js' ),
                'in_footer' => true
            ],
            'plusadmin-admin' => [
                'src'       => PLUSADMIN_ASSETS . '/js/admin.js',
                'deps'      => [ 'jquery', 'plusadmin-vendor', 'plusadmin-runtime' ],
                'version'   => filemtime( PLUSADMIN_PATH . '/assets/js/admin.js' ),
                'in_footer' => true
            ]
        ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles() {

        $styles = [
            'plusadmin-style' => [
                'src' =>  PLUSADMIN_ASSETS . '/css/style.css'
            ],
            'plusadmin-frontend' => [
                'src' =>  PLUSADMIN_ASSETS . '/css/frontend.css'
            ],
            'plusadmin-admin' => [
                'src' =>  PLUSADMIN_ASSETS . '/css/admin.css'
            ],
        ];

        return $styles;
    }

}
