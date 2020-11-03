<?php

if (!class_exists('TOPDROP_Ajax')) {

    /**
     * Ajax Class
     */
    class TOPDROP_Ajax
    {

        /**
         * Constructor
         */
        public function __construct()
        {
            add_action('wp_ajax_topdrop_get_dropship', array($this, 'get_dropship'));
        }

        public function get_dropship()
        {
            check_ajax_referer('topdrop-get-dropship-nonce', 'topdrop_nonce');

            if (!isset($_POST['customer']) || empty($_POST['customer'])) {
                wp_die(-1);
            }

            $user_id = sanitize_text_field(wp_unslash($_POST['customer']));

            if ($user_id > 0) {
                $dropship_name = get_user_meta($user_id, 'topdrop_dropship_name', true);
                $dropship_phone = get_user_meta($user_id, 'topdrop_dropship_phone', true);

                wp_send_json(
                    array(
                        'success'   => true,
                        'message'   => 'OK',
                        'results'      => array(
                            'name'  => $dropship_name,
                            'phone' => $dropship_phone
                        )
                    )
                );
            }

            wp_send_json(
                array(
                    'success'   => false,
                    'message'   => 'Wrong request',
                    'data'      => ''
                )
            );
        }
    }
}
