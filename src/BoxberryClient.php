<?php

namespace Sb15;

class BoxberryClient
{
    private $token;
    private $jsonUrl = 'http://api.boxberry.de/json.php';

    const OPT_METHOD = 'method';
    const OPT_TOKEN = 'token';

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function listCities()
    {
        return $this->call('ListCities');
    }

    public function listPoints($cityCode = null)
    {
        $params = array();
        if ($cityCode) {
            $params['CityCode'] = $cityCode;
        }
        return $this->call('ListPoints', $params);
    }

    public function deliveryCosts($weightGrams, $pointCode, $orderSum = 0, $deliverySum = 0, $paySum = 0)
    {
        $params = array();
        $params['weight'] = $weightGrams;
        $params['target'] = $pointCode;
        $params['ordersum'] = $orderSum;
        $params['deliverysum'] = $deliverySum;
        $params['paysum'] = $paySum;
        $result = $this->call('DeliveryCosts', $params);
        return $result['price'];
    }

    protected function call($method, array $params = array(), $timeout = 10)
    {
        $curl = curl_init();
        $params[self::OPT_TOKEN] = $this->token;
        $params[self::OPT_METHOD] = $method;
        $params = http_build_query($params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->jsonUrl . "?" . $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));
        $data = curl_exec($curl);
        $data = json_decode($data, true);
        curl_close($curl);

        if (count($data) <= 0 or $data[0]['err']) {
            throw new Exception\CallException;
        }

        return $data;
    }
} 