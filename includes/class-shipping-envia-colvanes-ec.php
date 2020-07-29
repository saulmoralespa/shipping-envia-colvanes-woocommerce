<?php

use EnviaColvanes\Client;

class Shipping_Envia_Colvanes_EC extends WC_Shipping_Method_Envia_Colvanes_EC
{

    public $enviaColvanes;

    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->enviaColvanes = new Client($this->user, $this->password, $this->code_account);
        $this->enviaColvanes->sandboxMode($this->isTest);
    }

    public static function test_connection_guides()
    {
        $instance = new self();
        try{
            $params = array (
                'ciudad_origen' => '1',
                'ciudad_destino' => '1',
                'cod_formapago' => 6,
                'cod_servicio' => 3,
                'num_unidades' => 1,
                'mpesoreal_k' => 10,
                'mpesovolumen_k' => 15,
                'valor_declarado' => 10000,
                'mca_nosabado' => 0,
                'mca_docinternacional' => 0,
                'cod_regional_cta' => 1,
                'cod_oficina_cta' => 1,
                'con_cartaporte' => '0',
                'info_origen' =>
                    array (
                        'nom_remitente' => 'JORGE GOMEZ',
                        'dir_remitente' => 'CALLE 13 84 60',
                        'tel_remitente' => '2020202',
                        'ced_remitente' => '79123456',
                    ),
                'info_destino' =>
                    array (
                        'nom_destinatario' => 'JUAN PEREZ',
                        'dir_destinatario' => 'CARRERA 15 # 15 15',
                        'tel_destinatario' => '3030303',
                    ),
                'info_contenido' =>
                    array (
                        'dice_contener' => '',
                        'num_documentos' => '12345-67890',
                    ),
                'numero_guia' => ''
            );
            $instance->enviaColvanes->generateGuide($params);
        }catch(\Exception $e){
            shipping_envia_colvanes_ec_notices("Shipping Envia Colvanes Woocommerce: " . $e->getMessage());
        }
    }

    public static function destination_code($state, $city)
    {
        $countries_obj        = new WC_Countries();
        $country_states_array = $countries_obj->get_states();
        $state_name           = $country_states_array['CO'][ $state ];
        $state_name           = self::short_name_location($state_name);
    }

    public static function short_name_location($name_location)
    {
        if ( 'Valle del Cauca' === $name_location )
            $name_location =  'Valle';
        return $name_location;
    }

    public static function clean_city($city)
    {
        return $city === 'Bogota D.C' ? 'Bogota' : $city;
    }

    public static function clean_string($string)
    {
        $not_permitted = array ("á","é","í","ó","ú","Á","É","Í",
            "Ó","Ú","ñ");
        $permitted = array ("a","e","i","o","u","A","E","I","O",
            "U","n");
        $text = str_replace($not_permitted, $permitted, $string);
        return $text;
    }

    public static function get_city( array $package = [])
    {
        $city_destination  = $package['destination']['city'];
        $city_destination = self::clean_string($city_destination);
        $city_destination = self::clean_city($city_destination);

        return $city_destination;
    }

    public static  function name_destination($country, $state_destination)
    {
        $countries_obj = new WC_Countries();
        $country_states_array = $countries_obj->get_states();

        $name_state_destination = '';

        if(!isset($country_states_array[$country][$state_destination]))
            return $name_state_destination;

        $name_state_destination = $country_states_array[$country][$state_destination];
        $name_state_destination = self::clean_string($name_state_destination);
        return self::short_name_location($name_state_destination);
    }

    public static function clean_cities($cities)
    {
        foreach ($cities as $key => $value){
            $cities[$key] = self::clean_string($value);
        }

        return $cities;
    }

    public static function liquidation(array $params)
    {
        $res = [];

        try{
            $instance = new self();
            $res = $instance->enviaColvanes->liquidation($params);
            return $res;
        }catch (\Exception $exception){
            shipping_envia_colvanes_ec()->log($exception->getMessage());
        }

        return $res;
    }

    public static function generate_guide_dispath($order_id, $old_status, $new_status, WC_Order $order)
    {

    }
}