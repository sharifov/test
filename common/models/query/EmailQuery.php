<?php

namespace common\models\query;

use common\models\Email;

/**
 * This is the ActiveQuery class for [[Email]].
 *
 * @see Email
 */
class EmailQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Email[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Email|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byEmailToList(array $emails): EmailQuery
	{
		return $this->andWhere(['IN', 'e_email_to', $emails]);
	}

	public function byDateSend(string $date): EmailQuery
	{
		return $this->andWhere(['date_format(e_created_dt, "%Y-%m-%d")' => $date]);
	}
}
