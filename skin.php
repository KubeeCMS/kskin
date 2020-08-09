<?php # -*- coding: utf-8 -*-
/**
 * KCMS Admin UI!
 *
 * @package      kskin
 * @author       KubeeCMS
 * @copyright    Copyright (c) 2012-2020, KubeeCMS - KUBEE
 * @license      GPL-2.0-or-later
 * @link         https://github.com/KubeeCMS/kskin/
 * @link         https://github.com/KubeeCMS/
 *
 * @wordpress-plugin
 * Plugin Name:  kSkin
 * Plugin URI:   https://github.com/KubeeCMS/kskin/
 * Description:  KCMS Admin UI!
 * Version:      1.0.2
 * Author:       KubeeCMS - KUBEE
 * Author URI:   https://github.com/KubeeCMS/
 * License:      GPL-2.0-or-later
 * License URI:  https://opensource.org/licenses/GPL-2.0
 * Text Domain:  plus_admin
 * Domain Path:  /languages/
 * Network:      true
 * Requires WP:  5.5
 * Requires PHP: 7.3
 *
 * Copyright (c) 2012-2020 KubeeCMS - KUBEE
 *
 *     This file is part of KCMS
 *
 *     KCMS is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 2 of the License, or (at your option)
 *     any later version.
 *
 *     Multisite Toolbar Additions is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Prevent direct access to this file.
 *
 * @since 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://github.com/brand-li/skin
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUS_ADMIN_VERSION', '1.0.2');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plus_admin-activator.php
 */
function cst_activate_plus_admin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plus_admin-activator.php';
	Plus_admin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plus_admin-deactivator.php
 */
function cst_deactivate_plus_admin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plus_admin-deactivator.php';
	Plus_admin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'cst_activate_plus_admin' );
register_deactivation_hook( __FILE__, 'cst_deactivate_plus_admin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plus_admin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plus_admin() {

	$plugin = new Plus_admin();
	$plugin->run();

}
run_plus_admin();



/* ------------------------------------------------------ */


/**
 * Base_Plugin class
 *
 * @class Base_Plugin The class that holds the entire Base_Plugin plugin
 */
final class Plus_admin {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.1.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the Base_Plugin class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Initializes the Base_Plugin() class
     *
     * Checks for an existing Base_Plugin() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Plus_admin();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'PLUS_ADMIN_VERSION', $this->version );
        define( 'PLUS_ADMIN_FILE', __FILE__ );
        define( 'PLUS_ADMIN_PATH', dirname( PLUS_ADMIN__FILE ) );
        define( 'PLUS_ADMIN_INCLUDES', PLUS_ADMIN__PATH . '/includes' );
        define( 'PLUS_ADMIN_URL', plugins_url( '', PLUS_ADMIN__FILE ) );
        define( 'PLUS_ADMIN_ASSETS', PLUS_ADMIN__URL . '/assets' );
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        $installed = get_option( 'plusadmin_installed' );

        if ( ! $installed ) {
            update_option( 'plusadmin_installed', time() );
        }

        update_option( 'plusadmin_version', PLUS_ADMIN_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {

        require_once PLUS_ADMIN_INCLUDES . '/Assets.php';

        if ( $this->is_request( 'admin' ) ) {
            require_once PLUS_ADMIN_INCLUDES . '/Admin.php';
        }

        if ( $this->is_request( 'frontend' ) ) {
            require_once PLUS_ADMIN_INCLUDES . '/Frontend.php';
        }

        if ( $this->is_request( 'ajax' ) ) {
            // require_once PLUS_ADMIN_INCLUDES . '/class-ajax.php';
        }

        require_once PLUS_ADMIN_INCLUDES . '/Api.php';
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {

        add_action( 'init', array( $this, 'init_classes' ) );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {

        if ( $this->is_request( 'admin' ) ) {
            $this->container['admin'] = new App\Admin();
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->container['frontend'] = new App\Frontend();
        }

        if ( $this->is_request( 'ajax' ) ) {
            // $this->container['ajax'] =  new App\Ajax();
        }

        $this->container['api'] = new App\Api();
        $this->container['assets'] = new App\Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'plusadmin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined( 'DOING_AJAX' );

            case 'rest' :
                return defined( 'REST_REQUEST' );

            case 'cron' :
                return defined( 'DOING_CRON' );

            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

} // Base_Plugin

$plusadmin = Plus_admin::init();
