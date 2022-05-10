<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Quote;

/**
 * QuoteSearch represents the model behind the search form of `common\models\Quote`.
 */
class QuoteSearch extends Quote
{
    public $datetime_start;
    public $datetime_end;
    public $date_range;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime_start', 'datetime_end'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['id', 'lead_id', 'employee_id', 'status', 'check_payment','q_create_type_id'], 'integer'],
            [['uid', 'record_locator', 'pcc', 'cabin', 'gds', 'trip_type', 'main_airline_code', 'reservation_dump', 'fare_type'], 'safe'],

            ['type_id', 'integer'],
            ['type_id', 'in', 'range' => array_keys(Quote::TYPE_LIST)],

            [['created', 'updated'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = Quote::find()->with('employee', 'lead');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (empty($this->created) && isset($params['QuoteSearch']['date_range'])) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if ($this->updated) {
            $query->andFilterWhere(['>=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated))])
                ->andFilterWhere(['<=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'check_payment' => $this->check_payment,
            'type_id' => $this->type_id,
            'q_create_type_id' => $this->q_create_type_id,

            //'created' => $this->created,
            //'updated' => $this->updated,
        ]);

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'record_locator', $this->record_locator])
            ->andFilterWhere(['like', 'pcc', $this->pcc])
            ->andFilterWhere(['like', 'cabin', $this->cabin])
            ->andFilterWhere(['like', 'gds', $this->gds])
            ->andFilterWhere(['like', 'trip_type', $this->trip_type])
            ->andFilterWhere(['like', 'main_airline_code', $this->main_airline_code])
            ->andFilterWhere(['like', 'reservation_dump', $this->reservation_dump])
            ->andFilterWhere(['like', 'fare_type', $this->fare_type]);

        return $dataProvider;
    }
}
