<?php

use common\models\CreditCard;
use yii\db\Migration;

/**
 * Class m220318_145316_clear_private_card_data
 */
class m220318_145316_clear_private_card_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%credit_card}}', 'cc_number', $this->string(50));
        CreditCard::updateAll(['cc_cvv' => null, 'cc_number' => null, 'cc_display_number' => null]);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
