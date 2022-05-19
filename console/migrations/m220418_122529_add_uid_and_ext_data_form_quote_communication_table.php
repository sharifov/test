<?php

use yii\db\Migration;
use common\models\QuoteCommunication;

/**
 * Class m220418_122529_add_uid_and_ext_data_form_quote_communication_table
 */
class m220418_122529_add_uid_and_ext_data_form_quote_communication_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quote_communication}}', '[[qc_uid]]', $this->string(32)->notNull());
        $this->addColumn('{{%quote_communication}}', '[[qc_ext_data]]', $this->text()->defaultValue(null));

        array_map(function (QuoteCommunication $quoteCommunication) {
            $quoteCommunication->qc_uid = \Yii::$app->security->generateRandomString(5);
            return $quoteCommunication->save();
        }, QuoteCommunication::find()->all());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quote_communication}}', '[[qc_uid]]');
        $this->dropColumn('{{%quote_communication}}', '[[qc_ext_data]]');
    }
}
