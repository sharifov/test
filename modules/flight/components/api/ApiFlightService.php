<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\flight\components\api;

use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class ApiFlightService
 * @package modules\flight\components\api
 *
 * @property string $url
 * @property string $username
 * @property string $password
 * @property Request $request
 */

class ApiFlightService extends ApiService
{
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
//        $out = ['error' => false, 'data' => []];
//
//        $data = $params;
//
//        $data['checkIn'] = $checkIn;
//        $data['checkOut'] = $checkOut;
//        $data['destination'] = $destination;
//
//        if ($rooms) {
//            $data['rooms'] = $rooms;
//        }
//
//        try {
//            $response = $this->sendRequest('booking/search', $data, 'POST');
//            // VarDumper::dump($response->data, 10, true); exit;
//
//            if ($response->isOk) {
//                if (isset($response->data['hotels'])) {
//                    $out['data'] = $response->data;
//                } else {
//                    $out['error'] = 'Not found in response array data key [hotels]';
//                }
//            } else {
//                $out['error'] = 'Error ('.$response->statusCode.'): ' . $response->content;
//                \Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Component:ApiHotelService::search');
//            }
//        } catch (\Throwable $throwable) {
//            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'Component:ApiHotelService::throwable');
//            $out['error'] = 'ApiHotelService error: ' . $throwable->getMessage();
//        }
//
//        return $out;
    }


}