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
            ->innerJoin(ClientEmail::tableName() . ' as cle', 'cle.client_id = cl_tbl.id AND cle.email = :email', [':email' => $email])
            ->orderBy(['cl_tbl.id' => SORT_ASC])
            ->all();
    }

    public static function oneByEmailAndProject(string $email, int $projectId, ?int $typeId = null)
    {
        $query = Client::find()->alias('clients')->select(['clients.*'])
            ->innerJoin(
                ClientEmail::tableName() . ' AS emails',
                'emails.client_id = clients.id AND emails.email = :email',
                [':email' => $email]
            )
            ->where(['clients.cl_project_id' => $projectId])
            ->orderBy(['clients.id' => SORT_DESC])
            ->limit(1);
        if ($typeId) {
            $query->andWhere(['cl_type_id' => $typeId]);
        }
        return $query->one();
    }

    public static function oneByPhoneAndProject(string $phone, int $projectId, ?int $typeId = null)
    {
        $query = Client::find()->alias('clients')->select(['clients.*'])
            ->innerJoin(
                ClientPhone::tableName() . ' AS phones',
                'phones.client_id = clients.id AND phones.phone = :phone',
                [':phone' => $phone]
            )
            ->where(['clients.cl_project_id' => $projectId])
            ->orderBy(['clients.id' => SORT_DESC])
            ->limit(1);
        if ($typeId) {
            $query->andWhere(['cl_type_id' => $typeId]);
        }
        return $query->one();
    }

    public static function findParentByEmail(?string $email, int $projectId)
    {
        return Client::find()->alias('clients')->select(['clients.*'])
            ->innerJoin(
                ClientEmail::tableName() . ' AS emails',
                'emails.client_id = clients.id AND emails.email = :email',
                [':email' => $email]
            )
            ->where(['!=', 'clients.cl_project_id', $projectId])
            ->andWhere(['IS', 'parent_id', null])
            ->orderBy(['clients.id' => SORT_ASC])
            ->limit(1)
            ->one();
    }
}
