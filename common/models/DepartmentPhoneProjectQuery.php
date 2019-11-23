<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[DepartmentPhoneProject]].
 *
 * @see DepartmentPhoneProject
 */
class DepartmentPhoneProjectQuery extends \yii\db\ActiveQuery
{
    /**
     * @param int|null $projectId
     * @param int|null $departmentId
     * @return $this
     */
    public function redialPhones(?int $projectId, ?int $departmentId): self
    {
        $query = $this
            ->select(['dpp_phone_number'])
            ->andWhere(['dpp_enable' => true])
            ->andWhere(['dpp_project_id' => $projectId])
            ->andWhere(['dpp_redial' => true])
            ->andWhere(['IS NOT', 'dpp_phone_number', null]);

        if ($departmentId === null) {
            $query->andWhere(['or',
                ['dpp_dep_id' => Department::DEPARTMENT_SALES],
                ['IS', 'dpp_dep_id', NULL]
            ]);
        } else {
            $query->andWhere(['dpp_dep_id' => $departmentId]);
        }

        return $query;
    }

    /**
     * @param string $phoneNumber
     * @return bool
     */
    public function isRedial(string $phoneNumber): bool
    {
        return $this
            ->andWhere(['dpp_enable' => true])
            ->andWhere(['dpp_phone_number' => $phoneNumber])
            ->andWhere(['dpp_redial'=> true])
            ->exists();
    }

    /**
     * @param string $phone
     * @return DepartmentPhoneProject|null
     */
    public function findByPhone(string $phone):? DepartmentPhoneProject
    {
        return $this->where(['dpp_phone_number' => $phone, 'dpp_enable' => true])->limit(1)->one();
    }
}
