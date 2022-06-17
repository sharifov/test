<?php

namespace modules\abac\migrations;

use frontend\helpers\JsonHelper;
use modules\abac\src\entities\AbacPolicy;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use yii\db\Migration;

/**
 * Class m220617_110018_correction_abac_subject
 */
class m220617_110018_correction_abac_subject extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($list = AbacPolicy::find()->where(['ap_subject' => ''])->all()) {
            foreach ($list as $abacPolicy) {
                if (empty($abacPolicy->getDecodeCode())) {
                    try {
                        $rules = JsonHelper::decode($abacPolicy->ap_subject_json);
                        $abacPolicy->ap_subject_json = $rules;

                        if (!$abacPolicy->save(false)) {
                            throw new \RuntimeException('AbacPolicy not saved. ' .
                                ErrorsToStringHelper::extractFromModel($abacPolicy, ' '));
                        }
                    } catch (\Throwable $throwable) {
                        $message = AppHelper::throwableLog($throwable);
                        $message['ap_id'] = $abacPolicy->ap_id;
                        \Yii::error($message, 'm220617_110018_correction_abac_subject:Throwable');
                    }
                }
            }
        }
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220617_110018_correction_abac_subject cannot be reverted.\n";
    }
}
