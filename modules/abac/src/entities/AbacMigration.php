<?php

namespace modules\abac\src\entities;

use modules\abac\src\forms\AbacPolicyInsertForm;
use modules\featureFlag\FFlag;
use src\helpers\ErrorsToStringHelper;
use yii\db\Migration;

class AbacMigration extends Migration
{
    public function insert($table, $columns)
    {
        /** @fflag FFlag::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION, Validate Abac policy in migration Enable */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION)) {
            $form = new AbacPolicyInsertForm();
            if (!($form->load($columns) && $form->validate())) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($form));
            }
        }
        parent::insert($table, $columns);
    }
}
