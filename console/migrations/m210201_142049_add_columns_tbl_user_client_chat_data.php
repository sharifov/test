<?php

use common\models\Employee;
use common\models\UserProfile;
use console\migrations\RbacMigrationService;
use sales\model\userClientChatData\entity\UserClientChatData;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m210201_142049_add_columns_tbl_user_client_chat_data
 */
class m210201_142049_add_columns_tbl_user_client_chat_data extends Migration
{
    private array $routes = [
        '/user-client-chat-data-crud/index',
        '/user-client-chat-data-crud/view',
        '/user-client-chat-data-crud/create',
        '/user-client-chat-data-crud/update',
        '/user-client-chat-data-crud/delete',

        '/user-client-chat-data/index',
        '/user-client-chat-data/view',
        '/user-client-chat-data/create',
        '/user-client-chat-data/update',
        '/user-client-chat-data/delete',
        '/user-client-chat-data/deactivate',
        '/user-client-chat-data/activate',
        '/user-client-chat-data/refresh-rocket-chat-user-token',
        '/user-client-chat-data/validate-rocket-chat-credential',
        '/user-client-chat-data/user-info',
    ];

    private array $oldRoutes = [
        '/user-client-chat-data-crud/*',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_chat_data}}', 'uccd_auth_token', $this->string(50));
        $this->addColumn('{{%user_client_chat_data}}', 'uccd_rc_user_id', $this->string(20));
        $this->addColumn('{{%user_client_chat_data}}', 'uccd_password', $this->string(50));
        $this->addColumn('{{%user_client_chat_data}}', 'uccd_token_expired', $this->dateTime());
        $this->addColumn('{{%user_client_chat_data}}', 'uccd_username', $this->string(50));
        $this->addColumn('{{%user_client_chat_data}}', 'uccd_name', $this->string(50));

        $userProfiles = UserProfile::find()
            ->select([
                'username', 'nickname_client_chat',
                'up_user_id', 'up_rc_auth_token', 'up_rc_user_id', 'up_rc_user_password', 'up_rc_token_expired'
            ])
            ->innerJoin('employees', 'employees.id = user_profile.up_user_id')
            ->where(['IS NOT', 'up_rc_user_id', null])
            ->andWhere(['!=', 'up_rc_user_id', ''])
            ->indexBy('up_user_id')
            ->asArray()
            ->all();

        $n = 1;
        $total = count($userProfiles);
        Console::startProgress(0, $total, 'Processing UserProfiles: ', false);

        foreach ($userProfiles as $employeeId => $profile) {
            $data = [
                'uccd_auth_token' => $profile['up_rc_auth_token'],
                'uccd_rc_user_id' => $profile['up_rc_user_id'],
                'uccd_password' => $profile['up_rc_user_password'],
                'uccd_token_expired' => $profile['up_rc_token_expired'],
                'uccd_username' => $profile['username'],
                'uccd_name' => $profile['nickname_client_chat'],
            ];

            if (UserClientChatData::find()->where(['uccd_employee_id' => $employeeId])->exists()) {
                Yii::$app->db->createCommand()
                    ->update(
                        UserClientChatData::tableName(),
                        $data,
                        'uccd_employee_id = :employee_id',
                        [
                            ':employee_id' => $employeeId
                        ]
                    )
                    ->execute();
            } else {
                Yii::$app->db->createCommand()
                    ->insert(UserClientChatData::tableName(), $data)->execute();
            }
            Console::updateProgress($n, $total);
            $n++;
        }
        Console::endProgress($total . '.' . PHP_EOL);

        (new RbacMigrationService())->down($this->oldRoutes, $this->roles);
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_chat_data}}', 'uccd_auth_token');
        $this->dropColumn('{{%user_client_chat_data}}', 'uccd_rc_user_id');
        $this->dropColumn('{{%user_client_chat_data}}', 'uccd_password');
        $this->dropColumn('{{%user_client_chat_data}}', 'uccd_token_expired');
        $this->dropColumn('{{%user_client_chat_data}}', 'uccd_username');
        $this->dropColumn('{{%user_client_chat_data}}', 'uccd_name');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        (new RbacMigrationService())->up($this->oldRoutes, $this->roles);
    }
}
