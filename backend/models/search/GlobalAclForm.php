<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GlobalAcl;

/**
 * GlobalAclSearch represents the model behind the search form about `common\models\GlobalAcl`.
 */
class GlobalAclForm extends GlobalAcl
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mask'], 'safe'],
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
        $query = GlobalAcl::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'mask' => $this->mask,
        ]);

        return $dataProvider;
    }
}