<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Reject
 *
 * @property string $reason
 * @property string $docUrl
 */
class Reject extends Model implements CommandTypeInterface
{
    public $reason = self::REASON_REJECTED;

    public const REASON_BUSY = 'busy';
    public const REASON_REJECTED = 'rejected';

    public const REASON_LIST = [
        self::REASON_BUSY => 'Busy',
        self::REASON_REJECTED => 'Rejected',
    ];

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/reject';

    public $typeId = CallCommand::TYPE_REJECT;
    public $sort;

    public function rules(): array
    {
        return [
            [['reason'], 'required'],

            ['reason', 'string', 'max' => 50],
            ['reason', 'in', 'range' => array_keys(self::REASON_LIST)],

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