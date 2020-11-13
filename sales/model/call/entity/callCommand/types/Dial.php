<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Dial
 *
 * @property string $docUrl
 */
class Dial extends Model implements CommandTypeInterface
{
    public $sort;
    public $typeId = CallCommand::TYPE_DIAL;

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/dial';

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