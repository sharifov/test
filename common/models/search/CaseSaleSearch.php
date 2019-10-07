<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CaseSale;
use yii\helpers\VarDumper;

/**
 * CaseSaleSearch represents the model behind the search form of `common\models\CaseSale`.
 */
class CaseSaleSearch extends CaseSale
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['css_cs_id', 'css_sale_id', 'css_sale_pax', 'css_created_user_id', 'css_updated_user_id'], 'integer'],
            [['css_sale_book_id', 'css_sale_pnr', 'css_sale_created_dt', 'css_sale_data', 'css_created_dt', 'css_updated_dt'], 'safe'],
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
        $query = CaseSale::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->css_sale_created_dt){
            $query->andFilterWhere(['>=', 'css_sale_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_sale_created_dt))])
                ->andFilterWhere(['<=', 'css_sale_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_sale_created_dt) + 3600 * 24)]);
        }

        if ($this->css_created_dt){
            $query->andFilterWhere(['>=', 'css_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_created_dt))])
                ->andFilterWhere(['<=', 'css_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_created_dt) + 3600 * 24)]);
        }

        if ($this->css_updated_dt){
            $query->andFilterWhere(['>=', 'css_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_updated_dt))])
                ->andFilterWhere(['<=', 'css_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'css_cs_id' => $this->css_cs_id,
            'css_sale_id' => $this->css_sale_id,
            'css_sale_pax' => $this->css_sale_pax,
            //'css_sale_created_dt' => $this->css_sale_created_dt,
            'css_created_user_id' => $this->css_created_user_id,
            'css_updated_user_id' => $this->css_updated_user_id,
            //'css_created_dt' => $this->css_created_dt,
            //'css_updated_dt' => $this->css_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'css_sale_book_id', $this->css_sale_book_id])
            ->andFilterWhere(['like', 'css_sale_pnr', $this->css_sale_pnr])
            ->andFilterWhere(['like', 'css_sale_data', $this->css_sale_data]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchByCase($params)
    {
        $query = CaseSale::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['css_created_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['css_cs_id' => $this->css_cs_id]);

        // grid filtering conditions
        $query->andFilterWhere([
            'css_sale_id' => $this->css_sale_id,
            'css_sale_pax' => $this->css_sale_pax,
            'css_sale_created_dt' => $this->css_sale_created_dt,
            'css_created_user_id' => $this->css_created_user_id,
            'css_updated_user_id' => $this->css_updated_user_id,
            'css_created_dt' => $this->css_created_dt,
            'css_updated_dt' => $this->css_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'css_sale_book_id', $this->css_sale_book_id])
            ->andFilterWhere(['like', 'css_sale_pnr', $this->css_sale_pnr])
            ->andFilterWhere(['like', 'css_sale_data', $this->css_sale_data]);

        return $dataProvider;
    }


}
