<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Forward
 *
 * @property string $type
 * @property string $value
 */
class Forward extends Model implements CommandTypeInterface
{
    public $value;
    public $type;

    public const TYPE_PHONE = 'phone';
    public const TYPE_LINE = 'line';
    public const TYPE_USER = 'user';

    public const TYPE_LIST = [
        self::TYPE_PHONE => 'Phone',
        self::TYPE_LINE => 'Line',
        self::TYPE_USER => 'User',
    ];

    public $typeId = CallCommand::TYPE_FORWARD;
    public $sort;

    public function rules(): array
    {
        return [
            [['value', 'type'], 'required'],
            ['value', 'string', 'max' => 255],

            ['type', 'string', 'max' => 50],
            ['type', 'in', 'range' => array_keys(self::TYPE_LIST)],

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

    public function getSort()
    {
        return $this->sort;
    }
}
