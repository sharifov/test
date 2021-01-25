<?php

namespace modules\fileStorage\src\entity\fileStorage;

/**
 * @see FileStorage
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function success(): self
    {
        return $this->andWhere(['fs_status' => FileStorageStatus::UPLOADED]);
    }

    /**
     * @param null $db
     * @return FileStorage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return FileStorage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
