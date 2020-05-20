<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sale_credit_card".
 *
 * @property int $scc_sale_id
 * @property int $scc_cc_id
 * @property string|null $scc_created_dt
 * @property int|null $scc_created_user_id
 *
 * @property CreditCard $sccCc
 * @property Employee $sccCreatedUser
 */
class SaleCreditCard extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'sale_credit_card';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['scc_sale_id', 'scc_cc_id'], 'required'],
            [['scc_sale_id', 'scc_cc_id', 'scc_created_user_id'], 'integer'],
            [['scc_created_dt'], 'safe'],
            [['scc_sale_id', 'scc_cc_id'], 'unique', 'targetAttribute' => ['scc_sale_id', 'scc_cc_id']],
            [['scc_cc_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreditCard::class, 'targetAttribute' => ['scc_cc_id' => 'cc_id']],
            [['scc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['scc_created_user_id' => 'id']],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'scc_sale_id' => 'Sale ID',
            'scc_cc_id' => 'Cc ID',
            'scc_created_dt' => 'Created Dt',
            'scc_created_user_id' => 'Created User ID',
        ];
    }

    /**
     * Gets query for [[SccCc]].
     *
     * @return ActiveQuery
     */
    public function getSccCc(): ActiveQuery
    {
        return $this->hasOne(CreditCard::class, ['cc_id' => 'scc_cc_id']);
    }

    /**
     * Gets query for [[SccCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getSccCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'scc_created_user_id']);
    }
}
