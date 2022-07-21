<?php

namespace src\model\cases\useCases\cases\addBookingId;

use src\entities\cases\Cases;
use yii\base\Model;

/**
 * Class AddBookingIdForm
 *
 * @property Cases $case
 * @property string|null $orderUid
 */
class AddBookingIdForm extends Model
{
    public ?string $orderUid;
    public int $userId;

    private Cases $case;

    public function __construct(
        Cases $case,
        int $userId,
        $config = []
    ) {
        parent::__construct($config);
        $this->case = $case;
        $this->orderUid = $case->cs_order_uid;
        $this->userId = $userId;
    }

    public function rules(): array
    {
        return [
            ['userId', 'integer'],

            ['orderUid', 'required'],
            ['orderUid', 'string', 'min' => '5', 'max' => 7],
            ['orderUid', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'orderUid' => 'Booking ID',
        ];
    }

    public function getCaseGid(): string
    {
        return $this->case->cs_gid;
    }

    public function getDto(): Command
    {
        return new Command(
            $this->case->cs_id,
            $this->orderUid,
            $this->userId
        );
    }
}
