<?php

if (!class_exists('TOPDROP_Helper')) {

    /**
     * Helper Class
     */
    class TOPDROP_Helper
    {

        /**
         * Minimum PHP version required
         *
         * @var string
         */
        private $min_php = '7.0';

        /**
         * Constructor
         */
        public function __construct()
        {
            // hati2 ada function dibawah validate plugins
        }

        /**
         * Check if woocommerce is active
         *
         * @return boolean Is active.
         */
        public function is_woocommerce_active()
        {
            return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true);
        }

        /**
         * Check if the PHP version is supported
         *
         * @return bool
         */
        public function is_supported_php()
        {
            if (version_compare(PHP_VERSION, $this->min_php, '<=')) {
                return false;
            }

            return true;
        }

        /**
         * Validate require plugins
         * 
         * @return boolean
         */
        public static function validate_plugins()
        {
            $error = array();

            if (!(new self)->is_supported_php()) {
                $error[] = 'Minimum PHP Required for this plugin ' . (new self)->min_php;
            }

            if (!(new self)->is_woocommerce_active()) {
                $error[] = 'Plugin Woocommerce is not active, install and active first!';
            }

            return $error;
        }

        public function sendmail($to = '', $subject = '', $body = '', $attachment = array())
        {
            if (empty($to) || empty($body)) {
                return false;
            }

            // set header to html email
            $header = 'Content-Type: text/html; charset=UTF-8';

            // Get woocommerce mailer from instance
            $mailer = WC()->mailer();
            $wrapped_message = $mailer->wrap_message($subject, $body); // Wrap message using woocommerce html email templ
            $wc_email = new WC_Email; // Create new WC_Email instance
            $html_message = $wc_email->style_inline($wrapped_message); // Style the wrapped message with woocommerce inline styles

            return wp_mail($to, $subject, $html_message, $header, $attachment);
        }
    }
}
