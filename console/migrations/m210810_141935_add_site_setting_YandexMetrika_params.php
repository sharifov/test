<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m210810_141935_add_site_setting_YandexMetrika_params
 */
class m210810_141935_add_site_setting_YandexMetrika_params extends Migration
{
    public string $key = 'yandex_metrika';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => $this->key,
                's_name' => 'Yandex Metrika params',
                's_description' => '',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'enabled' => false,
                    'counterId' => 0,
                    'clickmap' =>  true,
                    'trackLinks' =>  true,
                    'accurateTrackBounce' =>  true,
                    'webvisor' =>  true,
                    'trackHash' =>  true,
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            $this->key,
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
