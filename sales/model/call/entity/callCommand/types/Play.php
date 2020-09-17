<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Play
 *
 * @property int $loop
 * @property string $digits
 * @property string $url
 * @property string $docUrl
 */
class Play extends Model implements CommandTypeInterface
{
    public $url;
    public $loop = 1;
    public $digits;

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/play';

    public $typeId = CallCommand::TYPE_PLAY;
    public $sort;

    public function rules(): array
    {
        return [
            [['url', 'loop', 'digits'], 'required'],
            ['url', 'string', 'max' => 255],

            ['loop', 'integer', 'min' => 0],

            ['digits', 'string', 'max' => 20],
            ['digits', 'match' ,'pattern' => '/^[0-9w]+$/',
                'message' => 'SpeechTimeout can contain only int or "w"'],

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