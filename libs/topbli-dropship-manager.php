<?php

if (!class_exists('Topbli_Dropship_Manager')) {

    /**
     * Class Tobli_Dropship
     */
    class Topbli_Dropship_Manager
    {

        /**
         * Constructor
         */
        public function __construct()
        {
            add_action('init', array($this, 'check_update'));
        }

        public function check_update()
        {
            require TOPDROP_PLUGIN_PATH . 'libs/plugin-update-checker/plugin-update-checker.php';
            $puc = Puc_v4_Factory::buildUpdateChecker(
                'https://github.com/kiky1991/topbli-dropship/',
                __FILE__,
                'topbli-dropship'
            );

            $puc->setAuthentication('85ebd4be817d247ca972ff6654b91f6178726f2d');
            $puc->setBranch('master');
        }
    }

    new Topbli_Dropship_Manager();
}
