<?php

namespace src\model\emailQuote\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\emailQuote\entity\EmailQuote;

/**
 * EmailQuoteSearch represents the model behind the search form of `src\model\emailQuote\entity\EmailQuote`.
 */
class EmailQuoteSearch extends EmailQuote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eq_id', 'eq_email_id', 'eq_quote_id', 'eq_created_by'], 'integer'],
            [['eq_created_dt'], 'safe'],
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
        $query = EmailQuote::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'eq_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'eq_id' => $this->eq_id,
            'eq_email_id' => $this->eq_email_id,
            'eq_quote_id' => $this->eq_quote_id,
            'eq_created_dt' => $this->eq_created_dt,
            'eq_created_by' => $this->eq_created_by,
        ]);

        return $dataProvider;
    }
}
