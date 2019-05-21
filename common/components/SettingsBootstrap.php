<?php

namespace common\components;

use common\models\Setting;
use Yii;
use yii\base\BootstrapInterface;


/*
/* The base class that you use to retrieve the settings from the database
*/

class SettingsBootstrap implements BootstrapInterface {

    private $db;

    public function __construct() {
        $this->db = Yii::$app->db;
    }


    /**
     * @param \yii\base\Application $app
     * @throws \yii\db\Exception
     */
    public function bootstrap($app)
    {


        $tableSchema = Yii::$app->db->schema->getTableSchema('setting');


        if($tableSchema) {

            $cache = Yii::$app->cache;

            //$cache->delete('site_settings');

            $settingsArr = $cache->get('site_settings');

            if (!$settingsArr) {

                // Get settings from database
                $sql = $this->db->createCommand('SELECT s_key, s_type, s_value FROM {{%setting}}');
                $settings = $sql->queryAll();

                $settingsArr = [];
                if ($settings) {
                    foreach ($settings as $key => $setting) {
                        if (isset(Setting::TYPE_LIST[$setting['s_type']])) {
                            @settype($setting['s_value'], $setting['s_type']);
                        }
                        $value = $setting['s_value'];
                        $settingsArr[$setting['s_key']] = $value;
                    }
                }

                if ($settingsArr) {
                    $dependency = new \yii\caching\DbDependency(['sql' => 'SELECT MAX(s_updated_dt) FROM {{%setting}}']);
                    $cache->set('site_settings', $settingsArr, null, $dependency);
                    Yii::$app->params['settings'] = $settingsArr;
                }
            } else {
                // $settingsArr['cache'] = true;
                Yii::$app->params['settings'] = $settingsArr;
            }

        }

    }

}