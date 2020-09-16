<?php

namespace sales\repositories\client;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;

class ClientsQuery
{
    public static function allByPhone(string $phone): array
    {
        return Client::find()->alias('cl_tbl')
            ->innerJoin(ClientPhone::tableName() . ' as clp', 'clp.client_id = cl_tbl.id AND clp.phone = :phone', [':phone' => $phone])
            ->orderBy(['cl_tbl.id' => SORT_ASC])
            ->all();
    }

    public static function allByEmail(string $email): array
    {
        return Client::find()->alias('cl_tbl')
            ->innerJoin(ClientEmail::tableName() . ' as cle', 'cle.client_id = cl_tbl.id AND clp.email = :email', [':email' => $email])
            ->orderBy(['cl_tbl.id' => SORT_ASC])
            ->all();
    }
}
