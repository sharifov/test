<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211223_132435_remove_deprecated_case_abac_objs
 */
class m211223_132435_remove_deprecated_case_abac_objs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'case/case/act/reprotection_quote/set_recommended',
            'case/case/act/reprotection_quote/original_quote_diff',
            'case/case/act/reprotection_quote/send_email',
            'case/case/act/product_quote/view_details',
            'case/case/act/product_quote/remove',
            'case/case/act/flight-voluntary-quote',
            'case/case/act/flight-reprotection-quote',
            'case/case/act/flight-reprotection-refund',
            'case/case/act/flight-reprotection-confirm',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211223_132435_remove_deprecated_case_abac_objs cannot be reverted.\n";

        return false;
    }
}
