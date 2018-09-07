<?php

namespace common\models\search;

use common\models\ClientEmail;
use common\models\ClientPhone;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lead;
use yii\helpers\VarDumper;

/**
 * LeadSearch represents the model behind the search form of `common\models\Lead`.
 */
class LeadSearch extends Lead
{

    public $client_name;
    public $client_email;
    public $client_phone;

    public $statuses = [];
    public $created_date_from;
    public $created_date_to;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'employee_id', 'status', 'project_id', 'adults', 'children', 'infants', 'rating', 'called_expert'], 'integer'],
            [['client_name', 'client_email', 'client_phone'], 'string'],

            //['created_date_from', 'default', 'value' => '2018-01-01'],
            //['created_date_to', 'default', 'value' => date('Y-m-d')],

            [['uid', 'trip_type', 'cabin', 'notes_for_experts', 'created', 'updated', 'request_ip', 'request_ip_detail', 'offset_gmt', 'snooze_for', 'discount_id', 'bo_flight_id',
            'created_date_from', 'created_date_to', 'source_id', 'statuses'], 'safe'],
        ];
    }


    /**
     * @inheritdoc
     */
    /*public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels2 = [
            'statuses' => 'Statuses',
            'created_date_from' => 'Created date from',
            'created_date_to' => 'Created date to',
        ];

        $labels = array_merge($labels, $labels2);

        return $labels;
    }*/

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
    public function search2($params)
    {
        $query = Lead::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'snooze_for' => $this->snooze_for,
            'bo_flight_id' => $this->bo_flight_id,
            'rating' => $this->rating,
            'called_expert' => $this->called_expert,
        ]);


        if($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }


        if($this->created_date_from || $this->created_date_to) {

            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_to))]);
            }

        } else {

            if($this->created) {
                $query->andFilterWhere(['DATE(created)'=> date('Y-m-d', strtotime($this->created))]);
            }
        }

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                        ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;


        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'trip_type', $this->trip_type])
            ->andFilterWhere(['like', 'cabin', $this->cabin])
            ->andFilterWhere(['like', 'notes_for_experts', $this->notes_for_experts])
            ->andFilterWhere(['like', 'request_ip', $this->request_ip])
            ->andFilterWhere(['like', 'request_ip_detail', $this->request_ip_detail])
            ->andFilterWhere(['like', 'offset_gmt', $this->offset_gmt])
            ->andFilterWhere(['like', 'discount_id', $this->discount_id]);

        //$sqlRaw = $query->createCommand()->getRawSql();
        //VarDumper::dump($sqlRaw, 10, true); exit;

        return $dataProvider;
    }
}
