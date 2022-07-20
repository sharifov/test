<?php

namespace src\model\clientData\entity;

/**
* @see ClientData
*/
class ClientDataScopes extends \yii\db\ActiveQuery
{
    public function byKey(int $key): self
    {
        return $this->andWhere(['cd_key_id' => $key]);
    }

    public function byClientId(int $id): self
    {
        return $this->andWhere(['cd_client_id' => $id]);
    }
}
