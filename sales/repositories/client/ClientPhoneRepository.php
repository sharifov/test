<?php

namespace sales\repositories\client;

use common\models\ClientPhone;
use sales\repositories\NotFoundException;

class ClientPhoneRepository
{
    /**
     * @param $id
     * @return ClientPhone
     */
    public function get($id): ClientPhone
    {
        if ($phone = ClientPhone::findOne($id)) {
            return $phone;
        }
        throw new NotFoundException('Phone is not found');
    }

    /**
     * @param $phone
     * @return ClientPhone
     */
    public function getByPhone($phone): ClientPhone
    {
        if ($clientPhone = ClientPhone::find()->where(['phone' => $phone])->orderBy(['id' => SORT_DESC])->limit(1)->one()) {
            return $clientPhone;
        }
        throw new NotFoundException('Phone is not found');
    }

    /**
     * @param $clientId
     * @param $phone
     * @return bool
     */
    public function exists($clientId, $phone): bool
    {
        return ClientPhone::find()->where(['client_id' => $clientId, 'phone' => $phone])->exists();
    }

    /**
     * @param ClientPhone $phone
     * @return int
     */
    public function save(ClientPhone $phone): int
    {
        if ($phone->save(false)) {
            return $phone->id;
        }
        throw new \RuntimeException('Saving error');
    }

    /**
     * @param ClientPhone $phone
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(ClientPhone $phone): void
    {
        if (!$phone->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}