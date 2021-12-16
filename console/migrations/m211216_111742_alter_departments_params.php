<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m211216_111742_alter_departments_params
 */
class m211216_111742_alter_departments_params extends Migration
{
    public function safeUp()
    {
        $departmentParams = (new Query())
            ->select(['dep_id', 'dep_params'])
            ->from('{{%department}}')->all();

        try {
            foreach ($departmentParams as $departmentParam) {
                $params = @json_decode($departmentParam['dep_params'], true, 512, JSON_THROW_ON_ERROR);
                if (isset($params['object']['lead']['createOnEmail'])) {
                    unset($params['object']['lead']['createOnEmail']);
                }
                if (isset($params['object']['case']['createOnEmail'])) {
                    unset($params['object']['case']['createOnEmail']);
                }

                $params['object']['lead']['createOnDepartmentEmail'] = [];
                $params['object']['case']['createOnDepartmentEmail'] = [];
                $params['object']['lead']['createOnPersonalEmail'] = [];
                $params['object']['case']['createOnPersonalEmail'] = [];

                (new Query())->createCommand()->update('{{%department}}', [
                    'dep_params' => json_encode($params)
                ], [
                    'dep_id' => (int)$departmentParam['dep_id']
                ])->execute();
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $departmentParams = (new Query())
            ->select(['dep_id', 'dep_params'])
            ->from('{{%department}}')->all();

        try {
            foreach ($departmentParams as $departmentParam) {
                $params = @json_decode($departmentParam['dep_params'], true, 512, JSON_THROW_ON_ERROR);
                if (isset($params['object']['lead']['createOnDepartmentEmail'])) {
                    unset($params['object']['lead']['createOnDepartmentEmail']);
                }
                if (isset($params['object']['case']['createOnDepartmentEmail'])) {
                    unset($params['object']['case']['createOnDepartmentEmail']);
                }
                if (isset($params['object']['lead']['createOnPersonalEmail'])) {
                    unset($params['object']['lead']['createOnPersonalEmail']);
                }
                if (isset($params['object']['case']['createOnPersonalEmail'])) {
                    unset($params['object']['case']['createOnPersonalEmail']);
                }

                $params['object']['lead']['createOnEmail'] = false;
                $params['object']['case']['createOnEmail'] = false;

                (new Query())->createCommand()->update('{{%department}}', [
                    'dep_params' => json_encode($params)
                ], [
                    'dep_id' => (int)$departmentParam['dep_id']
                ])->execute();
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }
}
