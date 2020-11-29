<?php

/**
 * Form class
 */
class TOPDROP_Form
{
    /**
     * TOPDROP_Form::__construct
     * 
     * Main construct
     */
    public function __construct()
    {
        $this->user_id = get_current_user_id();
        $this->helper = new TOPDROP_Helper();
    }

    /**
     * TOPDROP_Form::form_dropship_information
     * 
     * Form for dropship information my-account page
     * 
     * @return  array
     */
    public function form_dropship_information()
    {
        $required_name = true;
        $required_phone = false;

        if (is_admin()) {
            $screen = get_current_screen();
            if (!is_null($screen) && $screen->id == 'shop_order') {
                global $post;

                $dropship_name = get_post_meta($post->ID, '_topdrop_dropship_name', true);
                $dropship_phone = get_post_meta($post->ID, '_topdrop_dropship_phone', true);
            }
        } else {
            $dropship_name = get_user_meta($this->user_id, 'topdrop_dropship_name', true);
            $dropship_phone = get_user_meta($this->user_id, 'topdrop_dropship_phone', true);
        }

        return apply_filters('form_dropship_information', array(
            'topdrop_name' => array(
                'field' => array(
                    'type'        => 'text',
                    'label'       => __('Name', 'topdrop'),
                    'placeholder' => __('', 'topdrop'),
                    'required'    => $required_name,
                    'class'       => array('woocommerce-form-row', 'form-row-first')
                ),
                'value' => $dropship_name
            ),
            'topdrop_phone' => array(
                'field' => array(
                    'type'        => 'tel',
                    'label'       => __('Phone', 'topdrop'),
                    'placeholder' => __('', 'topdrop'),
                    'required'    => $required_phone,
                    'class'       => array('woocommerce-form-row', 'form-row-last')
                ),
                'value' => $dropship_phone
            ),
        ));
    }

    /**
     * TOPDROP_Form::form_dropship_setting
     * 
     * Form for dropship setting my-account page
     * 
     * @return  array
     */
    public function form_dropship_setting()
    {
        $dropship_auto = get_user_meta($this->user_id, 'topdrop_dropship_auto', true);
        $value = ($dropship_auto === 'yes') ? 1 : 0;

        return apply_filters('form_dropship_setting', array(
            'topdrop_auto' =>
            array(
                'field' => array(
                    'type'        => 'checkbox',
                    'label'       => __('Auto Dropship', 'topdrop'),
                    'placeholder' => __('Enable / Disable auto dropship', 'topdrop'),
                    'required'    => false,
                    'class'       => array('input-checkbox', 'woocommerce-form-row', 'form-row-wide')
                ),
                'value' => $value
            ),
        ));
    }

    /**
     * TOPDROP_Form::form_dropship_settings
     * 
     * Form for dropship global setting page
     * 
     * @return  array
     */
    public function form_dropship_settings()
    {
        return apply_filters('form_dropship_settings', array(
            'topdrop_auto' =>
            array(
                'field' => array(
                    'type'        => 'checkbox',
                    'label'       => __('Auto Dropship', 'topdrop'),
                    'placeholder' => __('Enable / Disable auto dropship', 'topdrop'),
                    'required'    => false,
                    'class'       => array('input-checkbox', 'woocommerce-form-row', 'form-row-wide')
                ),
                'value' => ''
            ),
        ));
    }

    /**
     * TOPDROP_Form::form_dropship_settings
     * 
     * Form for dropship global setting page
     * 
     * @return  array
     */
    public function form_dropship_privilege()
    {
        $dropship_auto = get_user_meta($this->user_id, 'topdrop_dropship_auto', true);
        $value = ($dropship_auto === 'yes') ? 1 : 0;

        return apply_filters('form_dropship_privilege', array(
            'topdrop_privilege' =>
            array(
                'field' => array(
                    'type'        => 'checkbox',
                    'label'       => __('Use dropship information ?', 'topdrop'),
                    'placeholder' => __('', 'topdrop'),
                    'required'    => false,
                    'class'       => array('input-checkbox', 'woocommerce-form-row', 'form-row-wide')
                ),
                'value' => $value
            ),
        ));
    }

    /**
     * TOPDROP_Form::get_fields
     * 
     * Foreach all fields
     * 
     * @return  HTML
     */
    public function get_fields($function = '')
    {
        if (!empty($function)) {
            $fields = call_user_func(array($this, $function));
            foreach ($fields as $key => $field_args) {
                woocommerce_form_field($key, $field_args['field'], $field_args['value']);
            }
        }
    }
}
