<?php
namespace frontend\models\search;

use common\models\Airport;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class AirportForm extends Airport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iata', 'name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = Airport::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'iata' => $this->iata,
            'name' => $this->name
        ]);

        $dataProvider->sort->defaultOrder = ['iata' => SORT_ASC];

        return $dataProvider;
    }
}