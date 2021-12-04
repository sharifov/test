<?php

namespace sales\model\voip\phoneDevice;

use yii\base\Model;

/**
 * @property int $level
 * @property string $message
 * @property int $timestamp
 * @property string|null $stacktrace
 */
class PhoneDeviceLogForm extends Model
{
    public const LEVEL_ERROR = 4;

    public $level;
    public $message;
    public $timestamp;
    public $stacktrace;

    public function rules(): array
    {
        return [
            ['level', 'required'],
            ['level', 'integer'],
            ['level', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
            ['level', 'in', 'range' => [self::LEVEL_ERROR]],

            ['message', 'required'],
            ['message', 'string'],
            ['message', 'filterMessage', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['stacktrace', 'string'],

            ['timestamp', 'datetime', 'format' => 'php:U'],
        ];
    }

    public function filterMessage(): void
    {
        try {
            $error = json_decode($this->message, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
        }
    }

    public function formName(): string
    {
        return '';
    }
}
