<?php

if (!class_exists('ThirdParty_Print_Invoices_Packing_Slip_Labels_WebToffee')) {

    /**
     * Webtofee plugin packing slip
     */
    class ThirdParty_Print_Invoices_Packing_Slip_Labels_WebToffee
    {

        /**
         * Constructor
         */
        public function __construct()
        {
            if ($this->is_invoice_packing_slip_webtoffee()) {
                add_filter('wf_module_convert_to_design_view_html', array($this, 'replace_from_toffee_view_html'), 50, 3);
                add_filter('wf_module_generate_template_html', array($this, 'replace_from_toffee_template_html'), 50, 6);
                add_filter('wf_pklist_add_additional_info', array($this, 'wt_pklist_add_additional_data'), 50, 3);
            }
        }

        /**
         * Check if print invoice packing slip webtoffee is active
         *
         * @return boolean Is active.
         */
        public function is_invoice_packing_slip_webtoffee()
        {
            return in_array('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true);
        }

        /**
         * TOPDROP_Admin::replace_from_toffee_view_html
         * 
         * Replace text from address
         * @param   array   $find_replace  array data address
         * @param   html    $html          HTML format address
         * @param   string  $template_type type of template
         * 
         * @return  array
         */
        public function replace_from_toffee_view_html($find_replace, $html, $template_type)
        {
            global $woocommerce, $post;
            $order = new WC_Order($post->ID);

            return $this->search_replace($find_replace, $order);
        }

        /**
         * TOPDROP_Admin::replace_from_toffee_template_html
         * 
         * Replace text from address
         * @param   array   $find_replace  array data address
         * @param   html    $html          HTML format address
         * @param   string  $template_type type of template
         * @param   array   $order         The order
         * 
         * @return  array
         */
        public function replace_from_toffee_template_html($find_replace, $html, $template_type, $order, $box_packing, $order_package)
        {
            return $this->search_replace($find_replace, $order);
        }

        /**
         * TOPDROP_Admin::search_replace
         * 
         * Replace text from address
         * @param   array   $find_replace  array data address
         * @param   array   $order         The order
         * 
         * @return  array
         */
        private function search_replace($find_replace, $order = null)
        {
            // var_dump($find_replace); die;
            if (!is_null($order)) {
                $dropship_name = get_post_meta($order->get_id(), '_topdrop_dropship_name', true);
                $dropship_phone = get_post_meta($order->get_id(), '_topdrop_dropship_phone', true);
                $format_phone = !empty($dropship_phone) ? "- $dropship_phone" : '';
                if (!empty($dropship_name)) {
                    $find_replace['[wfte_from_address]'] = "$dropship_name $format_phone";
                    $find_replace['[wfte_additional_data]'] = "[wfte_additional_data]";
                    $find_replace['[wfte_weight]'] = "[wfte_weight]";
                    $find_replace['[wfte_box_name]'] = "[wfte_box_name]dd";
                }
            }

            return $find_replace;
        }

        public function wt_pklist_add_additional_data($additional_info, $template_type, $order)
        {
            if (is_null($order)) {
                return $additional_info;
            }

            $additional_info .= 'Shipping Fee: ' . wc_price($order->get_shipping_total());
            $additional_info .= '<br/>Shipping Service: ' . $order->get_shipping_method();
            return $additional_info;
        }
    }
}
