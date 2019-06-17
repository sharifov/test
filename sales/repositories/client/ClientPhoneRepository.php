<?php

namespace sales\repositories\client;

use common\models\ClientPhone;
use sales\repositories\NotFoundException;

class ClientPhoneRepository
{
    public function get($id): ClientPhone
    {
        if ($phone = ClientPhone::findOne($id)) {
            return $phone;
        }
        throw new NotFoundException('Phone is not found');
    }

    public function getByPhone($phone): ClientPhone
    {
        if ($phone = ClientPhone::find()->where(['phone' => $phone])->orderBy(['id' => SORT_ASC])->limit(1)->one()) {
            return $phone;
        }
        throw new NotFoundException('Phone is not found');
    }

    public function save(ClientPhone $phone): int
    {
        if ($phone->save(false)) {
            return $phone->id;
        }
        throw new \RuntimeException('Saving error');
    }

    public function remove(ClientPhone $phone): void
    {
        if (!$phone->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}