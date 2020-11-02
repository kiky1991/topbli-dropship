<?php

/**
 * Plugin Name:     Topbli Dropship
 * Plugin URI:      https://www.cybersoftmedia.com
 * Description:     Topbli Dropship is dropship information for topbli.com site
 * Version:         1.0.0
 * Author:          Hengky ST
 * Author URI:      https://www.cybersoftmedia.com
 * License:         GPL
 * Text Domain:     topdrop
 */

if (!defined('ABSPATH')) {
    exit;
}

// constants.
define('TOPDROP_VERSION', '1.0.0');
define('TOPDROP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TOPDROP_PLUGIN_URI', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));

// load files.
require_once TOPDROP_PLUGIN_PATH . 'autoload.php';

// set default timezone
date_default_timezone_set("Asia/Bangkok");

if (!class_exists('Tobli_Dropship')) {

    /**
     * Class Tobli_Dropship
     */
    class Tobli_Dropship
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
            // new TOPDROP_Ajax();
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
    new Tobli_Dropship();
}


/**
 * Create the section beneath the products tab
 **/
add_filter('woocommerce_get_sections_products', 'wcslider_add_section');
function wcslider_add_section($sections)
{
    $sections['wcslider'] = __('WC Slider', 'text-domain');
    return $sections;
}

/**
 * Add settings to the specific section we created before
 */
add_filter('woocommerce_get_settings_products', 'wcslider_all_settings', 10, 2);
function wcslider_all_settings($settings, $current_section)
{
    /**
     * Check the current section is what we want
     **/
    if ($current_section == 'wcslider') {
        $settings_slider = array();
        // Add Title to the Settings
        $settings_slider[] = array('name' => __('WC Slider Settings', 'text-domain'), 'type' => 'title', 'desc' => __('The following options are used to configure WC Slider', 'text-domain'), 'id' => 'wcslider');
        // Add first checkbox option
        $settings_slider[] = array(
            'name'     => __('Auto-insert into single product page', 'text-domain'),
            'desc_tip' => __('This will automatically insert your slider into the single product page', 'text-domain'),
            'id'       => 'wcslider_auto_insert',
            'type'     => 'checkbox',
            'css'      => 'min-width:300px;',
            'desc'     => __('Enable Auto-Insert', 'text-domain'),
        );
        // Add second text field option
        $settings_slider[] = array(
            'name'     => __('Slider Title', 'text-domain'),
            'desc_tip' => __('This will add a title to your slider', 'text-domain'),
            'id'       => 'wcslider_title',
            'type'     => 'text',
            'desc'     => __('Any title you want can be added to your slider with this option!', 'text-domain'),
        );

        $settings_slider[] = array('type' => 'sectionend', 'id' => 'wcslider');
        return $settings_slider;

        /**
         * If not, return the standard settings
         **/
    } else {
        return $settings;
    }
}
