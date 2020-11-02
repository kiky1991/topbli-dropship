<?php

/**
 * TOPDROP_Woocommerce class
 */
class TOPDROP_Woocommerce
{

    /**
     * TOPDROP_Woocommerce::__construct
     */
    public function __construct()
    {
        $this->helper = new TOPDROP_Helper();

        // add_action("wp_enqueue_scripts", array($this, 'register_assets_myaccount'));
        add_filter('woocommerce_account_menu_items', array($this, 'custom_nav_menu'), 99, 1);
        add_filter('woocommerce_settings_pages', array($this, 'add_endpoint_my_account'), 10, 1);
        add_filter('woocommerce_get_query_vars', array($this, 'add_woocommerce_query_vars'));
        add_filter('woocommerce_account_menu_items', array($this, 'menu_items'), 10, 1);
        foreach ($this->get_custom_menu() as $key => $value) {
            add_filter('woocommerce_endpoint_' . $key, array($this, 'woocommerce_endpoint_title'), 10, 2);
            add_action('woocommerce_account_' . $key . '_endpoint', $value['callback']);
        }
        add_action('init', array($this, 'flush_rules'));

        // save dropship
        add_action('template_redirect', array($this, 'save_dropship'));
        add_action('woocommerce_order_details_after_customer_details', array($this, 'dropship_information'), 10, 1);
    }

    /**
     * TOPDROP_Woocommerce::register_assets_myaccount
     * 
     * Register front assets
     * @access  public
     * 
     * @return  html
     */
    public function register_assets_myaccount()
    {
        if (is_wc_endpoint_url('dropship')) {
            wp_enqueue_style('dropship', TOPDROP_PLUGIN_URI . '/assets/css/topup-balance.css', '', ATKPD_VERSION);
            wp_enqueue_script('dropship', TOPDROP_PLUGIN_URI . "/assets/js/topup-balance.js", array('jquery'), ATKPD_VERSION);
            wp_localize_script(
                'dropship',
                'topdrop',
                array(
                    'url'       => admin_url('admin-ajax.php')
                )
            );
            wp_localize_script(
                'dropship',
                'atkpd_nonces',
                array(
                    'load_history_balance' => wp_create_nonce('atkpd-submit-topup-balance-nonce'),
                    'load_form_topup' => wp_create_nonce('atkpd-topup-balance-nonce'),
                    'submit_topup' => wp_create_nonce('atkpd-submit-topup-balance-nonce'),
                    'topup_confirmation' => wp_create_nonce('atkpd-topup-confirmation-nonce'),
                )
            );
        }
    }

    /**
     * TOPDROP_Woocommerce::custom_nav_menu
     * 
     * Custom navigation menu set or unset or modify
     * @access  public
     * @param   array   $items  menu items
     * 
     * @return  arrray  $items  manu items modified    
     */
    public function custom_nav_menu($items)
    {
        unset($items['downloads']);
        $items['orders'] = __('My Orders', 'topdrop');
        return $items;
    }

    /**
     * TOPDROP_Woocommerce::get_custom_menu
     * 
     * Add new custom menu my-account navigation
     * @access  public
     * 
     * @return  arrray  $menu  new menu items    
     */
    private function get_custom_menu()
    {
        $menu = array(
            'dropship' => array(
                'title'         => __('Dropship', 'topdrop'),
                'description'   => __('Endpoint for the "My account &rarr; dropship page.', 'topdrop'),
                'callback'      => array($this, 'render_page_dropship')
            )
        );
        return apply_filters('topdrop_custom_menu_my_account', $menu);
    }

    /**
     * TOPDROP_Woocommerce::flush_rules
     * 
     * Clear cache after add new menu
     * @access  public
     * 
     * @return  void    
     */
    public function flush_rules()
    {
        foreach ($this->get_custom_menu() as $key => $value) {
            add_rewrite_endpoint($key, EP_ROOT | EP_PAGES);
        }

        flush_rewrite_rules();
    }

    /**
     * TOPDROP_Woocommerce::add_endpoint_my_account
     * 
     * Add query vars woocommerce setting field
     * @access  public
     * @param   array  $settings      the menu field query var
     * 
     * @return  array  $sorted_setting      new menu field query var
     */
    public function add_endpoint_my_account($settings)
    {
        $menus = array();
        foreach ($this->get_custom_menu() as $key => $value) {
            $menus[] = array(
                'title'    => __($value['title'], 'topdrop'),
                'desc'     => $value['description'],
                'id'       => 'woocommerce_myaccount_' . $key . '_endpoint',
                'type'     => 'text',
                'default'  => $key,
                'desc_tip' => true,
            );
        }

        $sorted_setting = array();
        foreach ($settings as $setting) {
            $sorted_setting[] = $setting;
            if (isset($setting['default']) && $setting['default'] == 'edit-account') {
                foreach ($menus as $menu) {
                    $sorted_setting[] = $menu;
                }
            }
        }

        return $sorted_setting;
    }

    /**
     * TOPDROP_Woocommerce::add_woocommerce_query_vars
     * 
     * Add query vars woocommerce setting menu
     * @access  public
     * @param   array  $query_vars      the items query var
     * 
     * @return  array  $query_vars      new items query var
     */
    public function add_woocommerce_query_vars($query_vars)
    {
        foreach ($this->get_custom_menu() as $key => $value) {
            $query_vars[$key] = get_option('woocommerce_myaccount_' . $key . '_endpoint', $key);
        }
        return $query_vars;
    }

    /**
     * TOPDROP_Woocommerce::menu_items
     * 
     * Custom menu items array with sorted rules
     * @access  public
     * @param   array  $items      the items menu
     * 
     * @return  array  $sorted_items  new sorted items
     */
    public function menu_items($items)
    {
        $sorted_items = array();
        foreach ($items as $key => $label) {
            $sorted_items[$key] = $label;
            if ('edit-account' == $key) {
                foreach ($this->get_custom_menu() as $key => $value) {
                    $sorted_items[$key] = apply_filters($key . '_account_menu_title', __($value['title'], 'atkpd'));
                }
            }
        }

        return $sorted_items;
    }

    /**
     * TOPDROP_Woocommerce::woocommerce_endpoint_title
     * 
     * Custom menu title
     * @access  public
     * @param   string  $title      the title menu
     * @param   string  $endpoint   the slug menu
     * 
     * @return  string  $title  new title modified
     */
    public function woocommerce_endpoint_title($title, $endpoint)
    {
        foreach ($this->get_custom_menu() as $key => $value) {
            return apply_filters($key . '_account_menu_title', __($value['title'], 'topdrop'));
        }
    }

    /**
     * TOPDROP_Woocommerce::render_page_dropship
     * 
     * Show page for dropship
     * @access  public
     * 
     * @return  html
     */
    public function render_page_dropship()
    {
        $form = new TOPDROP_Form();

        include_once TOPDROP_PLUGIN_PATH . 'views/my-account-dropship.php';
    }

    /**
     * TOPDROP_Woocommerce::save_dropship
     * 
     * Save dropship form
     * @access  public
     * 
     * @return  html
     */
    public function save_dropship()
    {
        $nonce_value = wc_get_var($_REQUEST['topdrop_save_dropship_nonce'], wc_get_var($_REQUEST['_wpnonce'], '')); // @codingStandardsIgnoreLine.

        if (!wp_verify_nonce($nonce_value, 'topdrop_save_dropship')) {
            return;
        }

        if (empty($_POST['action']) || 'topdrop_save_dropship' !== $_POST['action']) {
            return;
        }

        wc_nocache_headers();

        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return;
        }

        $name = isset($_POST['topdrop_name']) ? sanitize_text_field(wp_unslash($_POST['topdrop_name'])) : '';   // WPCS: Input var okay, CSRF ok.
        $phone = isset($_POST['topdrop_phone']) ? sanitize_text_field(wp_unslash($_POST['topdrop_phone'])) : '';   // WPCS: Input var okay, CSRF ok.
        $auto = isset($_POST['topdrop_auto']) ? sanitize_text_field(wp_unslash($_POST['topdrop_auto'])) : 0;   // WPCS: Input var okay, CSRF ok.

        update_user_meta($user_id, 'topdrop_dropship_name', $name);
        update_user_meta($user_id, 'topdrop_dropship_phone', $phone);
        update_user_meta($user_id, 'topdrop_dropship_auto', ($auto == 1) ? 'yes' : 'no');

        wc_add_notice(__('Data dropship has been saved.', 'topdrop'));
        wp_safe_redirect(wc_get_page_permalink('myaccount') . 'dropship');
        exit;
    }

    public function dropship_information($order)
    {
        $order_id = $order->get_id();
        $dropship_name = get_post_meta($order_id, '_topdrop_dropship_name', true);
        $dropship_phone = get_post_meta($order_id, '_topdrop_dropship_phone', true);

        if (!empty($dropship_name) && !empty($dropship_phone)) {
            include_once TOPDROP_PLUGIN_PATH . 'views/view-order-dropship.php';
        }
    }
}
