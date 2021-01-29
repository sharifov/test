<?php

use common\models\Employee;
use common\models\UserProfile;
use yii\db\Migration;

/**
 * Class m210129_055002_add_column_up_rc_active_to_tbl_user_profile
 */
class m210129_055002_add_column_up_rc_active_to_tbl_user_profile extends Migration
{
    public $route = [
        '/employee/activate-to-rocket-chat',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_profile}}', 'up_rc_active', $this->boolean());

        UserProfile::updateAll(['up_rc_active' => true], ['IS NOT', 'up_rc_user_id', null]);

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_profile}}', 'up_rc_active');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
