<?php

namespace sales\entities\cases;

use common\models\CaseSale;
use common\models\Department;
use yii\db\ActiveQuery;
use yii\db\Expression;

class CasesQuery extends ActiveQuery
{
    public function findLastActiveClientCaseByDepartment(int $departmentId, int $clientId, ?int $projectId, int $trashActiveDaysLimit): self
    {
        return $this->findLastActiveClientCase($clientId, $projectId, $trashActiveDaysLimit)->byDepartment($departmentId);
    }

    public function findLastClientCaseByDepartment(int $departmentId, int $clientId, ?int $projectId): self
    {
        return $this->findLastClientCase($clientId, $projectId)->byDepartment($departmentId);
    }

    public function findLastActiveClientCase(int $clientId, ?int $projectId, int $trashActiveDaysLimit): self
    {
        $query = $this->findLastClientCase($clientId, $projectId)->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED]]);

        if ($trashActiveDaysLimit > 0) {
            $limit = (new \DateTimeImmutable())->modify('- ' . $trashActiveDaysLimit . 'day');
            $query->andWhere(['OR',
                ['NOT IN', 'cs_status', [CasesStatus::STATUS_TRASH]],
                ['>', 'cs_created_dt', $limit->format('Y-m-d H:i:s')],
            ]);
        } else {
            $query->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_TRASH]]);
        }

        return $query;
    }

    public static function findCasesByPhone(string $phone, ?bool $activeOnly, ?int $results_limit, ?int $projectId, ?int $departmentId): array
    {
        $query = Cases::find()
                ->select('cs_id, cs_gid, cs_status, cs_created_dt, cs_updated_dt, cs_last_action_dt, cs_category_id, cs_project_id, cs_dep_id, cs_order_uid, projects.name')
                ->leftJoin('client_phone', 'cs_client_id = client_phone.client_id')
                ->andWhere(['client_phone.phone' => $phone]);
        return self::findCasesPartial($query, $activeOnly, $results_limit, $projectId, $departmentId);
    }

    public static function findCasesByEmail(string $email, ?bool $activeOnly, ?int $results_limit, ?int $projectId, ?int $departmentId): array
    {
        $query = Cases::find()
            ->select('cs_id, cs_gid, cs_status, cs_created_dt, cs_updated_dt, cs_last_action_dt, cs_category_id, cs_project_id, cs_dep_id, cs_order_uid, projects.name')
            ->leftJoin('client_email', 'cs_client_id = client_email.client_id')
            ->andWhere(['client_email.email' => $email]);
        return self::findCasesPartial($query, $activeOnly, $results_limit, $projectId, $departmentId);
    }

    public static function findCaseByCaseGid(string $caseGid): array
    {
        $query = Cases::find()
            ->select('cs_id, cs_gid, cs_status, cs_created_dt, cs_updated_dt, cs_last_action_dt, cs_category_id, cs_project_id, cs_dep_id, cs_order_uid, projects.name')
            ->leftJoin('client_email', 'cs_client_id = client_email.client_id')
            ->andWhere(['cs_gid' => $caseGid]);
        return self::findCasesPartial($query, false, null, null, null);
    }

    public static function findCasesGidByPhone(string $phone, ?bool $activeOnly, ?int $results_limit, ?int $projectId, ?int $departmentId): array
    {
        $query = Cases::find()
            ->select('cs_gid, cs_status, cs_created_dt, cs_project_id, cs_dep_id')
            ->leftJoin('client_phone', 'cs_client_id = client_phone.client_id')
            ->andWhere(['client_phone.phone' => $phone]);
        return array_column(self::findCasesPartial($query, $activeOnly, $results_limit, $projectId, $departmentId), 'cs_gid');
    }

    public static function findCasesGidByEmail(string $email, ?bool $activeOnly, ?int $results_limit, ?int $projectId, ?int $departmentId): array
    {
        $query = Cases::find()
            ->select('cs_id, cs_gid, cs_status, cs_created_dt, cs_updated_dt, cs_last_action_dt, cs_category_id, cs_project_id, cs_dep_id, cs_order_uid, projects.name')
            ->leftJoin('client_email', 'cs_client_id = client_email.client_id')
            ->andWhere(['client_email.email' => $email]);
        return array_column(self::findCasesPartial($query, $activeOnly, $results_limit, $projectId, $departmentId), 'cs_gid');
    }

    public static function findCasesPartial(CasesQuery $query, ?bool $activeOnly, ?int $results_limit, ?int $projectId, ?int $departmentId): array
    {
        $where = [];
        if ($projectId) {
            $where['cs_project_id'] = $projectId;
        }
        if ($departmentId) {
            $where['cs_dep_id'] = $departmentId;
        }

        $query
                ->leftJoin('projects', 'cs_project_id = projects.id')
                ->addSelect(new Expression('
                        DATE (IF (last_out_date IS NULL, last_in_date, IF (last_in_date is NULL, last_out_date, LEAST (last_in_date, last_out_date)))) AS nextFlight'))
                ->leftJoin([
                    'sale_out' => CaseSale::find()
                        ->select([
                            'css_cs_id',
                            new Expression('
                        MIN(css_out_date) AS last_out_date'),
                        ])
                        ->innerJoin(
                            Cases::tableName() . ' AS cases',
                            'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PENDING
                        )
                        ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
                        ->groupBy('css_cs_id')
                ], 'cases.cs_id = sale_out.css_cs_id')
                ->leftJoin([
                    'sale_in' => CaseSale::find()
                        ->select([
                            'css_cs_id',
                            new Expression('
                        MIN(css_in_date) AS last_in_date'),
                        ])
                        ->innerJoin(
                            Cases::tableName() . ' AS cases',
                            'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PENDING
                        )
                        ->where('css_in_date >= SUBDATE(CURDATE(), 1)')
                        ->groupBy('css_cs_id')
                ], 'cases.cs_id = sale_in.css_cs_id')
                ->andWhere($where)
                ->orderBy('cs_created_dt ASC')
                ->asArray();
//            ->createCommand()->getRawSql();

        if ($activeOnly) {
            $deps_params = [];
            $deps = Department::find()->all();
            foreach ($deps as $dep) {
                $deps_params[$dep->dep_id] = $dep->getParams()->object->case->trashActiveDaysLimit;
            }

            $query->andWhere('cs_status != ' . CasesStatus::STATUS_SOLVED);
            $cases = $query->all();

            if (!empty($cases)) {
                $result_cases_cnt = 0;
                foreach ($cases as $key => $case) {
                    $cases[$key]['status_name'] = CasesStatus::getName($case['cs_status']);
                    $limit_dt = (new \DateTimeImmutable($case['cs_created_dt']))->modify('+' . $deps_params[$case['cs_dep_id']] . 'day');
                    if (
                        ($case['cs_status'] == CasesStatus::STATUS_TRASH && (strtotime($limit_dt->format('Y-m-d H:i:s')) < time()))
                        || ($results_limit && $result_cases_cnt >= $results_limit)
                    ) {
                        unset($cases[$key]);
                    } else {
                        $result_cases_cnt++;
                    }
                }
                return array_values($cases);
            }
        } else {
            $cases = $query->all();

            if (!empty($cases)) {
                $result_cases_cnt = 0;
                foreach ($cases as $key => $case) {
                    $cases[$key]['status_name'] = CasesStatus::getName($case['cs_status']);
                    if ($results_limit && $result_cases_cnt >= $results_limit) {
                        unset($cases[$key]);
                    } else {
                        $result_cases_cnt++;
                    }
                }
                return $cases;
            }
        }
        return [];
    }

    public function findLastClientCase(int $clientId, ?int $projectId): self
    {
        return $this
            ->andWhere(['cs_client_id' => $clientId])
            ->andFilterWhere(['cs_project_id' => $projectId])
            ->orderBy(['cs_last_action_dt' => SORT_DESC])
            ->limit(1);
    }

    public function bySupport(): self
    {
        return $this->andWhere(['cs_dep_id' => Department::DEPARTMENT_SUPPORT]);
    }

    public function byExchange(): self
    {
        return $this->andWhere(['cs_dep_id' => Department::DEPARTMENT_EXCHANGE]);
    }

    public function byDepartment(int $departmentId): self
    {
        return $this->andWhere(['cs_dep_id' => $departmentId]);
    }

    public function withNotFinishStatus(): self
    {
        return $this->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]]);
    }

    /**
     * @param null $db
     * @return Cases[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return Cases|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
