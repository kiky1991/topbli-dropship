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
            add_filter('woocommerce_account_settings', array($this, 'add_settings'));

            // show field in user profile
            add_action('show_user_profile', array($this, 'show_form_dropship'), 30, 1);
            add_action('edit_user_profile', array($this, 'show_form_dropship'), 30, 1);

            // // save update profile
            add_action('personal_options_update', array($this, 'save_form_dropship'), 1);
            add_action('edit_user_profile_update', array($this, 'save_form_dropship'), 1);

            add_action('admin_notices', array($this, 'display_flash_notices'), 12);

            $this->helper      = new TOPDROP_Helper();
        }

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

        public function show_form_dropship($user)
        {
            $dropship_name = get_user_meta($user->ID, 'topdrop_dropship_name', true);
            $dropship_phone = get_user_meta($user->ID, 'topdrop_dropship_phone', true);
            $dropship_auto = get_user_meta($user->ID, 'topdrop_dropship_auto', true);

            include_once TOPDROP_PLUGIN_PATH . '/views/admin-user-profile.php';
        }

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

        protected function add_flash_notice($message = '', $type = 'success', $p = true)
        {
            $old_notice = get_option('my_flash_notices', array());
            $old_notice[] = array(
                'type'      => !empty($type) ? $type : 'success',
                'message'   => $p ? '<p>' . $message . '</p>' : $message,
            );
            update_option('my_flash_notices', $old_notice, false);
        }

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
