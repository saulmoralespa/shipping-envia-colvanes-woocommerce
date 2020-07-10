<?php


class WC_Shipping_Method_Envia_Colvanes_EC extends WC_Shipping_Method
{
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id                 = 'shipping_envia_colvanes_ec';
        $this->instance_id        = absint( $instance_id );
        $this->method_title       = __( 'Envia colvanes' );
        $this->method_description = __( 'Envia Colvanes empresa transportadora de Colombia' );
        $this->title              = __( 'Envia Colvanes' );

        $this->supports = array(
            'settings',
            'shipping-zones'
        );

        $this->init();

        $this->debug = $this->get_option( 'debug' );
        $this->isTest = (bool)$this->get_option( 'environment' );

        if ($this->isTest){
            $this->user = $this->get_option( 'sandbox_user' );
            $this->password = $this->get_option( 'sandbox_password' );
            $this->code_account = $this->get_option('sandbox_code_account');
            $this->payment_method = $this->get_option('sandbox_payment_method');
        }else{
            $this->user = $this->get_option( 'user' );
            $this->password = $this->get_option( 'password' );
            $this->code_account = $this->get_option('code_account');
            $this->payment_method = $this->get_option('payment_method');
        }

        $this->city_sender = $this->get_option('city_sender');
    }

    /**
     * Init the class settings
     */
    public function init()
    {
        // Load the settings API.
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings.
        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
        // Save settings in admin if you have any defined.
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Init the form fields for this shipping method
     */
    public function init_form_fields()
    {
        $this->form_fields = include(dirname(__FILE__) . '/admin/settings.php');
    }

    public function admin_options()
    {
        ?>
        <h3><?php echo $this->title; ?></h3>
        <p><?php echo $this->method_description; ?></p>
        <table class="form-table">
            <?php
            if (!empty($this->user) && !empty($this->password) && !empty($this->code_account))
                Shipping_Envia_Colvanes_EC::test_connection_guides();
            $this->generate_settings_html();
            ?>
        </table>
        <?php
    }

    public function is_available($package)
    {
        return parent::is_available($package) &&
            !empty($this->user) &&
            !empty($this->password) &&
            !empty($this->code_account);
    }

    /**
     * Calculate the rates for this shipping method.
     *
     * @access public
     * @param mixed $package Array containing the cart packages. To see more about these packages see the 'calculate_shipping' method in this file: woocommerce/includes/class-wc-cart.php.
     */
    public function calculate_shipping( $package = array() )
    {
        $country = $package['destination']['country'];

        if($country !== 'CO')
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        $data = $this->calculate_cost($package);

        if (empty($data))
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        shipping_envia_colvanes_ec()->log($data);

        $rate = array(
            'id'      => $this->id,
            'label'   => $this->title,
            'cost'    => $data->valor_flete,
            'package' => $package,
        );

        return $this->add_rate( $rate );

    }

    public function calculate_cost($package)
    {
        global $woocommerce;
        $country = $package['destination']['country'];
        $state_destination = $package['destination']['state'];
        $city_destination  = $package['destination']['city'];
        $items = $woocommerce->cart->get_cart();
        $cart_prods = [];
        $total_weight = 0;

        foreach ( $items as $item => $values ) {
            $_product_id = $values['data']->get_id();
            $_product = wc_get_product( $_product_id );

            if ( !$_product->get_weight() || !$_product->get_length()
                || !$_product->get_width() || !$_product->get_height() )
                break;

            $custom_price_product = get_post_meta($_product_id, '_shipping_custom_price_product_smp', true);
            $price = $custom_price_product ? $custom_price_product : $_product->get_price();
            $total_weight =+ $_product->get_weight() * $values['quantity'];

            $cart_prods[] = [
                'cantidad' => $values['quantity'],
                'largo' => $_product->get_length(),
                'ancho' => $_product->get_width(),
                'alto' => $_product->get_height(),
                'peso' => ceil($_product->get_weight()),
                'declarado' => $price
            ];
        }

        $name_state_destination = Shipping_Envia_Colvanes_EC::name_destination($country, $state_destination);

        if (empty($name_state_destination))
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        $address_destine = "$city_destination - $name_state_destination";

        if ($this->debug === 'yes')
            shipping_envia_colvanes_ec()->log("origin: $this->city_sender: $address_destine");

        $cities = include dirname(__FILE__) . '/cities.php';

        $destine = array_search($address_destine, $cities);
        if(!$destine)
            $destine = array_search($address_destine, Shipping_Envia_Colvanes_EC::clean_cities($cities));

        if(!$destine)
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        if (strlen($this->city_sender) === 4)
            $this->city_sender = '0' . $this->city_sender;

        if (strlen($destine) === 4)
            $destine = '0' . $destine;

        $params = array (
            'ciudad_origen' => $this->city_sender,
            'ciudad_destino' => $destine,
            'cod_formapago' => $this->payment_method,
            'cod_servicio' => $total_weight > 9 ? 3 : 1,
            'info_cubicacion' => $cart_prods,
            'mca_docinternacional' => 0,
            'con_cartaporte' => '0'
        );

        if ($this->debug === 'yes')
            shipping_envia_colvanes_ec()->log($params);

        $response = Shipping_Envia_Colvanes_EC::liquidation($params);

        return apply_filters( 'shipping_envia_colvanes_calculate_cost', $response, $package );

    }
}