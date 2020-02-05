<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\hotel\components;

use modules\hotel\src\entities\hotelQuoteServiceLog\events\HotelQuoteServiceLogCreateEvent;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use modules\hotel\src\helpers\HotelApiDataHelper;
use modules\hotel\src\helpers\HotelApiMessageHelper;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

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

    private $apiServiceName = 'TravelServices Api';

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
     * @param string $urlAction
     * @param array $params
     * @param int $hotelQuoteId
     * @param string $method
     * @return array
     */
    public function requestBookingHandler(string $urlAction, array $params, int $hotelQuoteId, string $method = 'post')
    {
        $result = ['status' => 0, 'message' => '', 'data' => []];
        $urlMethod = $urlAction . '_' . $method;
        $url = $this->url . $urlAction;
        $resultMessage = new HotelApiMessageHelper($urlMethod, func_get_args());

        $hotelQuoteServiceLog = new HotelQuoteServiceLog;
        $hotelQuoteServiceLog->hqsl_hotel_quote_id = $hotelQuoteId;
        $hotelQuoteServiceLog->hqsl_message = serialize($params);
        $hotelQuoteServiceLog->hqsl_status_id = HotelQuoteServiceLog::STATUS_SEND_REQUEST;
        $hotelQuoteServiceLog->hqsl_action_type_id = HotelQuoteServiceLog::URL_METHOD_ACTION_TYPE_MAP[$urlMethod];
        $hotelQuoteServiceLog->save();

        try {
            $response = $this->sendRequest($urlAction, $params, $method);

            if ($response->isOk) {
                if ((new HotelApiDataHelper)->checkDataResponse($urlMethod, $response->data)) {
                    $result['data'] = (new HotelApiDataHelper)->prepareDataResponse($urlMethod, $response->data);
                    $result['status'] = 1;

                    $resultMessage->title = 'Process completed successfully';
                    $resultMessage->message = 'Process('. $urlMethod .') completed successfully';

                    $serviceLogStatusId = HotelQuoteServiceLog::STATUS_SUCCESS;
                    $serviceLogMessage = serialize($response->data);

                } elseif (isset($response->data['error']) && !empty($response->data['error'])) {
                    $resultMessage->title = $this->apiServiceName . ' responded with an error.';
                    $resultMessage->message = (isset($response->data['error']['message'])) ? $response->data['error']['message'] : '';
                    $resultMessage->code = (isset($response->data['error']['code'])) ? $response->data['error']['code'] : '';

                    $serviceLogStatusId = HotelQuoteServiceLog::STATUS_FAIL;
                    $serviceLogMessage = serialize($response->data);

                } else {
                    $resultMessage->title = $this->apiServiceName . ' did not send expected data.';
                    $resultMessage->message = $resultMessage->getErrorMessageByCode($response->statusCode, $url, $method);
                    $resultMessage->code = $response->statusCode;
                    $resultMessage->additional = $response->content;

                    $serviceLogStatusId = HotelQuoteServiceLog::STATUS_FAIL;
                    $serviceLogMessage = serialize($response->data);
                }
            } else {
                $resultMessage->title = $this->apiServiceName . ' api response error.';
                $resultMessage->message = $resultMessage->getErrorMessageByCode($response->statusCode, $url, $method);
                $resultMessage->code = $response->statusCode;
                $resultMessage->additional = $response->content;

                $serviceLogStatusId = HotelQuoteServiceLog::STATUS_ERROR;
                $serviceLogMessage = serialize($response);
            }
        } catch (\Throwable $throwable) {
            $resultMessage->title = 'Hotel booking request api throwable error.';
            $resultMessage->message = $throwable->getMessage();
            $resultMessage->code = $throwable->getCode();

            $serviceLogStatusId = HotelQuoteServiceLog::STATUS_ERROR;
            $serviceLogMessage = serialize($throwable);
        }

        $message = $resultMessage->prepareMessage();
        $result['message'] = $message->forHuman;

		if (!$result['status']) {
		    \Yii::error(VarDumper::dumpAsString($message->forLog),self::class . ':' . __FUNCTION__ . ':' . $urlMethod);
		}

        $hotelQuoteServiceLog->hqsl_status_id = $serviceLogStatusId;
        $hotelQuoteServiceLog->hqsl_message = $serviceLogMessage;
        $hotelQuoteServiceLog->save();

		return $result;
	}
}