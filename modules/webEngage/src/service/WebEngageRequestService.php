<?php

namespace modules\webEngage\src\service;

use common\components\Metrics;
use common\helpers\LogHelper;
use modules\webEngage\form\WebEngageEventForm;
use modules\webEngage\form\WebEngageUserForm;
use modules\webEngage\settings\WebEngageDictionary;
use modules\webEngage\settings\WebEngageSettings;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\conference\useCase\ReturnToHoldCall;
use Throwable;
use Yii;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class WebEngageRequestService
 *
 * @property array $options
 * @property WebEngageSettings $settings
 * @property Request $curlRequest
 * @property string $middleUrl
 */
class WebEngageRequestService
{
    public array $options = [CURLOPT_ENCODING => 'gzip'];

    private WebEngageSettings $settings;
    private ?Request $curlRequest = null;
    private string $middleUrl = 'v1/accounts';

    public function __construct()
    {
        $this->settings = new WebEngageSettings();
        if ($this->settings->isEnabled()) {
            $this->initRequest();
        } else {
            Yii::info('WebEngage is disabled', 'info\WebEngageRequestService:ServiceIsDisabled');
        }
    }

    private function initRequest(): void
    {
        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->curlRequest = $client->createRequest();
            $this->curlRequest->addHeaders(['Authorization' => 'Bearer ' . $this->settings->apiKey()]);
        } catch (Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'WebEngageRequestService::initRequest:Throwable'
            );
            throw new \RuntimeException('WebEngageRequestService not initialized');
        }
    }

    /**
     * @param array $data
     * @param string $apiEndpoint
     * @param string $method
     * @param array $headers
     * @param array $options
     * @param string|null $format
     * @return Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @throws \yii\httpclient\Exception
     */
    private function sendRequest(
        array $data,
        string $apiEndpoint,
        string $method = 'post',
        array $headers = [],
        array $options = [],
        ?string $format = Client::FORMAT_JSON
    ): ?Response {
        if ($this->curlRequest === null) {
            return null;
        }

        $this->curlRequest->setUrl($this->generateUrl($apiEndpoint))
            ->setMethod($method)
            ->setOptions($this->options)
            ->addData($data);

        if (!empty($headers)) {
            $this->curlRequest->addHeaders($headers);
        }
        if (!empty($options)) {
            $this->curlRequest->addOptions($options);
        }
        if (!empty($format)) {
            $this->curlRequest->setFormat($format);
        }

        $response = $this->curlRequest->send();

        $metrics = \Yii::$container->get(Metrics::class);
        if ($response->isOk) {
            $metrics->serviceCounter('web_engage', ['type' => 'success', 'action' => $apiEndpoint]);
        } else {
            $metrics->serviceCounter('web_engage', ['type' => 'error', 'action' => $apiEndpoint]);
        }
        unset($metrics);

        if ($this->settings->isDebugEnable()) {
            \Yii::info(
                [
                    'apiEndpoint' => $apiEndpoint,
                    'requestData' => LogHelper::hidePersonalData(
                        $data,
                        WebEngageDictionary::KEY_PERSONAL_DATA_LIST,
                        2
                    ),
                    'responseData' => $response->getData(),
                ],
                'info\WebEngageRequestService:sendRequest'
            );
        }

        return $response;
    }

    /**
     * @param WebEngageEventForm $webEngageEventForm
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @throws \yii\httpclient\Exception
     */
    public function addEvent(WebEngageEventForm $webEngageEventForm): ?array
    {
        $data = $webEngageEventForm->toArray();
        $response = $this->sendRequest($data, WebEngageDictionary::ENDPOINT_EVENTS);

        return ($response !== null) ? $response->getData() : null;
    }

    /**
     * @param WebEngageUserForm $webEngageUserForm
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @throws \yii\httpclient\Exception
     */
    public function addUser(WebEngageUserForm $webEngageUserForm): ?array
    {
        $data = $webEngageUserForm->toArray();
        $response = $this->sendRequest($data, WebEngageDictionary::ENDPOINT_USERS);

        return ($response !== null) ? $response->getData() : null;
    }

    private function generateUrl(string $apiEndpoint): string
    {
        return $this->settings->trackingEventsHost() . '/' . $this->middleUrl . '/' .
            $this->settings->licenseCode() . '/' . $apiEndpoint;
    }

    public function setMiddleUrl(string $middleUrl): WebEngageRequestService
    {
        $this->middleUrl = $middleUrl;
        return $this;
    }
}
