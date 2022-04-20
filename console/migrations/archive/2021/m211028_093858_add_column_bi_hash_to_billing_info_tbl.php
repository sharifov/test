<?php

use common\models\BillingInfo;
use yii\db\Migration;

/**
 * Class m211028_093858_add_column_bi_hash_to_billing_info_tbl
 */
class m211028_093858_add_column_bi_hash_to_billing_info_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%billing_info}}', 'bi_hash', $this->string(32));
        $this->createIndex('IND-billing_info-bi_hash', '{{%billing_info}}', ['bi_hash']);

        $models = BillingInfo::find()->all();
        foreach ($models as $model) {
            $model->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-billing_info-bi_hash', '{{%billing_info}}');
        $this->dropColumn('{{%billing_info}}', 'bi_hash');
    }
}
