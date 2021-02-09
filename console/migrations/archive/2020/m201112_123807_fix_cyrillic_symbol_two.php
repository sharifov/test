<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;
use yii\rbac\Permission;

/**
 * Class m201112_123807_fix_cyrillic_symbol_two
 */
class m201112_123807_fix_cyrillic_symbol_two extends Migration
{
    public $routeWithCyrillic = [
        'client-сhat/dashboard/filter/channel',
        'client-сhat/dashboard/filter/status',
        'client-сhat/dashboard/filter/user',
        'client-сhat/dashboard/filter/created_date',
        'client-сhat/dashboard/filter/department',
        'client-сhat/dashboard/filter/project',
        'client-сhat/dashboard/filter/read_unread',
        'client-сhat/dashboard/filter/group/my_chats',
        'client-сhat/dashboard/filter/group/other_chats',
        'client-сhat/dashboard/filter/group/free_to_take_chats',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->routeWithCyrillic as $name) {
            if ($permission = $auth->getPermission($name)) {
                $result = $auth->remove($permission);
                echo $name . ' removed (' . (int) $result . ").\n";
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201112_123807_fix_cyrillic_symbol_two cannot be reverted.\n";
        return false;
    }
}
