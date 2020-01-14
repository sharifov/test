<?php

namespace modules\flight\components\api;

use yii\base\Component;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Response;

/**
 * Class ApiService
 * @package modules\flight\components\api
 *
 * @property string $url
 * @property string $username
 * @property string $password
 */
class ApiService extends Component
{
	public $url;
	public $username;
	public $password;

	private $request;

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
			\Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiFlightService::initRequest:Throwable');
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

		if ($method === 'post') {
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
}