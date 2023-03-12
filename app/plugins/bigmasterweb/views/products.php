<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// echo '<div style="padding: 3%;" >';
// echo '<p style="color: red;">';
// echo 'Proceso de actualización iniciado';
// echo '</p>';
// echo '<p style="color: red;">';
// echo 'Finalizando...';
// echo '</p>';
// echo '<p style="color: red;">';
// echo '<strongh>Refrescando...</strongh>';
// echo '</p>';
// echo '</div>';

global $wp_filesystem;

if (empty($wp_filesystem)) {
    require_once (ABSPATH . '/wp-admin/includes/file.php');
    WP_Filesystem();
}

$BigMasterWebCHT_msj = '';
$BigMasterWebCHT_data = [];
if(!empty($_REQUEST['form_htapps_bmw_update'])){
    if(wp_verify_nonce( $_REQUEST['form_htapps_bmw_update'], 'form_htapps_bmw_update' )){
        $product_id = sanitize_text_field($_REQUEST['product_id']);
        $order_id   = sanitize_text_field($_REQUEST['order_id']);
    
        $data_send = array(
            'product_id'  => $product_id,
            'order_id'    => $order_id 
        );
        $BigMasterWebCHT_response = BigMasterWebCHT_curl_request('updates', $data_send, 'post');
        
        if(!empty($BigMasterWebCHT_response)){
            
            if($BigMasterWebCHT_response->status === 'success'){
                $url        = $BigMasterWebCHT_response->data->url_download;
                $wp_folder  = $BigMasterWebCHT_response->data->wp_folder;
                $category   = $BigMasterWebCHT_response->data->category;

                if(!empty($wp_folder) && !empty($category) && !empty($url)){

                    echo '<div style="padding: 3%;" >';
                    echo '<p style="color: red;">';
                    echo 'Proceso de actualización iniciado';
                    echo '</p>';

                    $file_to    = BigMasterWebCHT_DIR.'/'.$wp_folder.'.zip';
                    $get_file   = base64_decode(base64_encode($wp_filesystem->get_contents($url)));
                    
                    if(!empty($get_file)){
                        $local_file_ok = $wp_filesystem->put_contents($file_to, $get_file);
            
                        switch (strtolower($category)) {
                            case 'temas':
                                $update_folder = WP_CONTENT_DIR . '/themes';
                                break;
                
                            case 'plugins':
                                $update_folder = WP_PLUGIN_DIR;
                                break;
                        }
                        
                        if(!empty($update_folder) && !empty($local_file_ok)){
                            
                            // $wp_filesystem->rmdir($update_folder.'/'.$wp_folder, true);
                            $zip_extract = unzip_file($file_to, $update_folder);
                            wp_delete_file($file_to);
                            echo '<p style="color: red;">';
                            echo 'Finalizando...';
                            echo '</p>';

                            $count_up = (int)esc_attr( get_option('BigMasterWebCHT_updates_pending'));
                            if($count_up > 0){
                                $count_minus = ($count_up-1);
                                update_option('BigMasterWebCHT_updates_pending', $count_minus);
                            }
                            $BigMasterWebCHT_data = BigMasterWebCHT_get_items_local();
                            
                            echo '<p style="color: red;">';
                            echo '<strongh>Refrescando...</strongh>';
                            echo '</p>';
                            echo '</div>';
                            echo("<meta http-equiv='refresh' content='1'>");
        
                        }else{
                            $BigMasterWebCHT_msj = (object)array(
                                'status'    => 'warning',
                                'msj'       => array('Algo no salio bien, comunique al administrador')
                            );
                            $BigMasterWebCHT_data = BigMasterWebCHT_get_items_local();
        
                        }
                    }else{
                        $BigMasterWebCHT_msj = (object)array(
                            'status'    => 'warning',
                            'msj'       => array('Intente de nuevo')
                        );
                        $BigMasterWebCHT_data = BigMasterWebCHT_get_items_local();
        
                    }
                }else{
                    $BigMasterWebCHT_msj = (object)array(
                        'status'    => 'warning',
                        'msj'       => array('Algo no salio bien, comunique al administrador')
                    );
                }
            }else{
                $BigMasterWebCHT_msj = $BigMasterWebCHT_response->msj;
                $BigMasterWebCHT_data = BigMasterWebCHT_get_items_local();
            }
        }else{
            $BigMasterWebCHT_data = BigMasterWebCHT_get_items_local();
        }

    }else{
        $BigMasterWebCHT_data = BigMasterWebCHT_get_items_local();
    }
}else{
    $BigMasterWebCHT_data = BigMasterWebCHT_get_items_local();
}  

// echo time();

// echo '<br>';

// echo get_option('BigMasterWebCHT_cron_verifique_version');

?>
<style>

    #HTSliderRAP_content_4 .content-pedido{
        width: 100%;
        max-width: 600px;
        border: 1px solid #e1e1e1;
        margin-bottom: 1rem;
        box-shadow: 13px 32px 36px -14px hsl(0deg 0% 70% / 20%);
    } 
    #HTSliderRAP_content_4 .btn-success-ht{
        background-color: #32cd32 !important;
        color: black !important;
    }
    #HTSliderRAP_content_4 .header_bg{
        width: 100%;
        height: 3rem;
        padding: 0.2rem;
        border-bottom: 1px solid #e1e1e1;
    } 

    #HTSliderRAP_content_4 .products{
        width: 100%;
        padding: 0.4rem;
    } 

    #HTSliderRAP_content_4 .item_product{
        /* width: 100%; */
        padding: 0.2rem;
        margin-bottom: 0.3rem;
        height: 2.4rem;
    } 

    #HTSliderRAP_content_4 table{
        width: 100%;
    } 

    #HTSliderRAP_content_4 table thead tr{
        width: 100%;
    } 
    #HTSliderRAP_content_4 .text-center{
        text-align: center;
    }
    #HTSliderRAP_content_4 .text-small{
        font-size: 0.7rem;
    }
    #HTSliderRAP_content_4 .load_gif{
        width: 5rem;
        margin: auto;
        display: block;
    }
    #HTSliderRAP_content_4 .hidden{
        display: none;
    }
</style>
<div ng-app="BMW_HT" >
    <div class="wrap htapps-content" id="HTSliderRAP_content_4" ng-controller="appCTRL" >
        <h1 class="wp-heading-inline">
            Mis productos
        </h1>
        <?php if(!empty($BigMasterWebCHT_msj)){ ?>
        <div class="notice notice-custom notice-error is-dismissible" >
            <p>
                <?php 
                    echo $BigMasterWebCHT_msj;
                ?>
            </p>
        </div>
        <?php } ?>
        
        <div class="notice notice-custom notice-{{value.status}} is-dismissible {{!errror ? 'hidden':''}}" ng-repeat="(key, value) in  errror" >
            <p>
                {{value.msj}} 
            </p>
        </div>
        
        <form method="post" id="form_htapps_bmw_update" class="text-center" >
            <?php wp_nonce_field( 'form_htapps_bmw_update', 'form_htapps_bmw_update' ); ?>
            <input type="hidden" name="order_id" >
            <input type="hidden" name="product_id" >
        </form>
        <img src="<?php echo plugins_url('../assets/img/load.gif', __FILE__); ?>" alt="load_ht" class="load_gif" >
        <div class="container-app hidden"> <!--   -->
            <div class="content-pedido" ng-repeat="(key, value) in order" >
                <div class="header_bg">
                    <h3>
                        {{value.order_id > 0 ? 'Pedido #'+value.order_id:'Membresía'}}
                    </h3>
                </div>
                <!-- products -->
                <section class="products">
                    <table>
                        <thead>
                            <tr class="item_product">
                                <td>
                                    <b>
                                        Nombre
                                    </b>
                                </td>
                                <td class="text-center" >
                                    <b>
                                        Tipo
                                    </b>
                                </td>
                                <td class="text-center" >
                                    <b>
                                        Versión
                                    </b>
                                </td>
                                <td class="text-center" >
                                    <b>
                                        Nueva versión
                                    </b>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item_product" ng-repeat="(key_2, value_2) in value.products" ng-if="fn_get_version(value_2)" >
                                <td>
                                    {{value_2.wp_name}}
                                </td>
                                <td class="text-center">
                                    {{value_2.category}}
                                </td>
                                <td class="text-center">
                                    {{fn_get_version(value_2)}}
                                </td>
                                <td class="text-center" >
                                    <div ng-if="fn_get_status(value_2)" >
                                        <button class="button button-primary text-small" type="button" ng-click="fn_set_update(value.order_id, value_2.product_id, $event)" ng-disabled="disabled" >
                                            Actualizar: {{value_2.wp_version}} 
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
</div>
<script>
    BigMasterWebCHT = angular.module('BMW_HT',[]);
    BigMasterWebCHT.run(function($http) {
    $http.defaults.headers.common['X-TOKEN'] = '<?php echo sanitize_text_field(esc_attr( get_option('BigMasterWebCHT_user_token'))); ?>';
    });
    
    BigMasterWebCHT.controller('appCTRL', function ($scope, $http){
        $scope.order = [];
        $scope.disabled = false;
        $scope.errror = [];
        pt = <?php echo json_encode($BigMasterWebCHT_data); ?>;

        $scope.fn_plugin_temas = function (data){
            for (const key in pt) {
                if (Object.hasOwnProperty.call(pt, key)) {
                    const element = pt[key];
                    if(element.name == data.wp_name){
                        return element;
                    }
                }
            }
            return false;
        }

        $scope.fn_get_version = function (data){
            version = $scope.fn_plugin_temas(data);
            return version.version;
        };

        $scope.fn_get_status = function (data){
            old = $scope.fn_plugin_temas(data);
            new_v = data.wp_version.replaceAll('.', '');
            old_v = old.version.replaceAll('.', '');
            
            return new_v != old_v;
        };

        $scope.fn_set_update = function (order_id, product_id, element){
            jQuery(element.currentTarget).toggleClass('button-primary btn-success-ht').text('...');
            jQuery('#form_htapps_bmw_update [name="order_id"]').val(order_id);
            jQuery('#form_htapps_bmw_update [name="product_id"]').val(product_id);
            $scope.disabled = true;
            jQuery('#form_htapps_bmw_update').submit();
            return false;
        };
        jQuery(document).ready(function(){
            setTimeout(function(){
                $http.post('https://bigmasterweb.com/wp-json/big-master-web-ht/products', {'client_items': JSON.stringify(pt)} )
                .then(function(resp){
                    if(resp.data.status == 'success'){
                        jQuery('#HTSliderRAP_content_4 .container-app').removeClass('hidden');
                        jQuery('#HTSliderRAP_content_4 img').remove();
                    }else{
                        
                    }
                    $scope.order = resp.data.data;
                }, function (error){
                    jQuery('#HTSliderRAP_content_4 img').remove();
                    $scope.errror.push({
                        'msj': 'Algo no esta bien, revise sus datos de autenticación',
                        'status': 'warning'
                    });
                });
            },2000);
        });
    });

</script>