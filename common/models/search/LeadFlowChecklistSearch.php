<?php

namespace common\models\search;

use common\models\Employee;
use common\models\UserGroupAssign;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadFlow;

/**
 * LeadFlowSearch represents the model behind the search form of `common\models\LeadFlow`.
 */
class LeadFlowChecklistSearch extends LeadFlow
{
    public $statuses = [];
    public $dateRange;

    public $supervision_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'employee_id', 'lead_id', 'status', 'supervision_id', 'lf_from_status_id'], 'integer'],
            [['created', 'statuses', 'dateRange'], 'safe'],
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

    public function attributeLabels()
    {
        return [
            'dateRange' => 'Create date from / to'
        ];
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
        $query = LeadFlow::find()->with('employee', 'lead', 'leadFlowChecklist', 'leadFlowChecklist.user');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
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

        if (isset($params['NotificationsSearch']['created'])) {
            $query->andFilterWhere(['=','DATE(created)', $this->created]);
        }

        if($this->statuses) {
            $query->andWhere(['lead_flow.status' => $this->statuses]);
        }

        if($this->dateRange) {
            $dates = explode(' - ', $this->dateRange);
            $query->andFilterWhere(['>=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($dates[0]))]);
            $query->andFilterWhere(['<=', 'lead_flow.created', Employee::convertTimeFromUserDtToUTC(strtotime($dates[1]))]);

            /*$query->andFilterWhere(['>=', 'lead_flow.created', date('Y-m-d H:i', strtotime($dates[0]))]);
            $query->andFilterWhere(['<=', 'lead_flow.created', date('Y-m-d H:i', strtotime($dates[1]))]);*/
        } else {
            if($this->created) {
                $query->andFilterWhere(['DATE(lead_flow.created)'=> date('Y-m-d', strtotime($this->created))]);
            }
        }

        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'lead_flow.employee_id', $subQuery]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'lead_id' => $this->lead_id,
            'status' => $this->status,
            'lf_from_status_id' => $this->lf_from_status_id
        ]);

        return $dataProvider;
    }
}
