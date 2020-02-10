<?php

namespace modules\hotel\src\helpers;

use sales\helpers\app\HttpStatusCodeHelper;
use sales\helpers\email\TextConvertingHelper;

/**
 * Class HotelApiMessageHelper
 * @package modules\hotel\src\helpers
 */
class HotelApiMessageHelper
{
	public $title;
	public $urlMethod;
	public $arguments;
	public $message;
	public $code;
	public $additional;
	public $separator = ' ';
	public $forHuman = '';
	public $forLog = [];

	public $urlMethodMap = [
        'booking/book_post' => 'Booking Confirm',
        'booking/checkrate_post' => 'Booking CheckRate',
        'booking/book_delete' => 'Booking Cancel',
    ];

    /**
     * HotelApiMessageHelper constructor.
     * @param $urlMethod
     * @param $arguments
     */
    public function __construct(string $urlMethod, array $arguments)
	{
		$this->urlMethod = $urlMethod;
		$this->arguments = $arguments;
	}

    /**
     * @return $this
     */
    public function prepareMessage(): self
    {
        $this->forHuman = 'Title (' . $this->title . '):' . $this->separator;
        if (array_key_exists($this->urlMethod, $this->urlMethodMap)) {
            $this->forHuman .= 'Case (' . $this->urlMethodMap[$this->urlMethod] . '):' . $this->separator;
        }
        if (strlen($this->message)) {
            $this->forHuman .= 'Message (' . TextConvertingHelper::htmlToText($this->message) . ')' . $this->separator;
        }

        $this->forLog['title'] = $this->title;
        if ($this->arguments) {
            $this->forLog['arguments'] = $this->arguments;
        }
        if ($this->urlMethod && array_key_exists($this->urlMethod, $this->urlMethodMap)) {
            $this->forLog['case'] = $this->urlMethodMap[$this->urlMethod];
        }
        if ($this->code) {
            $this->forLog['code'] = $this->code;
        }
        if ($this->message) {
            $this->forLog['message'] = TextConvertingHelper::htmlToText($this->message);
        }
        if ($this->additional) {
            $this->forLog['additional'] = TextConvertingHelper::htmlToText($this->additional);
        }
        return $this;
    }

    /**
     * @param int $code
     * @param string $url
     * @param string $method
     * @return string
     */
    public function getErrorMessageByCode(int $code, string $url, string $method): string
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