<?php

namespace common\models\search;

use common\models\ClientEmail;
use common\models\ClientPhone;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lead;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\debug\models\timeline\DataProvider;
use yii\helpers\VarDumper;

/**
 * LeadSearch represents the model behind the search form of `common\models\Lead`.
 */
class LeadSearch extends Lead
{

    public $client_name;
    public $client_email;
    public $client_phone;
    public $cnt;

    public $statuses = [];
    public $created_date_from;
    public $created_date_to;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'employee_id', 'status', 'project_id', 'adults', 'children', 'infants', 'rating', 'called_expert', 'cnt'], 'integer'],
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

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

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

    public function searchAgent($params)
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
            $query->where('0=1');
            return $dataProvider;
        }


        /*'id' => ''
        'uid' => ''
        'client_id' => ''
        'client_name' => ''
        'client_email' => ''
        'client_phone' => ''
        'bo_flight_id' => ''
        'employee_id' => ''*/


        if($this->id || $this->uid || $this->client_id || $this->client_name || $this->client_email || $this->client_phone || $this->bo_flight_id || $this->employee_id || $this->request_ip) {

        } else {
            $this->employee_id = Yii::$app->user->id;
        }

        //VarDumper::dump($params, 10, true); exit;

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

            'uid' => $this->uid,
            'trip_type' => $this->trip_type,
            'cabin' => $this->cabin,
            'request_ip' => $this->request_ip,
            'discount_id' => $this->discount_id,
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
                    $q->where(['=', 'clients.last_name', $this->client_name])
                        ->orWhere(['=', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['=', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['=', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }




        return $dataProvider;
    }


    public function searchEmail($params)
    {

        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(*) AS cnt', 'ce.email AS client_email']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'ce.email', null]);

        if($this->client_email) {
            $query->andFilterWhere(['like', 'ce.email', $this->client_email]);
        }

        $query->leftJoin('client_email AS ce', 'ce.client_id = l.client_id');
        $query->groupBy('ce.email');
        $query->having(['>', 'cnt', 1]);
        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            //'sort' =>false, to remove the table header sorting
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'client_email' => [
                        'asc' => ['client_email' => SORT_ASC],
                        'desc' => ['client_email' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Email',
                    ],
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    public function searchPhone($params)
    {

        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(*) AS cnt', 'cp.phone AS client_phone']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'cp.phone', null]);

        if($this->client_phone) {
            $query->andFilterWhere(['like', 'cp.phone', $this->client_phone]);
        }

        $query->leftJoin('client_phone AS cp', 'cp.client_id = l.client_id');
        $query->groupBy('cp.phone');
        $query->having(['>', 'cnt', 1]);

        $command = $query->createCommand();
        $sql = $command->rawSql;



        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            //'sort' =>false, to remove the table header sorting
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'client_phone' => [
                        'asc' => ['client_phone' => SORT_ASC],
                        'desc' => ['client_phone' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Phone',
                    ],
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }


        return $dataProvider;
    }

    public function searchIp($params)
    {


        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(*) AS cnt', 'l.request_ip']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'l.request_ip', null]);

        if($this->request_ip) {
            $query->andFilterWhere(['like', 'l.request_ip', $this->request_ip]);
        }

        $query->groupBy('l.request_ip');
        $query->having(['>', 'cnt', 1]);

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                    'request_ip' => [
                        'asc' => ['request_ip' => SORT_ASC],
                        'desc' => ['request_ip' => SORT_DESC],
                        'label' => 'IP',
                    ],

                    //'request_ip'
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

}
