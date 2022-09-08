<?php

namespace webapi\src\request;

use common\components\BackOffice;
use modules\featureFlag\FFlag;
use Yii;
use yii\helpers\VarDumper;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class RequestBo
 *
 * @property Request $next
 * @property string $url
 */
class RequestBo
{
    private const ACTION_CLICK_TO_BOOK = 'flight-request/booking';
    private const ACTION_PHONE_TO_BOOK = 'lead/book-quote';

    private const REDIRECT_STATUSES = [307];
    private const REDIRECT_ATTEMPTS = 1;

    private $next;
    private $url;

    public function __construct(Request $request, string $url)
    {
        $this->next = $request;
        $this->url = $url;
    }

    public function addData($data): void
    {
        $this->next->addData($data);
    }

    public function send(string $action, $data = null): Response
    {
        if ($data !== null) {
            $this->addData($data);
        }

        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BO_API_RBAC_AUTH)) {
            $sigUsername = Yii::$app->params['backOffice']['username'];
            $signature   = BackOffice::getSignatureBO($sigUsername, '', $data);
            $headers = [
                'sig-username' => $sigUsername,
                'signature'    => $signature
            ];
            $response = $this->next->setUrl($this->createUrl($action))->addHeaders($headers)->send();
        } else {
            $response = $this->next->setUrl($this->createUrl($action))->send();
        }

        $response = $this->checkRedirect($response);

        return $response;
    }

    public function sendClickToBook($data): Response
    {
        return $this->send(self::ACTION_CLICK_TO_BOOK, $data);
    }

    public function sendPhoneToBook($data): Response
    {
        return $this->send(self::ACTION_PHONE_TO_BOOK, $data);
    }

    private function createUrl($action): string
    {
        return $this->url . $action;
    }

    private function checkRedirect(Response $response): Response
    {
        $statusCode = $this->getResponseStatusCode($response);

        $attempts = 0;
        while (in_array($statusCode, self::REDIRECT_STATUSES, true) && $attempts < self::REDIRECT_ATTEMPTS) {
            $attempts++;
            if ($location = $response->getHeaders()->get('location')) {
                Yii::warning('Detect redirect to ' . VarDumper::dumpAsString($location), 'RequestBo:checkRedirect');
                $response = $this->next->setUrl($location)->send();
                $statusCode = $this->getResponseStatusCode($response);
            } else {
                Yii::warning('Detect redirect status code, but location not found on params: ' . VarDumper::dumpAsString($response->getHeaders()), 'RequestBo:checkRedirect');
            }
        }
        return $response;
    }

    private function getResponseStatusCode(Response $response): ?int
    {
        try {
            return (int)$response->getStatusCode();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
