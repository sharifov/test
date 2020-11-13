<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Hangup
 *
 * @property string $docUrl
 */
class Hangup extends Model implements CommandTypeInterface
{
    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/hangup';

    public $typeId = CallCommand::TYPE_HANGUP;
    public $sort;

    public function rules(): array
    {
        return [
            [['typeId'], 'required'],

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