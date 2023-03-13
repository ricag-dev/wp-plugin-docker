<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https:// dmonioazul.com
 * @since      1.0.0
 *
 * @package    Calcula_Finiquito
 * @subpackage Calcula_Finiquito/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Calcula_Finiquito
 * @subpackage Calcula_Finiquito/public
 * @author     Ric Ag <kharman123@gmail.com>
 */
class Calcula_Finiquito_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Calcula_Finiquito_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Calcula_Finiquito_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/calcula-finiquito-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Calcula_Finiquito_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Calcula_Finiquito_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_register_script('vuejs', plugin_dir_url(__FILE__) . 'js/vue.global.js',null,null, true);
        wp_enqueue_script('vuejs');
        wp_enqueue_script($this->plugin_name.'-formulatio', plugin_dir_url(__FILE__) . 'js/calcula-finiquito-formulario.js', array('vuejs'), $this->version, false);
        wp_enqueue_script($this->plugin_name.'-calculo', plugin_dir_url(__FILE__) . 'js/calcula-finiquito-calculo.js', array('vuejs'), $this->version, false);

    }

    public function update_siteurl()
    {
        global $wpdb;
        $sq = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options  WHERE option_name = 'home' OR option_name = 'siteurl' ");
        $ip = $_SERVER['SERVER_ADDR'];
        $old_ip = '';
        $pasa = false;
        foreach ($sq as $li) {
            $site = parse_url($li->option_value);
            if ($site['host'] != $ip) {
                $old_ip = $site['host'];
                $pasa = true;
            }
        }


        if ($pasa) {
            $wpdb->query("UPDATE {$wpdb->prefix}options SET option_value = replace(option_value, '$old_ip', '$ip') WHERE option_name = 'home' OR option_name = 'siteurl';");
            $wpdb->query("UPDATE {$wpdb->prefix}posts SET guid = replace(guid, '$old_ip','$ip');");
            $wpdb->query("UPDATE {$wpdb->prefix}posts SET post_content = replace(post_content, '$old_ip', '$ip');");
            $wpdb->query("UPDATE {$wpdb->prefix}postmeta SET meta_value = replace(meta_value,'$old_ip','$ip');");
            wp_redirect('/');
        }

    }

    public function register_shortcodes(){
        add_shortcode( 'finiquito_calculo', array( $this, 'shortcode_function') );
    }

    public function shortcode_function(){
        include_once( plugin_dir_path( __FILE__ ) . 'partials/calcula-finiquito-public-display.php' );
    }
}
