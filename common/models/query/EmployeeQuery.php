<?php

namespace common\models\query;

use common\models\Employee;
use common\models\UserOnline;
use common\models\UserProfile;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\userClientChatData\entity\UserClientChatData;

/**
 * Class EmployeeQuery
 */
class EmployeeQuery extends \yii\db\ActiveQuery
{

    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->andWhere(['status' => Employee::STATUS_ACTIVE]);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function byEmail(string $email): self
    {
        return $this->andWhere(['email' => $email])->limit(1);
    }

    public function joinChatUserChannel(int $channelId): EmployeeQuery
    {
        return $this->innerJoin(
            ClientChatUserChannel::tableName(),
            'ccuc_channel_id = :channelId and ccuc_user_id = id',
            ['channelId' => $channelId]
        );
    }

    public function online(): EmployeeQuery
    {
        return $this->innerJoin(UserOnline::tableName(), 'ccuc_user_id = uo_user_id');
    }

    public function exceptUser(int $userId): EmployeeQuery
    {
        return $this->andWhere(['<>', 'id', $userId]);
    }

    public function registeredInRc(): EmployeeQuery
    {
        return $this->innerJoin(
            UserClientChatData::tableName(),
            "uccd_employee_id = id AND uccd_rc_user_id IS NOT NULL AND uccd_rc_user_id <> ''"
        );
    }

//    public function supervisorsByGroups(array $groups)
//  {
//      return $this->leftJoin('auth_assignment','auth_assignment.user_id = id')->andWhere(['auth_assignment.item_name' => Employee::SUPE])->innerJoin(UserGroup::tableName(), new Expression(''))
//  }
}
