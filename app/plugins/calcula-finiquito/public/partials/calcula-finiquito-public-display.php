<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https:// dmonioazul.com
 * @since      1.0.0
 *
 * @package    Calcula_Finiquito
 * @subpackage Calcula_Finiquito/public/partials
 */

$options = get_option( $this->plugin_name, [] );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="app-vue">
    <template v-if="!input">
        <Formulario @input="input_calcula"/>
    </template>

    <template v-if="input">
        <Calculo v-model:input="input" v-model:whats="whats"/>
    </template>
</div>

<script>
    jq = new Promise((resolve)=>{
        jQuery( document ).ready(()=> resolve(true))
    })
    time = new Promise((resolve)=> setTimeout(()=> resolve(true),500))
    Promise.all([jq,time]).then(()=>{
        console.log('entro')
        const { createApp } = Vue
        app = createApp({
            components:{
                Formulario,
                Calculo
            },
            data() {
                return {
                    input: null,
                    whats: '<?php echo $options["whatsapp"]?>'
                }
            },
            methods: {
                input_calcula(values){
                    this.input = values
                }
            }
        })

        app.mount('#app-vue')
    })
</script>