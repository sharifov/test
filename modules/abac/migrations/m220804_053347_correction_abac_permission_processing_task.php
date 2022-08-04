<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use src\helpers\app\AppHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220804_053347_correction_abac_permission_processing_task
 */
class m220804_053347_correction_abac_permission_processing_task extends Migration
{
    private const AP_OBJECT = 'lead/lead/task_list/processing_task';
    private const AP_ACTION = '(access)';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $abacPolicies = AbacPolicy::find()
                ->where(['ap_object' => self::AP_OBJECT])
                ->andWhere(['ap_action' => self::AP_ACTION])
                ->andWhere(['ap_enabled' => 1])
                ->orderBy(['ap_id' => SORT_ASC])
                ->all()
            ;

            if (count($abacPolicies) > 1) {
                array_pop($abacPolicies);
                foreach ($abacPolicies as $abacPolicy) {
                    AbacPolicy::updateAll(
                        ['ap_enabled' => 0],
                        ['ap_id' => $abacPolicy->ap_id]
                    );
                }
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220804_053347_correction_abac_permission_processing_task:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220804_053347_correction_abac_permission_processing_task cannot be reverted.\n";
    }
}
