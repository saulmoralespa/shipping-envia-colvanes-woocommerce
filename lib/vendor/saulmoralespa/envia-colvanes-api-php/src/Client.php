<?php


namespace EnviaColvanes;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;


class Client
{
    const SANDBOX_URL_LIQUIDATION = 'http://200.69.100.66/ServicioLiquidacionRESTpruebas/Service1.svc/Liquidacion/';
    const URL_LIQUIDATION = 'http://200.69.100.66/ServicioLiquidacionREST/Service1.svc/Liquidacion/';

    const SANDBOX_URL_GUIDES = 'http://200.69.100.66/ServicioLiquidacionRESTpruebas/Service1.svc/Generacion/';
    const URL_GUIDES = 'http://200.69.100.66/ServicioLiquidacionREST/Service1.svc/Generacion/';

    private $user;
    private $password;
    private $codeAccount;
    protected static $_sandbox = false;

    public function __construct($user, $password, $codeAccount)
    {
        $this->user = $user;
        $this->password = $password;
        $this->codeAccount = $codeAccount;
    }

    public function sandboxMode($status = false)
    {
        self::$_sandbox = $status;
    }

    protected static function getUrlLiquidation()
    {
        if(self::$_sandbox)
            return self::SANDBOX_URL_LIQUIDATION;
        return self::URL_LIQUIDATION;
    }

    protected static function getUrlGuides()
    {
        if(self::$_sandbox)
            return self::SANDBOX_URL_GUIDES;
        return self::URL_GUIDES;
    }

    public function client()
    {
        return new GuzzleClient();
    }

    public function liquidation(array $params)
    {
        try{
            $response = $this->client()->post(self::getUrlLiquidation(), [
                "headers" => [
                    "Authorization" => ["Basic " . $this->encodeCredentials()],
                    "Accept" => "application/json"
                ],
                "json" => $params
            ]);
            return self::responseJson($response);
        }catch(RequestException $exception){
            $response = $exception->getResponse();
            if (isset($response))
                $response = self::responseJson($response);
            $exceptionMessage = $response->respuesta ?? $exception->getMessage();
            throw new \Exception($exceptionMessage);
        }
    }

    public function generateGuide(array $params)
    {
        try{
            $params = array_merge($params,  [
                'cod_cuenta' => $this->codeAccount,
            ]);
            $response = $this->client()->request('POST', self::getUrlGuides(), [
                "headers" => [
                    "Authorization" => "Basic " . "{$this->encodeCredentials()}",
                    "Accept" => "application/json"
                ],
                "json" => $params
            ]);
            return self::responseJson($response);
        }catch(RequestException $exception){
            $response = $exception->getResponse();
            if (isset($response))
                $response = self::responseJson($response);
            $exceptionMessage = $response->respuesta ?? $exception->getMessage();
            throw new \Exception($exceptionMessage);
        }
    }

    public function encodeCredentials()
    {
        $access = "$this->user:$this->password";
        return base64_encode($access);
    }

    public static function responseJson($response)
    {
        return \GuzzleHttp\json_decode(
            $response->getBody()->getContents()
        );
    }
}