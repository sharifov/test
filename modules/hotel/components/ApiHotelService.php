<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\hotel\components;

use yii\base\Component;
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

        //$options = ['RETURNTRANSFER' => 1];

        $this->request->setMethod($method)
            ->setUrl($url)
            ->setData($data);

        if (strtolower($method) === 'post') {
			$this->request->setFormat(Client::FORMAT_JSON);
		}

        if ($headers) {
            $this->request->addHeaders($headers);
        }

        $this->request->setOptions([CURLOPT_ENCODING => 'gzip']);

        if ($options) {
            $this->request->setOptions($options);
        }

        return $this->request->send();
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

            if ($response->isOk) {
                if (isset($response->data['hotels'])) {
                    $out['data'] = $response->data;
                } else {
                    $out['error'] = 'Not found in response array data key [hotels]';
                }
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

}