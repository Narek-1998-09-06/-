<?php

class APPMAKER_WC_General_Helper {   


    public static function get_custom_html(){
        
        $output ='<style>.whb-sticky-header.whb-clone.whb-main-header.whb-sticked{box-shadow: none !important;}</style>';

        $options = get_option('appmaker_wc_custom_settings', array());
        if(!empty($options) && isset($options['custom_webview_head'])){

            $output = $options['custom_webview_head'];
        }
        return base64_encode($output);
    }    

}
new APPMAKER_WC_General_Helper();
