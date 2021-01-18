<?php

namespace modules\fileStorage\src\entity\fileCase;

/**
* @see FileCase
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byCase(int $caseId): self
    {
        return $this->andWhere(['fc_case_id' => $caseId]);
    }

    public function byFile(int $fileId): self
    {
        return $this->andWhere(['fc_fs_id' => $fileId]);
    }

    /**
    * @return FileCase[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileCase|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
