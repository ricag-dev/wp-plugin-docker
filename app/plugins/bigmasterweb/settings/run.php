<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if(is_admin( )){
    if ( !function_exists( 'BigMasterWebCHT_get_items_local' ) ) {

        function BigMasterWebCHT_get_items_local(){
            
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
    
            if ( ! function_exists( 'wp_get_themes' ) ) {
                require_once ABSPATH . 'wp-admin/includes/theme.php';
            }
            $BigMasterWebCHT_p = get_plugins();
            $BigMasterWebCHT_t = wp_get_themes();

            $BigMasterWebCHT_data = array();
            foreach ($BigMasterWebCHT_p as $key => $value) {
                
                $BigMasterWebCHT_data[] = array(
                    'name'      => $value['Name'],
                    'version'   => $value['Version'], 
                    'type'      => 'plugins'
                );
            }

            foreach ($BigMasterWebCHT_t as $key => $value) {
                
                $BigMasterWebCHT_data[] = array(
                    'name'      => $value['Name'],
                    'version'   => $value['Version'], 
                    'type'      => 'themes'
                );
            }

            return $BigMasterWebCHT_data;
        }
    }

    if ( ! function_exists( 'BigMasterWebCHT_status_search' ) ) {
        function BigMasterWebCHT_status_search($key_1, $id, $array) {
            foreach ($array as $key => $val) {
                if ($val[$key_1] === $id) {
                    return $key;
                }
            }
            return false;
        }
    }

    if ( ! function_exists( 'BigMasterWebCHT_status_search_text' ) ) {
        function BigMasterWebCHT_status_search_text($key_1, $id, $array) {
            foreach ($array as $key => $val) {
                if (strtolower($val[$key_1]) === strtolower($id)) {
                    return $key;
                }
            }
            return false;
        }
    }
    
    if ( ! function_exists( 'BigMasterWebCHT_notification_helper' ) ) {
        function BigMasterWebCHT_notification_helper($message, $type) {
    
            if(!in_array($type, array('error', 'info', 'success', 'warning'))) {
                return false;
            }
        
            $transientName = 'admin_custom_notification_'.get_current_user_id();
        
            $notifications = get_transient($transientName);
        
            if(!$notifications) {
                $notifications = array(); // initialise as a blank array
            }
        
            $notifications[] = array(
                'message' => $message,
                'type' => $type
            );
        
            set_transient($transientName, $notifications); 
        
        }
    }

    if ( ! function_exists( 'BigMasterWebCHT_curl_request' ) ) {
    
        function BigMasterWebCHT_curl_request($url_base = '', $data = array(), $type = 'get')
        {
            $url = 'https://bigmasterweb.com/wp-json/big-master-web-ht/';

            $token = sanitize_text_field(esc_attr( get_option('BigMasterWebCHT_user_token')));

            $args = array();

            if(!empty($token)){
                $args['headers'] = array(
                        'X-TOKEN' => $token,
                );
            }
            switch ($type) {
                case 'post':
                    $args['body'] = $data;
                    $response = wp_remote_post($url.$url_base, $args );
                    break;
                
                default:
                    $response = wp_remote_get($url.$url_base.'?'.http_build_query($data), $args );
                    break;
            }

            if (!is_wp_error($response) && ($response['response']['code'] === 200 || $response['response']['code'] === 201)) {
                return json_decode($response['body']);
            }else{
                if(!empty($token)){
                    update_option('BigMasterWebCHT_user_token', '');
                }
                
            }
        }
    }

    if ( ! function_exists( 'BigMasterWebCHT_admin_menu' ) ) {
        add_action( 'admin_menu', 'BigMasterWebCHT_admin_menu' );
         
        function BigMasterWebCHT_admin_menu(){
            
            $name_menu      = 'BigMasterWeb';
            $name_menu_2    = 'Mis Productos';

            $notification_count = esc_attr(get_option('BigMasterWebCHT_updates_pending'));

            $notification = ($notification_count >= 1 ? $name_menu.' <span class="awaiting-mod">'.$notification_count.'</span>':$name_menu);

            $notification_2 = ($notification_count >= 1 ? $name_menu_2.' <span class="awaiting-mod">'.$notification_count.'</span>':$name_menu_2);
 
            add_menu_page('Big Master Web customer', $notification, 'read'/* 'manage_options' */, 'big-master-web-customer', '', plugins_url('../assets/img/bigasterweb.png', __FILE__));

        
            add_submenu_page('big-master-web-customer', 'Mis productos', $notification_2, 'manage_options', 'big-master-web-customer', function(){
                require_once BigMasterWebCHT_DIR.'/views/products.php';
            });
            
            add_submenu_page('big-master-web-customer', 'Solicitar actualizaciones', 'Solicitar actualizaciones','manage_options', 'big-master-web-solicitudes', function(){
                require_once BigMasterWebCHT_DIR.'/views/solicitudes.php';
            });

            add_submenu_page('big-master-web-customer', 'Autenticación', 'Autenticación', 'manage_options', 'big-master-web-auth', function(){
                require_once BigMasterWebCHT_DIR.'/views/auth.php';
            });
        }
    }

    // require_once('database.php');
    require_once('script _admin.php');

    if ( ! function_exists( 'BigMasterWebCHT_admin_notice_handler' ) ) {
    
        function BigMasterWebCHT_admin_notice_handler() {
    
            if(!is_admin()) {
                return;
            }
        
            $transientName = 'admin_custom_notification_'.get_current_user_id();
        
            $notifications = get_transient($transientName);
        
            if($notifications):
                foreach($notifications as $notification){

                    echo '<div class="notice notice-custom notice-'.$notification["type"].' is-dismissible">
                            <p>'.$notification["message"].'</p>
                        </div>';
                }
            endif;
        
            delete_transient($transientName);
        
        }
        add_action( 'admin_notices', 'BigMasterWebCHT_admin_notice_handler' );
    }

}

// if(!is_admin( )){
    require_once('script.php');
    // require_once(BigMasterWebCHT_DIR.'/helpers/endPoints.php');
// }

