<?php

namespace sales\repositories\cases;

use common\models\Employee;
use sales\entities\cases\Cases;
use yii\db\ActiveQuery;

class CasesQRepository
{

    /**
     * @param Employee $user
     * @return int
     */
    public function getPendingCount(Employee $user): int
    {
        return $this->getPendingQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getPendingQuery(Employee $user): ActiveQuery
    {
        $query = Cases::find()->andWhere(['cs_status' => Cases::STATUS_PENDING]);

        if ($user->isAdmin()) {
            return $query;
        }

        return $query->andWhere('0 = 1');
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getProcessingCount(Employee $user): int
    {
        return $this->getProcessingQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getProcessingQuery(Employee $user): ActiveQuery
    {
        $query = Cases::find()->andWhere(['cs_status' => Cases::STATUS_PROCESSING]);

        if ($user->isAdmin()) {
            return $query;
        }

        $query->andWhere('0 = 1');

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getFollowupCount(Employee $user): int
    {
        return $this->getFollowupQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getFollowupQuery(Employee $user): ActiveQuery
    {
        $query = Cases::find()->andWhere(['cs_status' => Cases::STATUS_FOLLOW_UP]);

        if ($user->isAdmin()) {
            return $query;
        }

        $query->andWhere('0 = 1');

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getSolvedCount(Employee $user): int
    {
        return $this->getSolvedQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getSolvedQuery(Employee $user): ActiveQuery
    {
        $query = Cases::find()->where(['cs_status' => Cases::STATUS_SOLVED]);

        if ($user->isAdmin()) {
            return $query;
        }

        $query->andWhere('0 = 1');

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getTrashCount(Employee $user): int
    {
        return $this->getTrashQuery($user)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getTrashQuery(Employee $user): ActiveQuery
    {
        $query = Cases::find()->andWhere(['cs_status' => Cases::STATUS_TRASH]);

        if ($user->isAdmin()) {
            return $query;
        }

        $query->andWhere('0 = 1');

        return $query;
    }

    /**
     * @param $userId
     * @return array
     */
    private function isOwner($userId): array
    {
        return ['cs_user_id' => $userId];
    }

    /**
     * @return array
     */
    private function freeCase(): array
    {
        return ['cs_user_id' => null];
    }

}
