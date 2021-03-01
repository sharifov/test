<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\attraction\components;

use modules\attraction\models\Attraction;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLogStatus;
use modules\hotel\src\helpers\HotelApiDataHelper;
use modules\hotel\src\helpers\HotelApiMessageHelper;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;
use Datetime;

/**
 * Class ApiAttractionService
 * @package modules\attraction\components
 *
 * @property string $url
 * @property string $apiKey
 * @property string $secret
 * @property Request $request
 */

class ApiAttractionService extends Component
{
    public $url;
    public $apiKey;
    public $secret;
    //public $options = [CURLOPT_ENCODING => 'gzip'];

    //private $request;

    /*private const DESTINATION_LOCATIONS = 0;
    private const DESTINATION_CITY_ZONE = 1;
    private const DESTINATION_HOTEL = 2;

    private const DESTINATION_AVAILABLE_TYPE = [
        self::DESTINATION_LOCATIONS,
        self::DESTINATION_CITY_ZONE,
        self::DESTINATION_HOTEL
    ];*/

    public function init(): void
    {
        parent::init();
        //$this->initRequest();
    }

    /**
     * @return bool
     */
    /*private function initRequest(): bool
    {
        $authStr = base64_encode($this->username . ':' . $this->password);

        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            $this->request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
            return true;
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiAttractionService::initRequest:Throwable');
        }

        return false;
    }*/

    private function execRequest($query)
    {
        $graphqlEndpoint = $this->url;
        $apiKey = $this->apiKey;
        $secret = $this->secret;

        $dt = new DateTime();
        $date = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';

        $string = $date . $apiKey . 'POST/graphql' . $query;
        $base64HashSignature = base64_encode(hash_hmac('sha1', $string, $secret, true));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $graphqlEndpoint);

        $headers = [];
        $headers[] = "x-api-key: $apiKey";
        $headers[] = "x-holibob-date: $date";
        $headers[] = "x-holibob-signature: $base64HashSignature";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    public function getProductById($productId)
    {
        $query = [
            'query' => 'query holibob ($productId: String!){
              product(id: $productId) {
                id
                code
                name                
                guidePriceCurrency
                guidePrice
                difficultyLevel
                supplierName
                __typename
                contentList {nodes{
                  name
                  type
                  description
                }}
              }
            }',
            'variables' => '{"productId":"' . $productId . '"}',
            'operationName' => 'holibob',
        ];

        $result = self::execRequest(@json_encode($query));
        $data = json_decode($result, true);
        return $data['data'] ?? [];
    }

    public function getProductList()
    {
        $query = [
            'query' => 'query holibob {
              productList(filter: {search: "London"}) {
                recordCount
                pageCount 
                nodes {
                  id
                  name  
                  availabilityType
                  guidePriceFormattedText
                  guidePrice
                  supplierName
                  abstract
                  previewImage {
                     url
                  }
                   place {
                     cityId
                     cityName                    
                     countryId
                     countryName
                   }
                }
              }
            }',
            'operationName' => 'holibob',
        ];

        $result = self::execRequest(@json_encode($query));
        $data = json_decode($result, true);
        return $data['data'];
    }

    public function checkApi()
    {
        //$query = '{"query":"query {welcome}"}';
        //$query = ['query' => 'query {welcome}'];
        //$result = self::execRequest(@json_encode($query));
        //var_dump($result); die();
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
        $url = $this->url . $action;

        /* @var $this->request Client */
        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data);

        $this->setFormatJson($method);
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

    public function getAttractionQuotes(Attraction $attraction): array
    {
        $out = ['error' => false, 'data' => []];

        $data = [
            'date_from' => $attraction->atn_date_from,
            'date_to' => $attraction->atn_date_to,
            'destination' => $attraction->atn_destination,
        ];

        $response = $this->sendRequest('product/attraction-search', $data);

        if ($response->isOk) {
            if (isset($response->data['data'])) {
                $out['data'] = $response->data['data']['activitySearch'];
            } else {
                $out['error'] = 'Not found in response array data key [data]';
            }
        } else {
            $out['error'] = $response->content;
            \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:CommunicationService::attractionQuotes');
        }

        return $out;
    }

    /**
     * @param string $term
     * @param string $lang
     * @param string $hc
     * @param string $zc
     * @param array $type
     * @return array
     */
    public function searchDestination(string $term, string $lang = '', string $hc = '', string $zc = '', array $type = null): array
    {
        $out = ['error' => false, 'data' => []];

        $data['term'] = $term;
        $data['lang'] = $lang;
        $data['hc'] = $hc;
        $data['zc'] = $zc;

        $data['t'] = implode(',', $type ?: self::getDestinationAvailableTypeList());

        try {
            $response = $this->sendRequest('content/destinations', $data, 'get');

            if ($response->isOk) {
                if (isset($response->data['destinations'])) {
                    $out['data'] = $response->data;
                } else {
                    $out['error'] = 'Not found destination';
                }
            } else {
                $out['error'] = 'Error (' . $response->statusCode . '): ' . $response->content;
                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:ApiHotelService:searchDestination:');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'Component:ApiHotelService:searchDestination:throwable');
            $out['error'] = 'ApiHotelService error: ' . $throwable->getMessage();
        }

        return $out;
    }

    /**
     * @return array
     */
    public static function getDestinationAvailableTypeList(): array
    {
        return self::DESTINATION_AVAILABLE_TYPE;
    }

    /**
     * @param string $urlAction
     * @param array $params
     * @param string $method
     * @return array
     */
    public function requestBookingHandler(string $urlAction, array $params, string $method = 'post'): array
    {
        $result = ['statusApi' => HotelQuoteServiceLogStatus::STATUS_ERROR, 'message' => '', 'data' => []];
        $urlMethod = $urlAction . '_' . $method;
        $url = $this->url . $urlAction;
        $resultMessage = new HotelApiMessageHelper($urlMethod, func_get_args());

        try {
            $response = $this->sendRequest($urlAction, $params, $method);

            if ($response->isOk) {
                if ((new HotelApiDataHelper())->checkDataResponse($urlMethod, $response->data)) {
                    $result['data'] = (new HotelApiDataHelper())->prepareDataResponse($urlMethod, $response->data);
                    $result['data']['logData'] = $response->data;
                    $result['statusApi'] = HotelQuoteServiceLogStatus::STATUS_SUCCESS;
                    $resultMessage->message = 'Process(' . $resultMessage->urlMethodMap[$urlMethod] . ') completed successfully';
                } elseif (isset($response->data['error']) && !empty($response->data['error'])) {
                    $result['statusApi'] = HotelQuoteServiceLogStatus::STATUS_FAIL_WITH_ERROR;
                    $result['data']['logData'] = $response->data;
                    $resultMessage->message = $response->data['error']['message'];
                } else {
                    $result['statusApi'] = HotelQuoteServiceLogStatus::STATUS_FAIL;
                    $result['data']['logData'] = $response;
                    $resultMessage->message = $resultMessage->getErrorMessageByCode($response->statusCode, $url, $method);
                }
            } else {
                $result['statusApi'] = HotelQuoteServiceLogStatus::STATUS_ERROR_RESPONSE;
                $result['data']['logData'] = $response;
                $resultMessage->message = $resultMessage->getErrorMessageByCode($response->statusCode, $url, $method);
            }
        } catch (\Throwable $throwable) {
            $resultMessage->title = HotelQuoteServiceLogStatus::getTitle(HotelQuoteServiceLogStatus::STATUS_ERROR);
            $resultMessage->message = $throwable->getMessage();
            $resultMessage->code = $throwable->getCode();

            $result['data']['logData'] = $resultMessage->prepareMessage()->forLog;
            \Yii::error(
                VarDumper::dumpAsString($result['data']['logData']),
                'ApiHotelService error:' . $resultMessage->urlMethodMap[$urlMethod]
            );
        }

        $resultMessage->title = HotelQuoteServiceLogStatus::getTitle($result['statusApi']);
        $result['message'] = $resultMessage->prepareMessage()->forHuman;

        return $result;
    }
}
