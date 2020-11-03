<?php

if (!class_exists('TOPDROP_Admin')) {

    /**
     * Admin Class
     */
    class TOPDROP_Admin
    {

        /**
         * Constructor
         */
        public function __construct()
        {
            // assets
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 10);

            // woocommerce settings
            add_filter('woocommerce_account_settings', array($this, 'add_settings'));

            // user profile
            add_action('show_user_profile', array($this, 'show_form_dropship'), 30, 1);
            add_action('edit_user_profile', array($this, 'show_form_dropship'), 30, 1);
            add_action('personal_options_update', array($this, 'save_form_dropship'), 1);
            add_action('edit_user_profile_update', array($this, 'save_form_dropship'), 1);

            // admin order details form
            add_action('woocommerce_admin_order_data_after_order_details', array($this, 'admin_order_detail'), 12, 1);
            add_action('woocommerce_process_shop_order_meta', array($this, 'save_admin_order_detail'), 12, 1);
            add_action('woocommerce_new_order', 'admin_order_detail',  10, 1);

            // notices
            add_action('admin_notices', array($this, 'display_flash_notices'), 12);

            $this->helper      = new TOPDROP_Helper();
        }

        /**
         * Validate current admin screen
         *
         * @param   string  $page   page to validate 
         * @return  boolean         Screen is Brandplus or not.
         */
        public function validate_screen($page = '')
        {
            $screen = get_current_screen();
            if (is_null($screen)) {
                return false;
            }

            if (!empty($page) && $screen->id === $page) {
                return true;
            }

            return false;
        }

        /**
         * Enqueue Script Inventory Manager
         */
        public function enqueue_scripts()
        {
            if ($this->validate_screen('shop_order')) {
                wp_enqueue_style('topdrop-shop-order', TOPDROP_PLUGIN_URI . '/assets/css/shop-order.css', '', TOPDROP_VERSION);
                wp_enqueue_script('topdrop-shop-order', TOPDROP_PLUGIN_URI . '/assets/js/shop-order.js', array('jquery'), TOPDROP_VERSION, true);
                wp_localize_script(
                    'topdrop-shop-order',
                    'topdrop',
                    array(
                        'url' => admin_url('admin-ajax.php'),
                        'nonce' => wp_create_nonce('topdrop-get-dropship-nonce'),
                    )
                );
            }
        }

        /**
         * TOPDROP_Admin::add_settings
         * 
         * Add custom setting field
         * @param   array   $settings  array fields
         * 
         * @return  array  $new_settings  array fields    
         */
        public function add_settings($settings)
        {
            // return $settings;
            $new_settings = array();
            foreach ($settings as $setting) {
                if (isset($setting['title']) && $setting['title'] === 'Login') {
                    $setting['checkboxgroup'] = '';
                    $new_settings[] = $setting;

                    $new_settings[] = array(
                        'title'         => __('Dropship', 'topdrop'),
                        'desc'          => __('Allow customers to dropship information in checkout page', 'topdrop'),
                        'id'            => 'woocommerce_enable_customer_dropship',
                        'default'       => 'no',
                        'type'          => 'checkbox',
                        'checkboxgroup' => 'end',
                        'autoload'      => false,
                    );
                } else {
                    $new_settings[] = $setting;
                }
            }

            return $new_settings;
        }

        /**
         * TOPDROP_Admin::show_form_dropship
         * 
         * Add custom user field
         * @param   array   $user  user data
         * 
         * @return  HTML 
         */
        public function show_form_dropship($user)
        {
            $dropship_name = get_user_meta($user->ID, 'topdrop_dropship_name', true);
            $dropship_phone = get_user_meta($user->ID, 'topdrop_dropship_phone', true);
            $dropship_auto = get_user_meta($user->ID, 'topdrop_dropship_auto', true);

            include_once TOPDROP_PLUGIN_PATH . '/views/admin-user-profile.php';
        }

        /**
         * TOPDROP_Admin::save_form_dropship
         * 
         * Save form user
         * @param   array   $$user_id  user data
         * 
         * @return  void 
         */
        public function save_form_dropship($user_id)
        {
            if (!current_user_can('edit_user', $user_id)) {
                return;
            }

            if (isset($_POST['topdrop_dropship_name']) && !empty($_POST['topdrop_dropship_name'])) {
                update_user_meta($user_id, 'topdrop_dropship_name', sanitize_text_field(wp_unslash($_POST['topdrop_dropship_name'])));  // WPCS: Input var okay, CSRF ok.
            }

            if (isset($_POST['topdrop_dropship_phone']) && !empty($_POST['topdrop_dropship_phone'])) {
                update_user_meta($user_id, 'topdrop_dropship_phone', sanitize_text_field(wp_unslash($_POST['topdrop_dropship_phone'])));  // WPCS: Input var okay, CSRF ok.
            }

            if (isset($_POST['topdrop_dropship_auto']) && !empty($_POST['topdrop_dropship_auto']) && $_POST['topdrop_dropship_auto'] == 'yes') {
                update_user_meta($user_id, 'topdrop_dropship_auto', sanitize_text_field(wp_unslash($_POST['topdrop_dropship_auto'])));  // WPCS: Input var okay, CSRF ok.
            } else {
                update_user_meta($user_id, 'topdrop_dropship_auto', 'no');
            }
        }

        /**
         * TOPDROP_Admin::admin_order_detail
         * 
         * Dropship form
         * 
         * @return  HTML 
         */
        public function admin_order_detail($order)
        {
            $form = new TOPDROP_Form();

            $screen = get_current_screen();
            $user_id = $order->get_user_id();
            if ($user_id > 0 || (!empty($screen->action && $screen->action === 'add'))) {
                echo '<p class="form-field form-field-wide topdrop-load-dropship-information"><a id="topdrop-load-dropship-data">Load dropship information →</a> <span class="topbli-loader"></span></p>';
            }

            echo '<div class="form-dropship-information">';
            $form->get_fields('form_dropship_information');
            echo '</div>';
        }

        /**
         * TOPDROP_Admin::save_admin_order_detail
         * 
         * Save Dropship form
         * 
         * @return  HTML 
         */
        public function save_admin_order_detail($post_id)
        {
            if (isset($_POST['topdrop_name']) && !empty($_POST['topdrop_name'])) {
                $name = isset($_POST['topdrop_name']) ? sanitize_text_field(wp_unslash($_POST['topdrop_name'])) : '';   // WPCS: Input var okay, CSRF ok.
                update_post_meta($post_id, '_topdrop_dropship_name', $name);
            }

            if (isset($_POST['topdrop_phone']) && !empty($_POST['topdrop_phone'])) {
                $phone = isset($_POST['topdrop_phone']) ? sanitize_text_field(wp_unslash($_POST['topdrop_phone'])) : '';   // WPCS: Input var okay, CSRF ok.
                $is_correct = preg_match('/^[0-9]{6,20}$/', $phone);
                if ($phone && !$is_correct) {
                    $this->add_flash_notice('Phone number not valid.', 'error');
                    return;
                }

                update_post_meta($post_id, '_topdrop_dropship_phone', $phone);
            }
        }

        /**
         * TOPDROP_Admin::add_flash_notice
         * 
         * Add notice
         * @param   string  $message  Message
         * @param   string  $type     Error type
         * @param   string  $p        Pharagraph
         * 
         * @return  void 
         */
        protected function add_flash_notice($message = '', $type = 'success', $p = true)
        {
            $old_notice = get_option('my_flash_notices', array());
            $old_notice[] = array(
                'type'      => !empty($type) ? $type : 'success',
                'message'   => $p ? '<p>' . $message . '</p>' : $message,
            );
            update_option('my_flash_notices', $old_notice, false);
        }

        /**
         * TOPDROP_Admin::display_flash_notices
         * 
         * Display notice
         * 
         * @return  HTML 
         */
        public function display_flash_notices()
        {
            $notices = get_option('my_flash_notices', array());
            foreach ($notices as $notice) {
                printf(
                    '<div class="notice is-dismissible notice-%1$s">%2$s</div>',
                    esc_attr($notice['type']),
                    wp_kses_post($notice['message'])
                );
            }

            if (!empty($notices)) {
                delete_option("my_flash_notices", array());
            }
        }
    }
}
