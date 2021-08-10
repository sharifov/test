<?php

namespace modules\product\src\entities\productQuoteChange;

use Yii;
use sales\entities\cases\Cases;
use modules\product\src\entities\productQuote\ProductQuote;
use common\models\Employee;

/**
 * This is the model class for table "product_quote_change".
 *
 * @property int $pqc_id
 * @property int $pqc_pq_id
 * @property int|null $pqc_case_id
 * @property int|null $pqc_decision_user
 * @property int|null $pqc_status_id
 * @property int|null $pqc_decision_type_id
 * @property string|null $pqc_created_dt
 * @property string|null $pqc_updated_dt
 * @property string|null $pqc_decision_dt
 *
 * @property Cases $pqcCase
 * @property Employees $pqcDecisionUser
 * @property ProductQuote $pqcPq
 */
class ProductQuoteChange extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_quote_change';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqc_pq_id'], 'required'],
            [['pqc_pq_id', 'pqc_case_id', 'pqc_decision_user', 'pqc_status_id', 'pqc_decision_type_id'], 'integer'],
            [['pqc_created_dt', 'pqc_updated_dt', 'pqc_decision_dt'], 'safe'],
            [['pqc_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['pqc_case_id' => 'cs_id']],
            [['pqc_decision_user'], 'exist', 'skipOnError' => true, 'targetClass' => Employees::class, 'targetAttribute' => ['pqc_decision_user' => 'id']],
            [['pqc_pq_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqc_pq_id' => 'pq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pqc_id' => 'ID',
            'pqc_pq_id' => 'Product Quote ID',
            'pqc_case_id' => 'Case ID',
            'pqc_decision_user' => 'Decision User',
            'pqc_status_id' => 'Status ID',
            'pqc_decision_type_id' => 'Decision Type ID',
            'pqc_created_dt' => 'Created Dt',
            'pqc_updated_dt' => 'Updated Dt',
            'pqc_decision_dt' => 'Decision Dt',
        ];
    }

    /**
     * Gets query for [[PqcCase]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPqcCase()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'pqc_case_id']);
    }

    /**
     * Gets query for [[PqcDecisionUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPqcDecisionUser()
    {
        return $this->hasOne(Employees::class, ['id' => 'pqc_decision_user']);
    }

    /**
     * Gets query for [[PqcPq]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPqcPq()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqc_pq_id']);
    }
}
