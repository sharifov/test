<?php

namespace modules\fileStorage\src\entity\fileProductQuote;

/**
* @see FileLead
*/
class FileProductQuoteScopes extends \yii\db\ActiveQuery
{
    public function byProductQuote(int $ProductQuoteId): self
    {
        return $this->andWhere(['fpq_pq_id' => $ProductQuoteId]);
    }

    public function byFile(int $fileId): self
    {
        return $this->andWhere(['fpq_fs_id' => $fileId]);
    }

    /**
    * @return FileProductQuote[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileProductQuote|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
