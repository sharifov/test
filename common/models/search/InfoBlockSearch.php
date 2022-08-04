<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\InfoBlock;

/**
 * InfoBlockSearch represents the model behind the search form of `common\models\InfoBlock`.
 */
class InfoBlockSearch extends InfoBlock
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ib_id', 'ib_enabled', 'ib_created_user_id', 'ib_updated_user_id'], 'integer'],
            [['ib_title', 'ib_key', 'ib_description', 'ib_created_dt', 'ib_updated_dt'], 'safe'],
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
        $query = InfoBlock::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ib_updated_dt) {
            $query->andFilterWhere(['>=', 'ib_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ib_updated_dt))])
                ->andFilterWhere(['<=', 'ib_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ib_updated_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere([
            'ib_created_user_id' => $this->ib_created_user_id,
            'ib_updated_user_id' => $this->ib_updated_user_id,
            'ib_enabled' => $this->ib_enabled,
        ]);

        $query->andFilterWhere(['like', 'ib_key', $this->ib_key])
            ->andFilterWhere(['like', 'ib_title', $this->ib_title]);

        return $dataProvider;
    }
}
