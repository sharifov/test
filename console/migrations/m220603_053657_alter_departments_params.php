<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220603_053657_alter_departments_params
 */
class m220603_053657_alter_departments_params extends Migration
{
    const NEW_PARAM_KEYS = ['createOnGeneralLineCall', 'createOnDirectCall', 'createOnRedirectCall'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $departmentParams = (new Query())
            ->select(['dep_id', 'dep_params'])
            ->from('{{%department}}')->all();

        try {
            foreach ($departmentParams as $departmentParam) {
                $params = @json_decode($departmentParam['dep_params'], true, 512, JSON_THROW_ON_ERROR);

                $newLeadParams = $newCaseParams = [];
                $newLeadParamValue = $params['object']['lead']['createOnCall'] ?? false;
                $newCaseParamValue = $params['object']['case']['createOnCall'] ?? false;

                foreach (self::NEW_PARAM_KEYS as $key) {
                    $newLeadParams[$key] = $newLeadParamValue;
                    $newCaseParams[$key] = $newCaseParamValue;
                }

                $params['object']['lead']['createOnCall'] = $newLeadParams;
                $params['object']['case']['createOnCall'] = $newCaseParams;

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

                foreach (self::NEW_PARAM_KEYS as $key) {
                    if (isset($params['object']['lead']['createOnCall'][$key])) {
                        unset($params['object']['lead']['createOnCall'][$key]);
                    }

                    if (isset($params['object']['case']['createOnCall'][$key])) {
                        unset($params['object']['case']['createOnCall'][$key]);
                    }
                }

                $params['object']['lead']['createOnCall'] = false;
                $params['object']['case']['createOnCall'] = false;

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
