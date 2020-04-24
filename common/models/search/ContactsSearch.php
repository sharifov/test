<?php

namespace common\models\search;

use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\UserContactList;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;
use yii\helpers\ArrayHelper;

/**
 * ContactsSearch represents the model behind the search form of `common\models\Client`.
 *
 * @property int $userId
 * @property int $isPublic
 * @property int $isDisabled
 */
class ContactsSearch extends Client
{
    public $client_email;
    public $client_phone;

    public $userId;
    public $isPublic = true;
    public $isDisabled = false;

    /**
     * @param int $userId
     * @param bool $isPublic
     * @param bool $isDisabled
     * @param array $config
     */
    public function __construct(int $userId, bool $isPublic = true, bool $isDisabled = false, $config = [])
    {
        $this->userId = $userId;
        $this->isPublic = $isPublic;
        $this->isDisabled = $isDisabled;
        parent::__construct($config);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Client::find();

        $query->innerJoin(UserContactList::tableName() . ' AS user_contact_list',
            'user_contact_list.ucl_client_id = ' . Client::tableName() . '.id');

        $query->andWhere(['user_contact_list.ucl_user_id' => $this->userId])
            ->andWhere(['is_public' => $this->isPublic])
            ->andWhere(['disabled' => $this->isDisabled]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->created){
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 *24)]);
        }
        if ($this->updated){
            $query->andFilterWhere(['>=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated))])
                ->andFilterWhere(['<=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated) + 3600 *24)]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'id', $subQuery]);
        }
        if ($this->client_phone) {
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'id', $subQuery]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created' => $this->created,
            'updated' => $this->updated,
            'parent_id' => $this->parent_id,
            'is_company' => $this->is_company,
            'is_public' => $this->is_public,
            'disabled' => $this->disabled,
            'rating' => $this->rating,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
