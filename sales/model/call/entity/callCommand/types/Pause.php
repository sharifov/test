<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Pause
 *
 * @property int $length
 * @property string $docUrl
 */
class Pause extends Model implements CommandTypeInterface
{
    public $length = 1;

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/pause';

    public $typeId = CallCommand::TYPE_PAUSE;
    public $sort;

    public function rules(): array
    {
        return [
            [['length'], 'required'],

            ['length', 'integer', 'min' => 1],
            ['length', 'filter', 'filter' => 'intval'],

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
