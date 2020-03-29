<?php

namespace common\models\query;

use common\models\Department;
use common\models\DepartmentPhoneProject;

/**
 * This is the ActiveQuery class for [[DepartmentPhoneProject]].
 *
 * @see DepartmentPhoneProject
 */
class DepartmentPhoneProjectQuery extends \yii\db\ActiveQuery
{
//    /**
//     * @param int|null $projectId
//     * @param int|null $departmentId
//     * @return $this
//     */
//    public function redialPhones(?int $projectId, ?int $departmentId): self
//    {
//        $query = $this
//            ->select(['dpp_phone_number'])
//            ->andWhere(['dpp_enable' => true])
//            ->andWhere(['dpp_project_id' => $projectId])
//            ->andWhere(['dpp_redial' => true])
//            ->andWhere(['IS NOT', 'dpp_phone_number', null]);
//
//        if ($departmentId === null) {
//            $departmentId = Department::DEPARTMENT_SALES;
//        }
//
//        $query->andWhere(['dpp_dep_id' => $departmentId]);
//
//        return $query;
//    }

    /**
     * @param string|array $phone
     * @param bool $eagerLoading
     * @param bool $onyEnabled
     * @return $this
     */
    public function byPhone($phone, bool $eagerLoading = true, bool $onyEnabled = false): self
    {
        if ($onyEnabled) {
            $this->innerJoinWith(['phoneList' => static function(\sales\model\phoneList\entity\Scopes $query) use ($phone) {
                $query
                    ->andOnCondition(['pl_enabled' => true])
                    ->andOnCondition(['pl_phone_number' => $phone]);
            }], $eagerLoading);
        } else {
            $this->innerJoinWith(['phoneList' => static function(\sales\model\phoneList\entity\Scopes $query) use ($phone) {
                $query->andOnCondition(['pl_phone_number' => $phone]);
            }], $eagerLoading);
        }
        return $this->orderBy(['dpp_updated_dt' => SORT_DESC]);
    }

    public function withPhoneList(bool $onyEnabled = false): self
    {
        if ($onyEnabled) {
            return $this->with(['phoneList' => static function(\sales\model\phoneList\entity\Scopes $query) {
                $query->enabled();
            }]);
        }
        return $this->with(['phoneList']);
    }

    public function enabled(): self
    {
        return $this->andWhere(['dpp_enable' => true]);
    }

    public function redial(): self
    {
        return $this->andWhere(['dpp_redial' => true]);
    }

    public function byProject($projectId): self
    {
        return $this->andWhere(['dpp_project_id' => $projectId]);
    }

    public function byDepartment($departmentId): self
    {
        return $this->andWhere(['dpp_dep_id' => $departmentId]);
    }
}
