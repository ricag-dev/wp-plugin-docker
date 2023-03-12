<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$BigMasterWebCHT_msj = null;

if(!empty($_REQUEST['form_htapps_bmw_login'])){

    if(wp_verify_nonce( $_REQUEST['form_htapps_bmw_login'], 'form_htapps_bmw_login' )){
    
        $data = array(
            'name'  => sanitize_text_field($_REQUEST['name']),
            'pw'    => sanitize_text_field($_REQUEST['pw'])
        );
        
        $response = BigMasterWebCHT_curl_request('auth', $data, 'post');
        
        if(!empty($response->status)){
            if($response->status == 'success'){
                $BigMasterWebCHT_token = $response->data->token;
            }else{
                $BigMasterWebCHT_token = '';
                $BigMasterWebCHT_msj  = $response->msj; 
            }
            update_option('BigMasterWebCHT_user_token', $BigMasterWebCHT_token);
        }
    }
}

?>
<style>
#HTSliderRAP_content_3 .card-ht {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    border-radius: 2px;
    margin-bottom: 15px;
}
#HTSliderRAP_content_3 .ht-icon{
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}
#HTSliderRAP_content_3 .ht-icon.success{
    background-color: #32cd32;
    color: white;
    
}
#HTSliderRAP_content_3 .ht-icon.error{
    background-color: #5DADE2;
    color: white;

}
#HTSliderRAP_content_3 .card-ht .card-ht-content{
    padding: 5px 10px;
    margin-left: 90px;
}
#HTSliderRAP_content_3 .card-ht .card-ht-content .ht-title{
    display: block;
    font-size: 18px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: uppercase;
}
#HTSliderRAP_content_3 .card-ht .card-ht-content .ht-description{
    display: block;
    font-size: 70%;
    /* margin-top: 0.3rem; */
    text-transform: lowercase;
}
#HTSliderRAP_content_3 .card-ht .card-ht-content .ht-description-2{
    display: block;
    font-size: 14px;
    margin-top: 0.3rem;
}
#HTSliderRAP_content_3 .card-ht .card-ht-content .ht-title-2{
    display: block;
    font-size: 18px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: bold;
    text-transform: uppercase;
}
#HTSliderRAP_content_3  .emoji_img{
    width: 50%;
}
</style>
<div class="wrap htapps-content" >
    <h1 class="wp-heading-inline">
        Activa tu cuenta
    </h1> <br>
    <?php if(!empty($BigMasterWebCHT_msj)){ ?>
        <div class="notice notice-custom notice-error is-dismissible" >
            <p>
                <?php 
                    echo $BigMasterWebCHT_msj;
                ?>
            </p>
        </div>
    <?php } ?>
    
    <aside class="htApps-col-1">
        <form method="post" id="form_licenses_api" name="form_licenses_api" autocomplete="off" >
            <?php wp_nonce_field( 'form_htapps_bmw_login', 'form_htapps_bmw_login' ); ?>
            <br>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="name">Nombre de usuario o correo electrónico<span>*</span> </label>
                        </th>
                        <td>
                            <input type="text" name="name" id="name"  class="regular-text" minlength="3" required  />
                        </td>
                    </tr>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="pw">Contraseña<span>*</span> </label>
                        </th>
                        <td>
                            <input autocomplete="new-password" type="password" name="pw" id="pw"  class="regular-text" minlength="3" required  />
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="submit">
                <button type="submit" class="button button-secundary" >
                    Ingresar
                </button>
            </div>

        </form>
    </aside>
    <aside class="htApps-col-2" id="HTSliderRAP_content_3" >
        <h2>
            Su estado es:
        </h2>
        <?php $BigMasterWebCHT_token = esc_attr( get_option('BigMasterWebCHT_user_token')); ?>
        <div class="card-ht ">
            <span class="ht-icon <?php echo empty($BigMasterWebCHT_token) ? 'error':'success'; ?> " >
                <?php if(empty($BigMasterWebCHT_token)){ ?>
                    <img src="<?php echo plugins_url('../assets/img/pensando.svg', __FILE__); ?>" alt="load_ht" class="emoji_img" >
                <?php }else{ ?>
                    <img src="<?php echo plugins_url('../assets/img/ok.svg', __FILE__); ?>" alt="load_ht" class="emoji_img" >
                <?php } ?>
            </span>
            <div class="card-ht-content">
                <?php if(empty($BigMasterWebCHT_token)){ ?>
                    <p class="ht-title">
                        Puede que deba iniciar sesión 
                        <small class="ht-description">
                            Si presenta algún inconveniente puede comunicarlo  en <a href="https://bigmasterweb.com" target="_blank"  >BigMasterWeb</a>
                        </small>
                    </p>
                <?php }else{ ?>
                    <p class="ht-title">
                        Estás activo
                        <small class="ht-description">
                            <!-- Si presenta algún inconveniente intenta ingresar nuevamente. <br> -->
                            Para más productos e información visita <a href="https://bigmasterweb.com" target="_blank"  >BigMasterWeb</a>
                        </small>
                    </p>
                <?php } ?>
            </div>
        </div>
    </aside>
</div>