<?php

namespace common\models\search;

use common\models\UserProfile;
use yii\data\ActiveDataProvider;

class UserProfileSearch extends UserProfile
{
    public function rules(): array
    {
        return [
            ['up_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find()->with(['upUser'])->andWhere(['up_call_recording_disabled' => true]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'user-profile-page',
                'pageSizeParam' => 'user-profile-per-page',
            ],
            'sort' => [
                'sortParam' => 'user-profile-sort',
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'up_user_id' => $this->up_user_id
        ]);

        return $dataProvider;
    }
}
