<?php

namespace modules\fileStorage\src\entity\fileClient;

/**
* @see FileClient
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byClient(int $clientId): self
    {
        return $this->andWhere(['fcl_client_id' => $clientId]);
    }

    public function byFile(int $fileId): self
    {
        return $this->andWhere(['fcl_fs_id' => $fileId]);
    }

    /**
    * @return FileClient[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileClient|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
