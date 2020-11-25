<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class CommandList
 *
 * @property int $sub_type
 * @property int $sub_sort
 * @property array $multipleFormData
 */
class CommandList extends Model
{
    public $sub_type;
    public $sub_sort;

    public $multipleFormData;

    public const ALLOWED_TYPE_LIST = [
        CallCommand::TYPE_SAY => CallCommand::TYPE_LIST[CallCommand::TYPE_SAY],
        CallCommand::TYPE_PLAY => CallCommand::TYPE_LIST[CallCommand::TYPE_PLAY],
        CallCommand::TYPE_PAUSE => CallCommand::TYPE_LIST[CallCommand::TYPE_PAUSE],
        CallCommand::TYPE_REJECT => CallCommand::TYPE_LIST[CallCommand::TYPE_REJECT],
        CallCommand::TYPE_REFER => CallCommand::TYPE_LIST[CallCommand::TYPE_REFER],
        CallCommand::TYPE_HANGUP => CallCommand::TYPE_LIST[CallCommand::TYPE_HANGUP],
        CallCommand::TYPE_GATHER => CallCommand::TYPE_LIST[CallCommand::TYPE_GATHER],
        CallCommand::TYPE_DIAL => CallCommand::TYPE_LIST[CallCommand::TYPE_DIAL],
        CallCommand::TYPE_REDIRECT => CallCommand::TYPE_LIST[CallCommand::TYPE_REDIRECT],
        CallCommand::TYPE_TWIML => CallCommand::TYPE_LIST[CallCommand::TYPE_TWIML],
        CallCommand::TYPE_FORWARD => CallCommand::TYPE_LIST[CallCommand::TYPE_FORWARD],
        CallCommand::TYPE_VOICE_MAIL => CallCommand::TYPE_LIST[CallCommand::TYPE_VOICE_MAIL],
    ];

    private int $typeId = CallCommand::TYPE_COMMAND_LIST;

    private $_formName;

    public function rules(): array
    {
        return [
            [['sub_type', 'sub_sort'], 'required'],
            [['sub_type', 'sub_sort'], 'integer'],

            ['sub_type', 'in', 'range' => array_keys(self::ALLOWED_TYPE_LIST)],

            ['multipleFormData', 'safe'],
        ];
    }

    public function formName(): string
    {
        return $this->_formName ?: parent::formName();
    }

    public function setFormName($name): void
    {
        $this->_formName = $name;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    /**
     * @param $multipleFormData
     */
    public function setMultipleFormData($multipleFormData): void
    {
        $this->multipleFormData = $multipleFormData;
    }
}
