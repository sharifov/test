<?php

use yii\db\Migration;

/**
 * Class m200701_140605_convert_css_sale_data_to_json
 */
class m200701_140605_convert_css_sale_data_to_json extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return true;
        Yii::$app->db->createCommand(
            'UPDATE
                    case_sale
                SET 
                    css_sale_data = JSON_UNQUOTE(css_sale_data),
                    css_sale_data_updated = JSON_UNQUOTE(css_sale_data_updated)
                WHERE
                    JSON_TYPE(css_sale_data) = :json_type
            ',
            [
                ':json_type' => 'STRING',
            ]
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
        Yii::$app->db->createCommand(
            'UPDATE
                    case_sale
                SET 
                    css_sale_data = JSON_QUOTE(CAST(css_sale_data AS CHAR)),
                    css_sale_data_updated = JSON_QUOTE(CAST(css_sale_data_updated AS CHAR))
                WHERE
                    JSON_TYPE(css_sale_data) != :json_type
            ',
            [
                ':json_type' => 'STRING',
            ]
        )->execute();
    }
}
