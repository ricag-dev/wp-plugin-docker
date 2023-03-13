<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https:// dmonioazul.com
 * @since      1.0.0
 *
 * @package    Calcula_Finiquito
 * @subpackage Calcula_Finiquito/admin/partials
 */

$update = false;
if($_POST){
    add_option( $this->plugin_name, $_POST );
    update_option( $this->plugin_name, $_POST );
    $update = true;
}
$options = get_option( $this->plugin_name, [] );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h1>Config Finiquito</h1>
    <div class="notice notice-success" <?php echo $update ? '' : 'hidden'?>>
        <p>Datos actualizados!</p>
    </div>
    <form method="post">
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
            <tr>
                <th scope="col">shortcode</th>
                <th scope="col">[finiquito_calculo]</th>
            </tr>
            </thead>

            <tbody id="the-list">
            <tr>
                <td>
                    <label for="nwhats">Número whatsapp</label>
                </td>
                <td><input id="nwhats" class="regular-text" name="whatsapp" required value="<?php echo $options['whatsapp']?>"></td>
            </tr>
            </tbody>

        </table>
        <?php submit_button();?>
    </form>
</div>

