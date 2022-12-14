<?php

namespace common\models\search;

use common\models\Department;
use common\models\Employee;
use common\models\ProjectEmployeeAccess;
use common\models\UserDepartment;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserConnection;
use common\models\UserGroupAssign;
use Yii;
use yii\db\Query;

/**
 * UserConnectionSearch represents the model behind the search form of `common\models\UserConnection`.
 *
 * @property int $dep_id
 * @property array $ug_ids
 * @property array $project_ids
 */
class UserConnectionSearch extends UserConnection
{
    public $dep_id;

    /**
     * user groups id's
     *
     * @var array
     */
    public $ug_ids = [];

    public $project_ids = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uc_id', 'uc_connection_id', 'uc_user_id', 'uc_lead_id', 'uc_case_id', 'dep_id', 'uc_window_state', 'uc_idle_state'], 'integer'],
            [['uc_user_agent', 'uc_controller_id', 'uc_action_id', 'uc_page_url', 'uc_ip', 'uc_connection_uid', 'uc_app_instance', 'uc_sub_list'], 'safe'],
            ['ug_ids', 'each', 'rule' => ['integer']],
            [['uc_window_state_dt', 'uc_idle_state_dt', 'uc_created_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = UserConnection::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['uc_id' => SORT_DESC]
            ],
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

        if (!empty($this->uc_created_dt)) {
            $query->andFilterWhere(['>=','uc_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->uc_created_dt))])
                ->andFilterWhere(['<=', 'uc_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->uc_created_dt) + 3600 * 24)]);
        }

        if (!empty($this->uc_idle_state_dt)) {
            $query->andFilterWhere(['>=','uc_idle_state_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->uc_idle_state_dt))]);
        }

        if (!empty($this->uc_window_state_dt)) {
            $query->andFilterWhere(['>=','uc_window_state_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->uc_window_state_dt))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'uc_id' => $this->uc_id,
            'uc_connection_id' => $this->uc_connection_id,
            'uc_user_id' => $this->uc_user_id,
            'uc_lead_id' => $this->uc_lead_id,
            'uc_case_id' => $this->uc_case_id,
            'uc_connection_uid' => $this->uc_connection_uid,
            'uc_app_instance' => $this->uc_app_instance,
            'uc_idle_state' => $this->uc_idle_state,
            'uc_window_state' => $this->uc_window_state,

        ]);

        $query->andFilterWhere(['like', 'uc_user_agent', $this->uc_user_agent])
            ->andFilterWhere(['like', 'uc_controller_id', $this->uc_controller_id])
            ->andFilterWhere(['like', 'uc_action_id', $this->uc_action_id])
            ->andFilterWhere(['like', 'uc_page_url', $this->uc_page_url])
            ->andFilterWhere(['like', 'uc_sub_list', $this->uc_sub_list])
            ->andFilterWhere(['like', 'uc_ip', $this->uc_ip]);

        return $dataProvider;
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchUserCallMap($params): ActiveDataProvider
    {
        $query = UserConnection::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
             $query->where('0=1');
            return $dataProvider;
        }


        $query->select(['uc_user_id']); //'cnt' => 'COUNT(*)',
        $query->groupBy(['uc_user_id']);

        if ($this->dep_id > 0) {
            $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => $this->dep_id]);
            $query->andWhere(['IN', 'uc_user_id', $subQuery]);
        } elseif ($this->dep_id === 0) {
            $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => array_keys(Department::DEPARTMENT_LIST)]);
            $query->andWhere(['NOT IN', 'uc_user_id', $subQuery]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'uc_user_id', $subQuery]);
        }

        $query->cache(5);
        //$query->with(['ucUser']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchUsersByCallMap($params): ActiveDataProvider
    {
        $query = UserConnection::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
             $query->where('0=1');
            return $dataProvider;
        }


        $query->select(['uc_user_id']); //'cnt' => 'COUNT(*)',
        $query->groupBy(['uc_user_id']);

        if ($this->dep_id > 0) {
            $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => $this->dep_id]);
            $query->andWhere(['IN', 'uc_user_id', $subQuery]);
        } elseif ($this->dep_id === 0) {
            $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => array_keys(Department::DEPARTMENT_LIST)]);
            $query->andWhere(['NOT IN', 'uc_user_id', $subQuery]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'uc_user_id', $subQuery]);
        }

        if ($this->project_ids) {
            $subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $this->project_ids]);
            $query->andWhere(['IN', 'uc_user_id', $subQuery]);
        }

        $query->cache(5);
        //$query->with(['ucUser']);

        return $dataProvider;
    }

    public function searchRealtimeUserCallMap($params)
    {
        $this->load($params);

        $query = UserConnection::find()->joinWith(['ucUser', 'ucUser.userStatus']);
        $query->select(['uc_user_id', 'username', 'us_call_phone_status', 'us_is_on_call']);
        $query->addSelect([
            'user_roles' => (new Query())
                ->select(['group_concat(auth_assignment.item_name SEPARATOR "-")'])
                ->from('auth_assignment')
                ->where('auth_assignment.user_id = uc_user_id')
        ]);
        $query->groupBy(['uc_user_id']);

        if ($this->dep_id > 0) {
            $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => $this->dep_id]);
            $query->andWhere(['IN', 'uc_user_id', $subQuery]);
        } elseif ($this->dep_id === 0) {
            $subQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => array_keys(Department::DEPARTMENT_LIST)]);
            $query->andWhere(['NOT IN', 'uc_user_id', $subQuery]);
        }

        if ($this->ug_ids) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['ugs_group_id' => $this->ug_ids]);
            $query->andWhere(['IN', 'uc_user_id', $subQuery]);
        }

        $query->cache(5);
        $command = $query->createCommand();

        return $command->queryAll();
    }
}
