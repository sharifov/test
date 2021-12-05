<?php

namespace sales\model\voip\phoneDevice;

use yii\base\Model;

/**
 * @property int $deviceId
 * @property int $level
 * @property string $message
 * @property int $timestamp
 * @property string|null $stacktrace
 * @property string $errorMessage
 * @property array|null $errorObject
 */
class PhoneDeviceLogForm extends Model
{
    public $deviceId;
    public $level;
    public $message;
    public $timestamp;
    public $stacktrace;

    private $errorMessage;
    private $errorObject;

    public function rules(): array
    {
        return [
            ['deviceId', 'safe'],

            ['level', 'required'],
            ['level', 'integer'],
            ['level', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
            ['level', 'in', 'range' => [PhoneDeviceLog::LEVEL_ERROR]],

            ['message', 'required'],
            ['message', 'string'],
            ['message', 'filterMessage', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['stacktrace', 'string'],

            ['timestamp', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function filterMessage(): void
    {
        try {
            $this->errorMessage = $this->message;
            $message = preg_replace('/\\\"/', "\"", $this->message);
            $error = json_decode($message, true, 512, JSON_THROW_ON_ERROR);
            if (!empty($error['message'])) {
                $this->errorMessage = $error['message'];
                $this->errorObject = $error;
            }
        } catch (\Throwable $e) {
//            \Yii::error([
//                'message' => $e->getMessage(),
//                'log' => $this->message,
//            ], 'PhoneDeviceLogForm:filterMessage');
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return string|null
     * @throws \JsonException
     */
    public function getErrorObject(): ?string
    {
        return $this->errorObject ? json_encode($this->errorObject, JSON_THROW_ON_ERROR, 512) : null;
    }

    public function formName(): string
    {
        return '';
    }
}
