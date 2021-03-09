<?php

namespace modules\rentCar\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class ApiRentCarService
 * @package modules\RentCar\components
 *
 * @property string $url
 * @property string $refid
 * @property string $api_key
 * @property string $format
 *
 * @property Request $request
 */
class ApiRentCarService extends Component
{
    public $url;
    public $refid;
    public $api_key;
    public $format = 'json2';
    public $options = [CURLOPT_ENCODING => 'gzip'];

    private $request;

    public function init(): void
    {
        parent::init();
        $this->initRequest();
    }

    /**
     * @return bool
     */
    private function initRequest(): bool
    {
        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            return true;
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiRentCarService::initRequest:Throwable');
        }

        return false;
    }

    /**
     * @param string $action
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $options
     * @return Response
     * @throws \yii\httpclient\Exception
     */
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []): Response
    {
        $dataParams['api_key'] = $this->api_key;
        $dataParams['refid'] = $this->refid;
        $dataParams['format'] = $this->format;

        $url = $this->url . $action . '?' . http_build_query($dataParams);

        /* @var $this->request Client */
        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data);

        $this->request->setOptions(ArrayHelper::merge($this->options, $options));
        if ($headers) {
            $this->request->addHeaders($headers);
        }
        return $this->request->send();
    }

    /**
     * @param string $method
     */
    protected function setFormatJson(string $method): void
    {
        $method = strtolower($method);
        if ($method === 'post' || $method === 'delete') {
            $this->request->setFormat(Client::FORMAT_JSON);
        }
    }

    public function search(
        string $pickUpCode,
        string $pickUpDate,
        ?string $pickUpTime = null,
        ?string $dropOffTime = null,
        ?string $dropOffCode = null,
        ?string $dropOffDate = null,
        ?string $sid = null
    ): array {
        $out = ['error' => false, 'data' => []];

        $data['pickup_date'] = $pickUpDate;
        $data['pickup_code'] = $pickUpCode;
        if ($pickUpTime) {
            $data['pickup_time'] = $pickUpTime;
        }
        if ($dropOffCode) {
            $data['dropoff_code'] = $dropOffCode;
        }
        if ($dropOffDate) {
            $data['dropoff_date'] = $dropOffDate;
        }
        if ($dropOffTime) {
            $data['dropoff_time'] = $dropOffTime;
        }
        if ($sid) {
            $data['sid'] = $sid;
        }

        try {
            $response = $this->sendRequest('getResultsV3', $data, 'get');

            if ($response->isOk) {
                if (isset($response->data['getCarResultsV3']['results']['result_list'])) {
                    $out['data'] = $response->data['getCarResultsV3']['results'];
                } else {
                    $out['error'] = 'In response not found getCarResultsV3.results.result_list';
                    \Yii::error([
                        'error' => $out['error'],
                        'data' => $response->data ?? [],
                    ], 'ApiRentCarService:search');
                }
            } else {
                $out['error'] = 'Error (' . $response->statusCode . '): ' . $response->content;
                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:ApiRentCarService:search');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'Component:ApiRentCarService:throwable');
            $out['error'] = 'ApiRentCarService error: ' . $throwable->getMessage();
        }

        return $out;
    }

    public function contractRequest(string $referenceId, ?string $sid = null): array
    {
        $out = ['error' => false, 'data' => []];

        $data['ppn_bundle'] = $referenceId;
        if ($sid) {
            $data['sid'] = $sid;
        }

        try {
            $response = $this->sendRequest('getContractRequest', $data, 'get');

            if ($response->isOk) {
                if (isset($response->data['getCarContractRequest']['results']['status'])) {
                    $out['data'] = $response->data['getCarContractRequest']['results'];
                } else {
                    $out['error'] = 'In response not found getCarContractRequest.results.status';
                    \Yii::error([
                        'error' => $out['error'],
                        'data' => $response->data ?? [],
                    ], 'ApiRentCarService:contractRequest');
                }
            } else {
                $out['error'] = 'Error (' . $response->statusCode . '): ' . $response->content;
                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ApiRentCarService:contractRequest:error');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiRentCarService:contractRequest:throwable');
            $out['error'] = 'ApiRentCarService error: ' . $throwable->getMessage();
        }
        return $out;
    }

    /**
     * @param string $referenceId
     * @param string $firstName
     * @param string $lastName
     * @param string $phone
     * @param string $email
     * @param string|null $sid
     * @return array
     */
    public function book(
        string $referenceId,
        string $firstName,
        string $lastName,
        string $phone,
        string $email,
        ?string $sid = null
    ) {
        $out = ['error' => false, 'data' => []];

        $data['ppn_bundle'] = $referenceId;
        $data['car_book_bundle'] = $referenceId;
        $data['driver_first_name'] = $firstName;
        $data['driver_last_name'] = $lastName;
        $data['cust_phone'] = $phone;
        $data['cust_email'] = $email;
        if ($sid) {
            $data['sid'] = $sid;
        }

        try {
            $response = $this->sendRequest('getBookRequest', $data, 'post');

            if ($response->isOk) {
                if ($results = ArrayHelper::getValue($response->data, 'getCarBookRequest.results')) {
                    $out['data'] = $results;
                } elseif ($error = ArrayHelper::getValue($response->data, 'getCarBookRequest.error.status')) {
                    $out['error'] = $error;
                } else {
                    $out['error'] = 'In response not found results|error';
                    \Yii::error([
                        'requestData' => $out['error'],
                        'responseData' => $response->data ?? [],
                    ], 'ApiRentCarService::book');
                }
            } else {
                $out['error'] = 'Error (' . $response->statusCode . '): ' . $response->content;
                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ApiRentCarService:book:error');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiRentCarService:book:throwable');
            $out['error'] = 'ApiRentCarService error: ' . $throwable->getMessage();
        }
        return $out;
    }

    /**
     * @param $bookingId
     * @param string $email
     * @param string|null $sid
     * @return array
     */
    public function cancel($bookingId, string $email, ?string $sid = null): array
    {
        $out = ['error' => false, 'data' => []];

        $data['booking_id'] = $bookingId;
        $data['email'] = $email;
        if ($sid) {
            $data['sid'] = $sid;
        }

        try {
            $response = $this->sendRequest('getCancelRequest', $data, 'get');
            if ($response->isOk) {
                if (isset($response->data['getCarCancel']['results']['status'])) {
                    $out['data'] = $response->data['getCarContractRequest']['results'];
                } else {
                    $out['error'] = 'In response not found getCancelRequest.results.status';
                    \Yii::error([
                        'error' => $out['error'],
                        'data' => $response->data ?? [],
                    ], 'ApiRentCarService:cancel');
                }
            } else {
                $out['error'] = 'Error (' . $response->statusCode . '): ' . $response->content;
                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'ApiRentCarService:cancel:error');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiRentCarService:cancel:throwable');
            $out['error'] = 'ApiRentCarService error: ' . $throwable->getMessage();
        }
        return $out;
    }
}
