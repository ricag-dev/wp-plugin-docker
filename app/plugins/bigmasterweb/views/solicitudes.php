<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$BigMasterWebCHT_msj = null;

if(!empty($_REQUEST['form_htapps_bmw_solicitudes'])){
    if(wp_verify_nonce( $_REQUEST['form_htapps_bmw_solicitudes'], 'form_htapps_bmw_solicitudes' )){
        $msj_f = sanitize_text_field($_REQUEST['solicitud']);
        $BigMasterWebCHT_msj = BigMasterWebCHT_curl_request('solicitudes', array('solicitud' => $msj_f), 'post');
    }
}

?>
<div class="wrap htapps-content" >
    <h1 class="wp-heading-inline">
        Solicitar actualizaciones
    </h1>
    <?php if(!empty($BigMasterWebCHT_msj)){ ?>
        <?php foreach ($BigMasterWebCHT_msj->msj as $key => $value) { ?>
            <div class="notice notice-custom notice-<?php echo $BigMasterWebCHT_msj->status; ?> is-dismissible">
                <p>
                    <?php echo $value; ?>
                </p>
            </div>
    <?php } } ?>
    <p>
        Ayúdanos a saber que productos debemos actualizar
    </p>
    <form method="post" id="form_htapps_bmw_solicitudes" name="form_htapps_bmw_solicitudes"  >
        <?php wp_nonce_field( 'form_htapps_bmw_solicitudes', 'form_htapps_bmw_solicitudes' ); ?>
        <textarea name="solicitud" id="solicitud" minlength="3" required class="htApps-form-data"  ></textarea>
        <div class="submit">
            <button type="submit" class="button button-secundary">
                Enviar
            </button>
        </div>
    </form>
</div>