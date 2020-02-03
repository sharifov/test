<?php

namespace modules\hotel\src\helpers;

use sales\helpers\app\HttpStatusCodeHelper;
use sales\helpers\email\TextConvertingHelper;

class HotelApiMessageHelper
{
	public $title;
	public $urlMethod;
	public $arguments;
	public $message;
	public $code;
	public $additional;
	public $separator = ' <br />';
	public $forHuman = '';
	public $forLog = [];

	public $urlMethodMap = [
        'booking/book_post' => 'Booking Confirm',
        'booking/checkrate_post' => 'Booking CheckRate',
        'booking/book_delete' => 'Booking Cancel',
    ];

    /**
     * @return $this
     */
    public function prepareMessage()
    {
        $this->forHuman = 'Title (' . $this->title . '):' . $this->separator;
        if ($this->urlMethod && array_key_exists($this->urlMethod, $this->urlMethodMap)) {
            $this->forHuman .= 'Case (' . $this->urlMethodMap[$this->urlMethod] . '):' . $this->separator;
        }
        if (strlen($this->message)) {
            $this->forHuman .= 'Message (' . TextConvertingHelper::htmlToText($this->message) . ')' . $this->separator;
        }

        $this->forLog['title'] = 'Title (' . $this->title . ')';
        if ($this->arguments) {
            $this->forLog['arguments'] = $this->arguments;
        }
        if ($this->urlMethod && array_key_exists($this->urlMethod, $this->urlMethodMap)) {
            $this->forLog['case'] = 'Case (' . $this->urlMethodMap[$this->urlMethod] . ')';
        }
        if ($this->code) {
            $this->forLog['code'] = 'Status Code (' . $this->code . ')';
        }
        if ($this->message) {
            $this->forLog['message'] = 'Message (' . TextConvertingHelper::htmlToText($this->message) . ')';
        }
        if ($this->additional) {
            $this->forLog['additional'] = 'Additional content (' . TextConvertingHelper::htmlToText($this->additional) . ')';
        }
        return $this;
    }

    /**
     * @param int $code
     * @param string $url
     * @param string $method
     * @return string
     */
    public function getErrorMessageByCode(int $code, string $url, string $method)
    {
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
}