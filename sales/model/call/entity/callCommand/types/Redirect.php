<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Redirect
 *
 * @property string $method
 * @property string $url
 * @property string $docUrl
 */
class Redirect extends Model implements CommandTypeInterface
{
    public $url;
    public $method = self::METHOD_POST;

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    public const METHOD_LIST = [
        self::METHOD_GET => 'GET',
        self::METHOD_POST => 'POST',
    ];

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/redirect';

    public $typeId = CallCommand::TYPE_REDIRECT;
    public $sort;

    public function rules(): array
    {
        return [
            [['url', 'method'], 'required'],
            ['url', 'string', 'max' => 255],

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