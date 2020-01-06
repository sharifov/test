<?php

namespace webapi\src\request;

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
        return $this->next->setUrl($this->createUrl($action))->send();
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
}
