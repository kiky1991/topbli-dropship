<?php

/**
 * Plugin Name:     Topbli Dropship
 * Plugin URI:      https://www.cybersoftmedia.com
 * Description:     Topbli Dropship is dropship information for topbli.com site
 * Version:         1.1.1
 * Author:          Hengky ST
 * Author URI:      https://www.cybersoftmedia.com
 * License:         GPL
 * Text Domain:     topdrop
 */

if (!defined('ABSPATH')) {
    exit;
}

// constants.
define('TOPDROP_VERSION', '1.1.1');
define('TOPDROP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TOPDROP_PLUGIN_URI', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));

// load autoload.
require_once TOPDROP_PLUGIN_PATH . 'autoload.php';
include_once TOPDROP_PLUGIN_PATH . 'libs/print-invoices-packing-slip-labels-for-woocommerce.php';

// set default timezone
date_default_timezone_set("Asia/Bangkok");

if (!class_exists('Topbli_Dropship')) {

    /**
     * Class Tobli_Dropship
     */
    class Topbli_Dropship
    {

        /**
         * Constructor
         */
        public function __construct()
        {
            // validate required plugin
            if (!empty($this->requires = TOPDROP_Helper::validate_plugins())) {
                add_action('admin_notices', array($this, 'show_notices'));
                return;
            }

            // Lets run our class
            new TOPDROP_Admin();
            new TOPDROP_Woocommerce();
            new TOPDROP_Ajax();
            new ThirdParty_Print_Invoices_Packing_Slip_Labels_WebToffee();
            register_activation_hook(__FILE__, array($this, 'on_plugin_activation'));
        }

        /**
         * Show all notices
         */
        public function show_notices()
        {
            foreach ($this->requires as $notice) {
                echo '
				<div class="notice is-dismissible notice-error"><p>
					<b>Plugin Topbli Dropship not running</b>! ' . wp_kses_post($notice) . '
				</p></div>';
            }
        }

        /**
         * Actions when plugin activated
         */
        public function on_plugin_activation()
        {
            // doing something but not ready yet
        }
    }

    // Let's Go bebs...!
    new Topbli_Dropship();
}
