<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class TwiML
 *
 * @property string $xml
 */
class TwiML extends Model implements CommandTypeInterface
{
    public $xml;

    public $typeId = CallCommand::TYPE_TWIML;
    public $sort;

    public function rules(): array
    {
        return [
            [['xml'], 'required'],

            ['xml', 'string', 'max' => 999],

            [['typeId', 'sort'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
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
