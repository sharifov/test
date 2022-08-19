<?php

namespace src\forms\clientChat;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChatForm\entity\ClientChatForm;
use src\model\clientChatForm\entity\ClientChatFormQuery;
use src\model\clientChatFormResponse\entity\ClientChatFormResponseQuery;
use yii\base\Model;

/**
 * Class BookingIdCreateForm
 *
 * @property string $bookingId
 */
class BookingIdCreateForm extends Model
{
    public $bookingId;

    private $clientChatId;

    private $clientChatFormId;

    /**
     * SubscribeForm constructor.
     * @param ClientChat $clientChat
     * @param ClientChatForm $clientChatForm
     * @param array $config
     */
    public function __construct(int $clientChatId, int $clientChatFormId, $config = [])
    {
        parent::__construct($config);
        $this->clientChatId = $clientChatId;
        $this->clientChatFormId = $clientChatFormId;
    }
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['bookingId', 'filter', 'filter' => 'trim'],
            [
                'bookingId', 'match',  'pattern' => '/^[a-zA-Z0-9]+$/',
                'message' => 'BookingId can only contain alphanumeric characters'
            ],
            ['bookingId', 'required'],
            ['bookingId', 'string', 'min' => 5, 'max' => 20],
            ['bookingId', 'validateUniqueBookingId'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'bookingId' => 'Booking id',
        ];
    }

    public function validateUniqueBookingId($attribute)
    {
        if (
            ClientChatFormResponseQuery::checkDuplicateValue(
                $this->clientChatFormId,
                $this->clientChatId,
                $this->bookingId
            )
        ) {
            $this->addError($attribute, 'Booking ID already exists');
        }
    }
}
