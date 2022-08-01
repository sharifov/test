<?php

namespace common\models\search;

use common\models\Department;
use common\models\ProjectEmployeeAccess;
use common\models\UserConnection;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use src\model\user\entity\userStatus\UserStatus;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserOnline;

/**
 * UserOnlineSearch represents the model behind the search form of `common\models\UserOnline`.
 *
 * @property array $dep_ids
 * @property array $ug_ids
 * @property array $project_ids
 * @property array $user_id
 */
class UserOnlineSearch extends UserOnline
{
    public array $dep_ids = [];

    public array $ug_ids = [];

    public array $project_ids = [];

    public int $user_id = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uo_user_id', 'uo_idle_state', 'user_id'], 'integer'],
            [['uo_updated_dt', 'uo_idle_state_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['dep_ids', 'each', 'rule' => ['integer']],
            ['ug_ids', 'each', 'rule' => ['integer']],
            ['project_ids', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserOnline::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['uo_updated_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'uo_user_id' => $this->uo_user_id,
            'DATE(uo_updated_dt)' => $this->uo_updated_dt,
            'uo_idle_state' => $this->uo_idle_state,
            'DATE(uo_idle_state_dt)' => $this->uo_idle_state_dt,
        ]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return array|UserOnline[]
     */
    public function searchUserByIncomingCall($params): array
    {
        $query = self::find();

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return [];
        }

        $query->select(['uo_user_id', 'uo_idle_state', 'GROUP_CONCAT(ud_dep_id) as userDep']); //'cnt' => 'COUNT(*)',
        $query->groupBy(['uo_user_id', 'uo_idle_state']);

        if (!empty($this->dep_ids)) {
            $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['IN', 'ud_dep_id', $this->dep_ids]);
            $query->andWhere(['IN', 'uo_user_id', $subQuery]);

            if (in_array(0, $this->dep_ids)) {
                $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => array_keys(Department::DEPARTMENT_LIST)]);
                $query->orWhere(['NOT IN', 'uo_user_id', $subQuery]);
            }
        }

        $query->leftJoin(UserDepartment::tableName(), 'ud_user_id=uo_user_id');

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'uo_user_id', $subQuery]);
        }

        if ($this->project_ids) {
            $subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $this->project_ids]);
            $query->andWhere(['IN', 'uo_user_id', $subQuery]);
        }

        $query->orWhere(['uo_user_id' => $this->user_id]);

        $query->cache(5);
        //$query->with(['ucUser']);

        return $query->asArray()->all();
    }
}
