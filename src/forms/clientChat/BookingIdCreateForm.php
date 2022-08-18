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

    /**
     * SubscribeForm constructor.
     * @param int $project_id
     * @param array $config
     */
    public function __construct(ClientChat $clientChat, $config = [])
    {
        parent::__construct($config);
        $this->clientChatId = $clientChat->cch_id;
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
            ['bookingId', 'validateUniqueBookingId'],
            ['bookingId', 'string', 'min' => 5, 'max' => 20],
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
        $clientChatForm = ClientChatFormQuery::getByKey(ClientChatForm::KEY_BOOKING_ID);

        if (is_null($clientChatForm)) {
            throw new \RuntimeException("client chat form with room id  " . ClientChatForm::KEY_BOOKING_ID . " not found");
        }

        if (
            ClientChatFormResponseQuery::checkDuplicateValue(
                $clientChatForm->ccf_id,
                $this->clientChatId,
                $this->bookingId
            )
        ) {
            $this->addError($attribute, 'Booking ID already exists');
        }
    }
}
