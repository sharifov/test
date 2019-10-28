<?php

namespace common\models\search;

use common\models\Call;
use common\models\Employee;
use common\models\Lead;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadQcall;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * LeadQcallSearch represents the model behind the search form of `common\models\LeadQcall`.
 *
 * @property string $current_dt
 * @property $attempts
 */
class LeadQcallSearch extends LeadQcall
{
    public $current_dt;

    public $attempts;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lqc_lead_id', 'lqc_weight'], 'integer'],

            [['lqc_dt_from', 'lqc_dt_to', 'current_dt'], 'safe'],

            ['attempts', 'safe'],
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
        $query = LeadQcall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lqc_weight' => SORT_ASC, 'lqc_dt_from' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 40,
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
            'lqc_lead_id' => $this->lqc_lead_id,
            'lqc_dt_from' => $this->lqc_dt_from,
            'lqc_dt_to' => $this->lqc_dt_to,
            'lqc_weight' => $this->lqc_weight,
        ]);

        return $dataProvider;
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchList($params): ActiveDataProvider
    {
        $query = LeadQcall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lqc_weight' => SORT_ASC, 'lqc_dt_from' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->current_dt) {
            $current_dt = Employee::convertTimeFromUserDtToUTC(strtotime($this->current_dt));
            //echo $current_dt; exit;
            $query->andWhere(['<=', 'lqc_dt_from', $current_dt]);
            //$query->andWhere(['>=', 'lqc_dt_to', $current_dt]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lqc_lead_id' => $this->lqc_lead_id,
            'lqc_dt_from' => $this->lqc_dt_from,
            'lqc_dt_to' => $this->lqc_dt_to,
            'lqc_weight' => $this->lqc_weight,
        ]);

        $query->with(['lqcLead', 'lqcLead.project', 'lqcLead.source', 'lqcLead.employee']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchByRedial($params, Employee $user): ActiveDataProvider
    {
        $query = LeadQcall::find()->select('*');

        $query->addSelect([
            'attempts' => (new Query())
                ->select('count(*)')
                ->from(Call::tableName())
                ->andWhere('c_lead_id = lqc_lead_id')
                ->andWhere(['c_is_deleted' => false])
                ->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT])
                ->andWhere(['IS NOT', 'c_parent_id', null])
        ]);

        $query->addOrderBy([
            'lqc_dt_to' => SORT_ASC,
            'attempts' => SORT_ASC,
            'lqc_dt_from' => SORT_ASC
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort'=> [
//                'defaultOrder' => [
//                    'lqc_dt_to' => SORT_ASC,
//                    'attempts' => SORT_ASC,
//                    'lqc_dt_from' => SORT_ASC,
//                ]
//            ],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['<=', 'lqc_dt_from', date('Y-m-d H:i:s')]);

        if ($user->isAgent() || $user->isSupervision()) {
            $query->andWhere(['l_call_status_id' => Lead::CALL_STATUS_READY]);
            $query->andWhere([Lead::tableName() . '.status' => Lead::STATUS_PENDING]);
        }

//        $query->andFilterWhere([
//            'lqc_lead_id' => $this->lqc_lead_id,
//            'lqc_dt_from' => $this->lqc_dt_from,
//            'lqc_dt_to' => $this->lqc_dt_to,
//        ]);

        $query->with(['lqcLead.project', 'lqcLead.source', 'lqcLead.employee']);

        $query->joinWith('lqcLead');

//        VarDumper::dump($query->createCommand()->getRawSql());die;

        return $dataProvider;
    }
}
