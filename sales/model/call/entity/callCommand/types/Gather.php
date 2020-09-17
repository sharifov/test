<?php

namespace sales\model\call\entity\callCommand\types;

use common\models\Language;
use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Gather
 *
 * @property int $type_id
 * @property string $action
 * @property string $finishOnKey
 * @property string $hints
 * @property string $input
 * @property string $language
 * @property string $method
 * @property int $numDigits
 * @property string $partialResultCallback
 * @property string $partialResultCallbackMethod
 * @property bool $profanityFilter
 * @property string $speechTimeout
 * @property int $timeout
 * @property string $speechModel
 * @property bool $enhanced
 * @property bool $actionOnEmptyResult
 * @property string $docUrl
 */
class Gather extends Model implements CommandTypeInterface
{
    public $type_id;
    public $action;
    public $finishOnKey = '#';
    public $hints;
    public $input = self::INPUT_DTMF;
    public $language;
    public $method = self::METHOD_POST;
    public $numDigits;
    public $partialResultCallback;
    public $partialResultCallbackMethod = self::METHOD_POST;
    public $profanityFilter = true;
    public $speechTimeout;
    public $timeout = 5;
    public $speechModel = self::SPEECH_MODEL_DEFAULT;
    public $enhanced = false;
    public $actionOnEmptyResult = false;

    public const TYPE_SWITCH = 'switch';
    public const TYPE_USER_NUMBER = 'user_number';

    public const TYPE_LIST = [
        self::TYPE_SWITCH => 'Switch',
        self::TYPE_USER_NUMBER => 'Additional user number',
    ];

    public const INPUT_DTMF = 'dtmf';
    public const INPUT_SPEECH = 'speech';
    public const INPUT_DTMF_SPEECH = 'dtmf speech';

    public const INPUT_LIST = [
        self::INPUT_DTMF => 'dtmf',
        self::INPUT_SPEECH => 'speech',
        self::INPUT_DTMF_SPEECH => 'dtmf speech',
    ];

    public const FINISH_ON_KEYS = [
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '#' => '#',
        '*' => '*',
        '' => 'empty string',
    ];

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    public const METHOD_LIST = [
        self::METHOD_GET => 'GET',
        self::METHOD_POST => 'POST',
    ];

    public const SPEECH_MODEL_DEFAULT = 'default';
    public const SPEECH_MODEL_PC = 'phone_call';
    public const SPEECH_MODEL_NC = 'numbers_and_commands';

    public const SPEECH_MODEL_LIST = [
        self::SPEECH_MODEL_DEFAULT => 'Default',
        self::SPEECH_MODEL_PC => 'Phone Call',
        self::SPEECH_MODEL_NC => 'Numbers and Commands',
    ];

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/gather';

    public $typeId = CallCommand::TYPE_GATHER;
    public $sort;

    public function rules(): array
    {
        return [
            [[
                'type_id', 'action', 'input', 'language', 'method',
                'speechTimeout', 'timeout', 'actionOnEmptyResult', 'enhanced',
            ], 'required'],

            ['type_id', 'string', 'max' => 50],
            ['type_id', 'in', 'range' => array_keys(self::TYPE_LIST)],

            ['action', 'string', 'max' => 255],

            ['finishOnKey', 'string', 'max' => 1, 'skipOnEmpty' => true],
            ['finishOnKey', 'in', 'range' => array_keys(self::FINISH_ON_KEYS)],

            ['hints', 'string', 'max' => 255],

            ['input', 'string', 'max' => 50],
            ['input', 'in', 'range' => array_keys(self::INPUT_LIST)],

            ['language', 'string', 'max' => 5],
            ['language', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true,
                'targetClass' => Language::class, 'targetAttribute' => ['language' => 'language_id']],

            ['method', 'string', 'max' => 20],
            ['method', 'in', 'range' => array_keys(self::METHOD_LIST)],

            ['numDigits', 'integer', 'min' => 1],

            ['partialResultCallback', 'string', 'max' => 255],

            ['partialResultCallbackMethod', 'string', 'max' => 20],
            ['partialResultCallbackMethod', 'in', 'range' => array_keys(self::METHOD_LIST)],

            ['profanityFilter', 'boolean'],
            ['profanityFilter', 'default', 'value' => true],

            ['speechTimeout', 'match' ,'pattern' => '/^[(1-9)|(auto)]+$/i',
                'message' => 'SpeechTimeout can contain only positive int or "auto"'],

            ['timeout', 'integer', 'min' => 1],

            ['speechModel', 'string', 'max' => 50],
            ['speechModel', 'in', 'range' => array_keys(self::SPEECH_MODEL_LIST)],

            ['enhanced', 'boolean'],

            ['actionOnEmptyResult', 'boolean'],

            [['typeId', 'sort'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getDocUrl(): string
    {
        return $this->docUrl;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function getSort()
{
    return $this->sort;
}

}