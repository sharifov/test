<?php

namespace modules\user\userFeedback\forms;

use frontend\helpers\JsonHelper;
use modules\user\userFeedback\entity\UserFeedback;
use yii\base\Model;
use common\components\validators\CheckJsonValidator;

/**
 * Class UserFeedbackBugForm
 *
 * @property string $title
 * @property string|null $message
 * @property string|null $screenshot
 * @property string|null $data
 * @property string|null $pageUrl
 * @property string|null $date
 * @property string|null $time
 * @property string|null $type_id
 */
class UserFeedbackBugForm extends Model
{
    public $title;
    public $message;
    public $screenshot;
    public $data;
    public $pageUrl;
    public $date;
    public $time;
    public $type_id;

    private const VALID_MIME_TYPES = [
        'image/png',
        'image/jpeg'
    ];

    private const VALID_SCREENSHOT_SIZE_IN_MB = 1;

    private string $screenshotMimeType = '';

    private ?float $screenshotSize = null;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['message', 'screenshot', 'data'], 'string'],
            [['data'], 'safe'],
            [['data'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['title'], 'string', 'max' => 255],
            [['pageUrl'], 'string'],
            [['date', 'time'], 'safe'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['time'], 'datetime', 'format' => 'php:H:s'],
            [['type_id'], 'required'],
            [['type_id'], 'in', 'range' => array_keys(UserFeedback::TYPE_LIST)],
            [['screenshot'], 'validateScreenshot', 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Title',
            'type_id' => 'Type',
            'message' => 'Message',
            'screenshot' => 'Screenshot',
            'data' => 'Data',
        ];
    }

    public function beforeValidate()
    {
        if ($this->screenshot) {
            $fileinfo = new \finfo(FILEINFO_MIME_TYPE);
            $this->screenshotMimeType = $fileinfo->buffer(file_get_contents($this->screenshot));
            $this->screenshotSize = (int)(strlen(rtrim($this->screenshot, '=')) * 0.75);
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function validateScreenshot(): bool
    {
        if (!in_array($this->screenshotMimeType, self::VALID_MIME_TYPES, true)) {
            $this->addError('screenshot', 'Mime type not valid: ' . $this->screenshotMimeType);
            return false;
        }

        if (($this->screenshotSize / 1024 / 1024) > self::VALID_SCREENSHOT_SIZE_IN_MB) {
            $this->addError('screenshot', 'Image size is to big; Valid size is: ' . self::VALID_SCREENSHOT_SIZE_IN_MB . 'MB');
            return false;
        }
        return true;
    }

    public function getScreenshotMimeType(): string
    {
        return $this->screenshotMimeType;
    }

    public function getScreenshotSize(): ?float
    {
        return $this->screenshotSize;
    }

    public function getDecodedData(): ?array
    {
        return $this->data ? JsonHelper::decode($this->data) : null;
    }
}
