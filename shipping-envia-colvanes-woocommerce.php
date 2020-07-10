<?php
/**
 * Plugin Name: Shipping Envia Colvanes Woocommerce
 * Description: hipping Envia Colvanes Woocommerce is available for Colombia
 * Version: 1.0.0
 * Author: Saul Morales Pacheco
 * Author URI: https://saulmoralespa.com
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC tested up to: 4.3
 * WC requires at least: 4.0
 *
 * @package ShippingCoordinadora
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if(!defined('SHIPPING_ENVIA_COLVANES_EC_VERSION')){
    define('SHIPPING_ENVIA_COLVANES_EC_VERSION', '1.0.0');
}

add_action( 'plugins_loaded', 'shipping_envia_colvanes_ec_init');

function shipping_envia_colvanes_ec_init(){
    if ( !shipping_envia_colvanes_ec_requirements() )
        return;

    shipping_envia_colvanes_ec()->run_envia_colvanes();
}

function shipping_envia_colvanes_ec_notices( $notice ) {
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function shipping_envia_colvanes_ec_requirements(){

    if ( !in_array(
        'woocommerce/woocommerce.php',
        apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
        true
    ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    shipping_envia_colvanes_ec_notices( 'Shipping Envia Colvanes Woocommerce requiere que se encuentre instalado y activo el plugin: Woocommerce' );
                }
            );
        }
        return false;
    }

    if ( ! in_array(
        'departamentos-y-ciudades-de-colombia-para-woocommerce/departamentos-y-ciudades-de-colombia-para-woocommerce.php',
        apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
        true
    ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    $action = 'install-plugin';
                    $slug = 'departamentos-y-ciudades-de-colombia-para-woocommerce';
                    $plugin_install_url = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => $action,
                                'plugin' => $slug
                            ),
                            admin_url( 'update.php' )
                        ),
                        $action.'_'.$slug
                    );
                    $plugin = 'Shipping Envia Colvanes Woocommerce requiere que se encuentre instalado y activo el plugin: '  .
                        sprintf(
                            '%s',
                            "<a class='button button-primary' href='$plugin_install_url'>Departamentos y ciudades de Colombia para Woocommerce</a>" );

                    shipping_envia_colvanes_ec_notices( $plugin );
                }
            );
        }
        return false;
    }

    $woo_countries   = new WC_Countries();
    $default_country = $woo_countries->get_base_country();

    if ( ! in_array( $default_country, array( 'CO' ), true ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    $country = 'Shipping Envia Colvanes Woocommerce requiere que el país donde se encuentra ubicada la tienda sea Colombia '  .
                        sprintf(
                            '%s',
                            '<a href="' . admin_url() .
                            'admin.php?page=wc-settings&tab=general#s2id_woocommerce_currency">' .
                            'Click para establecer</a>' );
                    shipping_envia_colvanes_ec_notices( $country );
                }
            );
        }
        return false;
    }

    /*$wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
    $license = $wc_main_settings['servientrega_license'] ?? '';

    if(empty($license)){
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    $plugin_license = 'Shipping Servientrega Woocommerce requiere una licencia para poder generar guías entre otras funciones: '  .
                        sprintf(
                            '%s',
                            "<a class='button button-primary' target='_blank' href='https://shop.saulmoralespa.com/producto/plugin-shipping-servientrega-woocommerce/'>Obtener licencia</a>" );
                    shipping_servientrega_wc_ss_notices( $plugin_license );
                }
            );
        }
    }*/

    return true;
}

function shipping_envia_colvanes_ec(){
    static  $plugin;
    if(!isset($plugin)){
        require_once ("includes/class-shipping-envia-colvanes-plugin-ec.php");
        $plugin = new Shipping_Envia_Colvanes_Plugin_EC(__FILE__, SHIPPING_ENVIA_COLVANES_EC_VERSION);
    }

    return $plugin;
}