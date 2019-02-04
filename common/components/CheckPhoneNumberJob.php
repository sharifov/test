<?php

namespace common\components;


use yii\base\BaseObject;
use yii\helpers\VarDumper;
use Yii;
use common\components\CommunicationService;
use yii\helpers\ArrayHelper;
use common\models\ClientPhone;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;


class CheckPhoneNumberJob extends BaseObject implements \yii\queue\JobInterface
{

    public $client_id = 0;

    public $client_phone_id = 0;

    public $request_data = [];

    /**
     * @param \yii\queue\Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $debug = false;
        \Yii::info(VarDumper::dumpAsString(['client_id' =>  $this->client_id , 'client_phone_id' => $this->client_phone_id]), 'info\CheckPhoneNumberJob:execute:34');
        if($debug) {
            echo 'Start debug' . PHP_EOL;
        }

        $out = [
            'status' => 'check',
            'message' => 'init checking',
        ];
        try {

            if($debug) { echo 'client_id: ' . $this->client_id . ' client_phone_id: ' . $this->client_phone_id . PHP_EOL;  }

            if($this->client_id < 1 || $this->client_phone_id < 1) {
                throw new \Exception('Error CheckPhoneNumberJob data');
            }
            $clientPhone = ClientPhone::findOne(['client_id' => $this->client_id, 'id' => $this->client_phone_id ]);
            if(!$clientPhone || strlen($clientPhone->phone) < 9 ) {
                throw new \Exception('ClientPhone is empty or not found');
            }

            if($debug) { echo 'clientPhone->validate_dt: ' . $clientPhone->validate_dt . PHP_EOL;  }

            /*if($clientPhone->validate_dt !== null) {
                 return true;
            }*/

            $data = [];
            $url = Yii::$app->communication->url .'phone/index/?phone=' . $clientPhone->phone;
            if($debug) {
                echo $url . PHP_EOL;
            }
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $request = $client->createRequest();
            $request->setUrl($url)
                ->setMethod('GET')
                ->setOptions([
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_TIMEOUT => 30,
                ]);
                //->setData($data);

            $response = $request->send();
            //print_r($response->data); exit;

            if($debug) {
                echo "isOk: ". $response->isOk . PHP_EOL;
            }

            if ($response->isOk) {
                \Yii::info(VarDumper::dumpAsString(['client_phone' => $clientPhone->phone, 'response_data' => $response->data]), 'info\CheckPhoneNumberJob:execute:84');
                if(!isset($response->data['data']['response'])) {
                    throw new \Exception('Not found in response array data key [data][response]');
                }
            } else {
                throw new \Exception('ClientPhone error response from communication');
            }

            $is_error = false;
            if(isset($response->data['data']['response'], $response->data['data']['response']['numbers'])
                && count($response->data['data']['response']['numbers'])) {
                foreach ($response->data['data']['response']['numbers'] AS $phoneNumber => $phoneData) {
                    if(isset($phoneData['internationalNumber'], $phoneData['numberType'])) {
                        $phone = str_replace(' ', '', $phoneNumber);
                        if($phone === $clientPhone->phone) {
                            //$clientPhone->is_sms = ($phoneData['numberType'] == 'mobile') ? 1 : 0;
                            //$clientPhone->validate_dt = date("Y-m-d H:i:s");
                            if(!count($phoneData['errors'])) {
                                $clientPhone->updateAttributes([
                                    'is_sms' => ($phoneData['numberType'] == 'mobile') ? 1 : 0,
                                    'validate_dt' => date("Y-m-d H:i:s"),
                                ]);
                            }
                        }
                    }
                }
            } else {
                $is_error = true;
                throw new \Exception('Not found in response array data key [data][response]');
            }

            if(!$is_error) return true;



        } catch (\Throwable $e) {
            $out['status'] = 'error';
            $out['message'] = $e->getMessage() . ': ' . $e->getFile() . ' : ' . $e->getLine();
            \Yii::error(VarDumper::dumpAsString($out), 'CheckPhoneNumberJob:execute');
            if ($debug) {
                echo "Error: ";
                print_r($out);
            }
        }

        if ($debug) {
            echo "response: " . print_r($out, true) . PHP_EOL;
        }
        return true;
    }
}