<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\hotel\components;

use sales\helpers\app\AppHelper;
use sales\helpers\app\HttpStatusCodeHelper;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;
use sales\helpers\email\TextConvertingHelper;

/**
 * Class ApiHotelService
 * @package modules\hotel\components
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property Request $request
 */

class ApiHotelService extends Component
{
    public $url;
    public $username;
    public $password;
    public $options = [CURLOPT_ENCODING => 'gzip'];

    private $request;

    private const DESTINATION_LOCATIONS = 0;
    private const DESTINATION_CITY_ZONE = 1;
    private const DESTINATION_HOTEL = 2;

    private const DESTINATION_AVAILABLE_TYPE = [
    	self::DESTINATION_LOCATIONS,
		self::DESTINATION_CITY_ZONE,
		self::DESTINATION_HOTEL
	];

    public function init() : void
    {
        parent::init();
        $this->initRequest();
    }

    /**
     * @return bool
     */
    private function initRequest() : bool
    {
        $authStr = base64_encode($this->username . ':' . $this->password);

        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            $this->request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
            return true;
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiHotelService::initRequest:Throwable');
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
    protected function sendRequest(string $action = '', array $data = [], string $method = 'post', array $headers = [], array $options = []) : Response
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

    /**
     * @param string $checkIn
     * @param string $checkOut
     * @param string $destination
     * @param array $rooms
     * @param array $params
     * @return array
     */
    public function search(string $checkIn, string $checkOut, string $destination, array $rooms = [], array $params = []): array
    {
        $out = ['error' => false, 'data' => []];

        $data = $params;

        $data['checkIn'] = $checkIn;
        $data['checkOut'] = $checkOut;
        $data['destination'] = $destination;

        if ($rooms) {
            $data['rooms'] = $rooms;
        }

        try {
            $response = $this->sendRequest('booking/search', $data, 'post');
            // VarDumper::dump($response->data, 10, true); exit;

            if ($response->isOk && !isset($response->data['error'])) {
                if (isset($response->data['hotels'])) {
                    $out['data'] = $response->data;
                } else {
                    $out['error'] = 'Not found in response array data key [hotels]';
                }
            } elseif (isset($response->data['error'])) {
                $out['error'] = 'Not found in response array data key [hotels]';
                \Yii::error(VarDumper::dumpAsString($response->data['error'], 10), 'Component:ApiHotelService::search');
            } else {
                $out['error'] = 'Error ('.$response->statusCode.'): ' . $response->content;
                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:ApiHotelService::search');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'Component:ApiHotelService::throwable');
            $out['error'] = 'ApiHotelService error: ' . $throwable->getMessage();
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
				$out['error'] = 'Error ('.$response->statusCode.'): ' . $response->content;
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
     * @param array $params
     * @param string $urlAction
     * @param string $method
     * @return array [int status, string message, array data]
     */
    public function requestBookingHandler(string $urlAction, array $params, string $method = 'post')
    {
        $result = ['status' => 0, 'message' => '', 'data' => []];
        $actionCase = $urlAction . '_' . $method;

        /* TODO:
            1) add custom logging from add SL-988
            2) rewrote "set message", diff to human/system
         */

        try {
            $response = $this->sendRequest($urlAction, $params, $method);

            if ($response->isOk) {
                if ($this->checkDataResponse($actionCase, $response->data)) {
                    $result['data'] = $this->prepareDataResponse($actionCase, $response->data);
                    $result['status'] = 1;
                    $result['message'] = 'Process('. $actionCase .') completed successfully';
                } elseif (isset($response->data['error']) && !empty($response->data['error'])) {
                    $errorCode = (isset($response->data['error']['code'])) ? $response->data['error']['code'] : '';
                    $errorMessage = (isset($response->data['error']['message'])) ? $response->data['error']['message'] : '';
                    $result['message'] = 'TravelServices api responded with an error. ';
                    $result['message'] .= 'Status Code (' . $errorCode . '): ';
                    $result['message'] .= 'Message (' . $errorMessage . ')';
                } else {
                    $result['message'] = 'TravelServices api did not send expected data.
                        Status Code (' . $response->statusCode . '): 
                        Message (' . $this->getErrorMessageByCode($response->statusCode, $urlAction, $method) . ')';
                        $responseContent = TextConvertingHelper::htmlToText($response->content);
                }
            } else {
                $result['message'] = 'TravelServices api response error.
                    Status Code (' . $response->statusCode . '): 
                    Message (' . $this->getErrorMessageByCode($response->statusCode, $urlAction, $method) . ')';
                    $responseContent = TextConvertingHelper::htmlToText($response->content);
            }
        } catch (\Throwable $throwable) {
            $result['message'] = 'Hotel booking request api throwable error.
                    Status Code (' . $throwable->getCode() . '): 
                    Message (' . TextConvertingHelper::htmlToText($throwable->getMessage()) . ')';
            \Yii::error(AppHelper::throwableFormatter($throwable),self::class . ':' . __FUNCTION__ . ':' . $actionCase . ':Throwable');
        }

		if (!$result['status']) {
		    $additionalContent = (isset($responseContent) ? ': Response Content (' . $responseContent . ')' : '');
		    $log = [
		        'arguments' => func_get_args(),
		        'message' => $result['message'] . ' ' . $additionalContent,
            ];
		    \Yii::error(VarDumper::dumpAsString($log),self::class . ':' . __FUNCTION__ . ':' . $actionCase);
		}

		return $result;
	}

    /**
     * @param int $code
     * @param string $urlAction
     * @param string $method
     * @return string
     */
    private function getErrorMessageByCode(int $code, string $urlAction, string $method)
    {
        $url = $this->url . $urlAction;
        $errorMessage = HttpStatusCodeHelper::getName($code);
        switch ($code) {
            case '404':
                $info = 'Please recheck url(' . $url . ')';
                break;
            case '405':
                $info = 'Host(' . $url . ') does not work correctly with this method('. $method .')';
                break;
            case '401':
                $info = 'Please recheck in config(username and password)';
                break;
            default:
                $info = '';
        }
        return $errorMessage . '. ' . $info;
    }

    /**
     * @param string $actionCase
     * @param array $responseData
     * @return bool
     */
    private function checkDataResponse(string $actionCase, array $responseData)
    {
        $result = false;
        switch ($actionCase) {
            case 'booking/checkrate_post':
                if (isset($responseData['hotel']['rooms']) || isset($responseData['rateComments'])) {
                    $result = true;
                }
                break;
            case 'booking/book_post':
                if (isset($responseData['booking']['reference'])) {
                    $result = true;
                }
                break;
            case 'booking/book_delete':
                if (isset($responseData['booking'])) {
                    $result = true;
                }
                break;
        }
        return $result;
	}

    /**
     * @param string $actionCase
     * @param array $responseData
     * @return array
     */
    private function prepareDataResponse(string $actionCase, array $responseData)
    {
        $result = [];
        switch ($actionCase) {
            case 'booking/checkrate_post':
                $result = [
                    'source' => $responseData,
                    'rateComments' => (isset($responseData['rateComments'])) ?: '',
                    'rooms' => ((isset($responseData['hotel']['rooms']))) ? $this->prepareRooms($responseData['hotel']['rooms']) : [],
                ];
                break;
            case 'booking/book_post':
                $result = [
                    'source' => $responseData,
                    'reference' => $responseData['booking']['reference'],
                    'rooms' => ((isset($responseData['booking']['rooms']))) ? $this->prepareRooms($responseData['booking']['rooms']) : [],
                ];
                break;
            case 'booking/book_delete':
                $result = [];
                break;
        }
        return $result;
    }

    /**
     * @param array $responseRooms
     * @return array
     */
    private function prepareRooms(array $responseRooms)
    {
        $result = [];
        foreach ($responseRooms as $item) {
            foreach ($item['rates'] as $room) {
                $result[] = $room;
            }
        }
        return $result;
    }
}