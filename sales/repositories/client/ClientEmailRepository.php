<?php

namespace sales\repositories\client;

use common\models\ClientEmail;
use sales\repositories\NotFoundException;

class ClientEmailRepository
{
    public function get($id): ClientEmail
    {
        if ($email = ClientEmail::findOne($id)) {
            return $email;
        }
        throw new NotFoundException('Email is not found');
    }

    public function getByEmail($email): ClientEmail
    {
        if ($email = ClientEmail::find()->where(['email' => $email])->orderBy(['id' => SORT_ASC])->limit(1)->one()) {
            return $email;
        }
        throw new NotFoundException('Email is not found');
    }

    public function save(ClientEmail $email): int
    {
        if ($email->save(false)) {
            return $email->id;
        }
        throw new \RuntimeException('Saving error');
    }

    public function remove(ClientEmail $email): void
    {
        if (!$email->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}