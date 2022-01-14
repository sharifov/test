<?php

namespace src\model\call\entity\callCommand\types;

use src\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Refer
 *
 * @property string $action
 * @property string $method
 * @property string $sip
 * @property string $docUrl
 */
class Refer extends Model implements CommandTypeInterface
{
    public $action;
    public $method = self::METHOD_POST;
    public $sip;

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    public const METHOD_LIST = [
        self::METHOD_GET => 'GET',
        self::METHOD_POST => 'POST',
    ];

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/refer';

    public $typeId = CallCommand::TYPE_REFER;
    public $sort;

    public function rules(): array
    {
        return [
            [['action', 'method', 'sip'], 'required'],
            ['action', 'string', 'max' => 255],

            ['sip', 'string', 'max' => 255],

            ['method', 'string', 'max' => 20],
            ['method', 'in', 'range' => array_keys(self::METHOD_LIST)],

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

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }
}
