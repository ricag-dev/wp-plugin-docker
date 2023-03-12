<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'BigMasterWebCHT_admin_scripts' ) ) {
    function BigMasterWebCHT_admin_scripts($hook) {

        $index          = date("ymd-Gis", filemtime( BigMasterWebCHT_DIR. 'assets/js/angular.min.js' ));
        wp_enqueue_script( 'HTSliderRA_js', plugins_url( '../assets/js/angular.min.js', __FILE__ ), array('jquery'), $index );
    
        $index_css_2          = date("ymd-Gis", filemtime( BigMasterWebCHT_DIR. '/assets/css/admin.css' ));
        wp_register_style( 'BigMasterWebCHT_admin_css', plugins_url( '../assets/css/admin.css', __FILE__ ), false, $index_css_2 );
        wp_enqueue_style ( 'BigMasterWebCHT_admin_css' );
    }
    add_action('admin_enqueue_scripts', 'BigMasterWebCHT_admin_scripts');
}

if ( ! function_exists( 'BigMasterWebCHT_cron_update_verifique' ) ) {

    function BigMasterWebCHT_cron_update_verifique() {

        if(get_option('BigMasterWebCHT_cron_verifique_version') < time() ){
            $items_local = BigMasterWebCHT_get_items_local();
            $data = BigMasterWebCHT_curl_request('products', array(
                'client_items' => json_encode($items_local)
            ), 'post');

            if(!empty($data)){
                if($data->status === 'success'){
    
                    $fin_fn = true;
                    $count_update = 0;
                    $key_update_list = [];
                    
                    if(count($data->data)){
                        foreach ($data->data as $key => $value) {
                            
                            foreach ($value->products as $key_x => $value_x) {
                                $key_local = BigMasterWebCHT_status_search_text('name', $value_x->wp_name, $items_local);
                                
                                if($key_local !== false){
                                    $item_local = $items_local[$key_local];
                                    $ok_update = ((int)str_replace('.','', $value_x->wp_version)) != ((int)str_replace('.','', $item_local['version']));
                                    
                                    if($fin_fn){
                                        $pase_ok = false;
                                        $fin_fn = false;
                                    }else{
                                        $pase_ok = array_search($key_local, $key_update_list);
                                    }
                                    
                                    if($ok_update && $pase_ok === false){
                                        array_push($key_update_list, $key_local);
                                        
                                        $count_update++;
                                    }
                                }
                            }
                        }
                        if($count_update > 0){
                            BigMasterWebCHT_notification_helper('<b>BigMasterWeb:</b> <span> Tienes actualizaciones</span>', 'success');
                        }
                        update_option('BigMasterWebCHT_updates_pending', $count_update);
    
                        $ran_h  = random_int(8, 12);
                        $ran_m  = random_int(1, 59);
                        $h      = 3600;
                        $m      = 60;
                        $time_p = time()+(($h*$ran_h)+($m*$ran_m));
                        update_option('BigMasterWebCHT_cron_verifique_version', (string)$time_p);
                    }
                }
            }
        }

    }
    add_action('init', 'BigMasterWebCHT_cron_update_verifique');
}

