<?php

use yii\db\Migration;
use common\models\EmailTemplateType;

/**
 * Class m210204_153541_add_column_etp_params_json_to_email_template_type_tbl
 */
class m210204_153541_add_column_etp_params_json_to_email_template_type_tbl extends Migration
{


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email_template_type}}', 'etp_params_json', $this->json());
        $this->update('{{%email_template_type}}', ['etp_params_json' => EmailTemplateType::etpParamsInit()]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%email_template_type}}', 'etp_params_json');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
