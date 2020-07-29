<?php

wc_enqueue_js( "
    jQuery( function( $ ) {
	
	let shipping_envia_colvanes_fields = '#woocommerce_shipping_envia_colvanes_ec_user, #woocommerce_shipping_envia_colvanes_ec_password, #woocommerce_shipping_envia_colvanes_ec_code_account, #woocommerce_shipping_envia_colvanes_ec_payment_method';
	
	let shipping_envia_colvanes_sandbox_fields = '#woocommerce_shipping_envia_colvanes_ec_sandbox_user, #woocommerce_shipping_envia_colvanes_ec_sandbox_password, #woocommerce_shipping_envia_colvanes_ec_sandbox_code_account, #woocommerce_shipping_envia_colvanes_ec_sandbox_payment_method';

	$( '#woocommerce_shipping_envia_colvanes_ec_environment' ).change(function(){

		$( shipping_envia_colvanes_sandbox_fields + ',' + shipping_envia_colvanes_fields ).closest( 'tr' ).hide();

		if ( '0' === $( this ).val() ) {
			$( shipping_envia_colvanes_fields ).closest( 'tr' ).show();
			
		}else{
		   $( shipping_envia_colvanes_sandbox_fields ).closest( 'tr' ).show();
		}
	}).change();
});	
");

$docs_url = '<a target="_blank" href="https://shop.saulmoralespa.com/shipping-envia-colvanes-woo/">' . __( 'Ver documentación completa del plugin') . '</a>';
$docs = array(
    'docs'  => array(
        'title' => __( 'Documentación' ),
        'type'  => 'title',
        'description' => $docs_url
    )
);

return apply_filters(
    'envia_colvanes_settings',
    array_merge(
        $docs,
        array(
            'enabled' => array(
                'title' => __('Activar/Desactivar'),
                'type' => 'checkbox',
                'label' => __('Activar Envia Colvanes'),
                'default' => 'no'
            ),
            'title'        => array(
                'title'       => __( 'Título método de envío' ),
                'type'        => 'text',
                'description' => __( 'Esto controla el título que ve el usuario' ),
                'default'     => __( 'Envia Colvanes' ),
                'desc_tip'    => true
            ),
            'debug'        => array(
                'title'       => __( 'Depurador' ),
                'label'       => __( 'Habilitar el modo de desarrollador' ),
                'type'        => 'checkbox',
                'default'     => 'no',
                'description' => __( 'Habilite el modo de depuración para mostrar información de depuración en su carrito / pago' ),
                'desc_tip' => true
            ),
            'environment' => array(
                'title' => __('Entorno'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __('Entorno de pruebas o producción'),
                'desc_tip' => true,
                'default' => '1',
                'options'     => array(
                    '0'    => __( 'Producción'),
                    '1' => __( 'Pruebas')
                ),
            ),
            'city_sender' => array(
                'title' => __('Ciudad del remitente (donde se encuentra ubicada la tienda)'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __('Se recomienda selecionar ciudadades centrales'),
                'desc_tip' => true,
                'default' => true,
                'options'     => include dirname(__FILE__) . '/../cities.php'
            ),
            'user' => array(
                'title' => __( 'Usuario' ),
                'type'  => 'text',
                'description' => __( 'Usuario de la cuenta de Envia Colvanes' ),
                'desc_tip' => true
            ),
            'password' => array(
                'title' => __( 'Contraseña' ),
                'type'  => 'password',
                'description' => __( 'Contraseña de la cuenta de Envia Colvanes' ),
                'desc_tip' => true
            ),
            'code_account' => array(
                'title' => __( 'Código de cuenta' ),
                'type'  => 'number',
                'description' => __( 'El código de la cuenta de Envia Colvanes' ),
                'desc_tip' => true
            ),
            'payment_method' => array(
                'title' => __( 'Forma de Pago' ),
                'type'  => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __( 'Condición comercial que indica la forma de Pago.' ),
                'desc_tip' => true,
                'default' => 4,
                'options' => array(
                    6    => __( 'Contado'),
                    7   => __( 'Contraentrega'),
                    4    => __( 'Crédito')
                )
            ),
            'sandbox_user' => array(
                'title' => __( 'Usuario' ),
                'type'  => 'text',
                'description' => __( 'Usuario de la cuenta de Envia Colvanes' ),
                'desc_tip' => true
            ),
            'sandbox_password' => array(
                'title' => __( 'Contraseña' ),
                'type'  => 'password',
                'description' => __( 'Contraseña de la cuenta de Envia Colvanes' ),
                'desc_tip' => true
            ),
            'sandbox_code_account' => array(
                'title' => __( 'Código de cuenta' ),
                'type'  => 'number',
                'description' => __( 'El código de la cuenta de Envia Colvanes' ),
                'desc_tip' => true
            ),
            'sandbox_payment_method' => array(
                'title' => __( 'Forma de Pago' ),
                'type'  => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __( 'Condición comercial que indica la forma de Pago.' ),
                'desc_tip' => true,
                'default' => 4,
                'options' => array(
                    6    => __( 'Contado'),
                    7   => __( 'Contraentrega'),
                    4    => __( 'Crédito')
                )
            )
        )
    )
);