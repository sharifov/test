<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m211217_130234_alter_projects_settings_add_new_parameter
 */
class m211217_130234_alter_projects_settings_add_new_parameter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = (new Query())->select(['id', 'p_params_json'])->from('{{%projects}}')->all();
        foreach ($projects as $project) {
            $params = @json_decode($project['p_params_json'], true, 512, JSON_THROW_ON_ERROR);
            if (isset($params['object']['lead'])) {
                $params['object']['lead']['default_cid_on_direct_call'] = '';
            }

            (new Query())->createCommand()->update('{{%projects}}', [
                'p_params_json' => $params
            ], [
                'id' => (int)$project['id']
            ])->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $projects = (new Query())->select(['id', 'p_params_json'])->from('{{%projects}}')->all();
        foreach ($projects as $project) {
            $params = @json_decode($project['p_params_json'], true, 512, JSON_THROW_ON_ERROR);
            if (isset($params['object']['lead']['default_cid_on_direct_call'])) {
                unset($params['object']['lead']['default_cid_on_direct_call']);
            }

            (new Query())->createCommand()->update('{{%projects}}', [
                'p_params_json' => $params
            ], [
                'id' => (int)$project['id']
            ])->execute();
        }
    }
}
