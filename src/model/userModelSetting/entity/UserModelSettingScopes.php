<?php

namespace src\model\userModelSetting\entity;

/**
* @see UserModelSetting
*/
class UserModelSettingScopes extends \yii\db\ActiveQuery
{
    public function byUserId(int $userId): self
    {
        return $this->andWhere(['ums_user_id' => $userId]);
    }

    public function byClass(string $class): self
    {
        return $this->andWhere(['ums_class' => $class]);
    }

    public function byName(string $name): self
    {
        return $this->andWhere(['ums_name' => $name]);
    }

    public function last(): self
    {
        return $this->orderBy(['ums_id' => SORT_DESC])->limit(1);
    }
}
