<?php

use PHPUnit\Framework\TestCase;
use EnviaColvanes\Client;

class EnviaColvanesTest extends TestCase
{
    public $enviaColvanes;

    protected function setUp()
    {
        $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/../');
        $dotenv->load();

        $user = $_ENV['USERENVIA'];
        $password = $_ENV['PASSWORDENVIA'];
        $codAccount = $_ENV['CODCUENTAENVIA'];

        $this->enviaColvanes = new Client($user, $password, $codAccount);
        $this->enviaColvanes->sandboxMode(true);

    }

    public function testLiquidation()
    {
        $params = array (
            'ciudad_origen' => '1',
            'ciudad_destino' => '1',
            'cod_formapago' => 6,
            'cod_servicio' => 3,
            'info_cubicacion' =>
                array (
                    array (
                        'cantidad' => 1,
                        'largo' => 10.0,
                        'ancho' => 10.0,
                        'alto' => 10.0,
                        'peso' => 10,
                        'declarado' => 10000,
                    ),
                    array (
                        'cantidad' => 1,
                        'largo' => 20.0,
                        'ancho' => 20.0,
                        'alto' => 20.0,
                        'peso' => 20,
                        'declarado' => 20000,
                    )
                ),
            'mca_docinternacional' => 0,
            'con_cartaporte' => '0',
            'info_contenido' =>
                array (
                    'num_documentos' => '12345-67890',
                    'valorproducto' => '10000',
                ),
        );
        $response = $this->enviaColvanes->liquidation($params);
        $this->assertAttributeEmpty('respuesta', $response);
    }

    public function testGenerateGuide()
    {
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
                    'dice_contener' => 'zapatos',
                    'num_documentos' => '12345-67890',
                ),
            'numero_guia' => ''
        );
        $response = $this->enviaColvanes->generateGuide($params);
        $this->assertAttributeEmpty('respuesta', $response);
        $this->assertAttributeNotEmpty('urlguia', $response);
    }
}