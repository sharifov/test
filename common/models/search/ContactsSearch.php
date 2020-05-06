<?php

namespace common\models\search;

use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\ClientProject;
use common\models\Employee;
use common\models\UserContactList;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\model\emailList\entity\EmailList;
use sales\model\phoneList\entity\PhoneList;
use sales\model\user\entity\userStatus\UserStatus;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * ContactsSearch represents the model behind the search form of `common\models\Client`.
 *
 * @property int $userId
 * @property bool $isPublic
 * @property bool $isDisabled
 * @property bool $ucl_favorite
 * @property string $by_name
 * @property Employee $user
 * @property int $contact_project_id
 */
class ContactsSearch extends Client
{
    public $client_email;
    public $client_phone;
    public $by_name;
    public $contact_project_id;
    public $ucl_favorite;

    public $userId;
    public $isPublic = true;
    public $isDisabled = false;

    private $user;

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
        $this->user = Employee::findIdentity($this->userId);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'contact_project_id'], 'integer'],
            [['client_email', 'client_phone'], 'string'],
            [['first_name', 'middle_name', 'last_name', 'created', 'updated'], 'safe'],
            ['uuid', 'string', 'max' => 36],
            [['company_name', 'by_name'], 'string', 'max' => 150],
            [['is_company', 'is_public', 'disabled', 'ucl_favorite'], 'boolean'],
        ];
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

        if (!$this->isRoleAdmin()) {
            $query->andWhere(['user_contact_list.ucl_user_id' => $this->userId]);
            $query->orWhere(['AND',
               ['!=', 'user_contact_list.ucl_user_id', $this->userId],
               ['disabled' => $this->isDisabled],
               ['is_public' => $this->isPublic],
            ]);
        }

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
        if ($this->contact_project_id > 0) {
            $subQuery = ClientProject::find()->select(['DISTINCT(cp_client_id)'])->where(['=', 'cp_project_id', $this->contact_project_id]);
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
            'ucl_favorite' => $this->ucl_favorite,
        ]);

        if ($this->by_name)  {
            $query->andWhere(
                ['OR',
                    ['like', 'first_name', $this->by_name],
                    ['like', 'middle_name', $this->by_name],
                    ['like', 'last_name', $this->by_name],
                ]
            );
        }

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

//    public function searchByWidgetContactsSection(?string $q = null, ?int $limit = null): ActiveDataProvider
//    {
//        $queryClient = Client::find()->alias('c')->select([
//            'c.id as id',
//            'concat(IFNULL(c.first_name, \'\'), \' \', IFNULL(c.middle_name, \'\'), \' \', IFNULL(c.last_name, \'\')) as full_name',
//            'c.company_name as company_name',
//            'c.cl_type_id as type',
//            'c.is_company as is_company',
//            'cp.phone as phone',
//            'ce.email as email',
//            'c.description as description',
//        ]);
//
//        $queryClient->innerJoin(UserContactList::tableName(), 'ucl_client_id = c.id');
//        $queryClient->leftJoin(ClientPhone::tableName() . ' as cp', 'cp.client_id = c.id');
//        $queryClient->leftJoin(ClientEmail::tableName() . ' as ce', 'ce.client_id = c.id');
//
//        if (!$this->isRoleAdmin()) {
//            $queryClient->andWhere(['ucl_user_id' => $this->userId]);
//            $queryClient->orWhere(['AND',
//                ['!=', 'ucl_user_id', $this->userId],
//                ['c.disabled' => $this->isDisabled],
//                ['c.is_public' => $this->isPublic],
//            ]);
//        }
//
//        $queryUser = Employee::find()->alias('u')->select([
//            'u.id as id',
//            'u.full_name as full_name',
//            'company_name' => new Expression('null'),
//            'type' => new Expression(Client::TYPE_INTERNAL),
//            'is_company' => new Expression('null'),
//            'pl_phone_number as phone',
//            'el_email as email',
//            'description' => new Expression('null'),
//        ]);
//
//        $queryUser->innerJoin(UserProjectParams::tableName(), 'upp_user_id = u.id');
//        $queryUser->innerJoin(UserProfile::tableName(), 'up_user_id = u.id and up_show_in_contact_list = 1');
//        $queryUser->leftJoin(PhoneList::tableName(), 'pl_id = upp_phone_list_id');
//        $queryUser->leftJoin(EmailList::tableName(), 'el_id = upp_email_list_id');
//
//        $union = $queryClient->union($queryUser);
//
//        $query = (new Query())
//            ->select(['id', 'full_name', 'company_name', 'type', 'is_company' , 'phone', 'email', 'description'])
//            ->from($union);
//        if ($q) {
//            $query->andWhere([
//                'OR',
//                ['like', 'full_name', $q],
//                ['like', 'company_name', $q],
//                ['like', 'phone', $q],
//                ['like', 'email', $q],
//            ]);
//        }
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
////            'pagination' => [
////                'pageSize' => 5,
////            ]
//        ]);
//
////        VarDumper::dump($query->createCommand()->getRawSql());die;
//
//        return $dataProvider;
//    }

    public function searchByWidgetContactsSection(?string $q = null): ActiveDataProvider
    {
        $queryClient = Client::find()->alias('c')->select([
            'c.id as id',
            'full_name' => new Expression('if (c.is_company, c.company_name, (concat(IFNULL(c.first_name, \'\'), \' \', IFNULL(c.middle_name, \'\'), \' \', IFNULL(c.last_name, \'\'))))'),
            'c.cl_type_id as type',
            'c.is_company as is_company',
            'c.description as description',
        ]);

        $queryClient->innerJoin(UserContactList::tableName(), 'ucl_client_id = c.id');

        if (!$this->isRoleAdmin()) {
            $queryClient->andWhere(['ucl_user_id' => $this->userId]);
            $queryClient->orWhere(['AND',
                ['!=', 'ucl_user_id', $this->userId],
                ['c.disabled' => $this->isDisabled],
                ['c.is_public' => $this->isPublic],
            ]);
        }

        if ($q) {
            $queryClient->andWhere([
                'OR',
                ['IN', 'c.id', ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $q])],
                ['IN', 'c.id', ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $q])],
                ['like', 'c.first_name', $q],
                ['like', 'c.middle_name', $q],
                ['like', 'c.last_name', $q],
                ['like', 'c.company_name', $q],
            ]);
        }

//        VarDumper::dump($queryClient->createCommand()->getRawSql());die;

        $queryUser = Employee::find()->alias('u')->select([
            'u.id as id',
            'u.full_name as full_name',
            'type' => new Expression(Client::TYPE_INTERNAL),
            'is_company' => new Expression('null'),
            'description' => new Expression('null'),
        ]);

        $queryUser->innerJoin(UserProfile::tableName(), 'up_user_id = u.id and up_show_in_contact_list = 1');

        $queryUser->andWhere(['<>', 'u.id', $this->userId]);

        if ($q) {
            $queryUser->andWhere([
                'OR',
                ['IN', 'u.id', UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])->andWhere('upp_user_id = u.id')->andWhere([
                    'upp_phone_list_id' => PhoneList::find()->select('pl_id')->andWhere(['like', 'pl_phone_number', $q])
                ])],
                ['IN', 'u.id', UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])->andWhere('upp_user_id = u.id')->andWhere([
                    'upp_email_list_id' => EmailList::find()->select('el_id')->andWhere(['like', 'el_email', $q])
                ])],
                ['like', 'u.full_name', $q],
            ]);
        }

//        VarDumper::dump($queryUser->createCommand()->getRawSql());die;

        $union = $queryClient->union($queryUser);

        $query = (new Query())->select(['id', 'full_name', 'type', 'is_company', 'description'])->from($union)->orderBy(['full_name' => SORT_ASC]);

//        VarDumper::dump($query->createCommand()->getRawSql());die;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ]
        ]);

        return $dataProvider;
    }

    public function searchByWidgetCallSection(string $q, ?int $limit = null): array
    {
        $queryClient = Client::find()->alias('c')->select([
            'c.id as id',
            'concat(IFNULL(c.first_name, \'\'), \' \', IFNULL(c.middle_name, \'\'), \' \', IFNULL(c.last_name, \'\')) as full_name',
            'c.company_name as company_name',
            'c.cl_type_id as type',
            'cp.phone as phone',
            'c.is_company as is_company',
            'user_call_phone_status' => new Expression('null'),
            'user_is_on_call' => new Expression('null'),
            'ce.email as email',
        ]);

        $queryClient->innerJoin(UserContactList::tableName(), 'ucl_client_id = c.id');
        $queryClient->innerJoin(ClientPhone::tableName() . ' as cp', 'cp.client_id = c.id');
        $queryClient->leftJoin(ClientEmail::tableName() . ' as ce', 'ce.client_id = c.id');

        if (!$this->isRoleAdmin()) {
            $queryClient->andWhere(['ucl_user_id' => $this->userId]);
            $queryClient->orWhere(['AND',
                ['!=', 'ucl_user_id', $this->userId],
                ['c.disabled' => $this->isDisabled],
                ['c.is_public' => $this->isPublic],
            ]);
        }

        $queryUser = Employee::find()->alias('u')->select([
            'u.id as id',
            'u.full_name as full_name',
            'company_name' => new Expression('null'),
            'type' => new Expression(Client::TYPE_INTERNAL),
            'pl_phone_number as phone',
            'is_company' => new Expression('null'),
            'us_call_phone_status as user_call_phone_status',
            'us_is_on_call as user_is_on_call',
            'el_email as email',
        ]);

        $queryUser->innerJoin(UserProjectParams::tableName(), 'upp_user_id = u.id');
        $queryUser->innerJoin(UserProfile::tableName(), 'up_user_id = u.id and up_show_in_contact_list = 1');
        $queryUser->innerJoin(PhoneList::tableName(), 'pl_id = upp_phone_list_id');
        $queryUser->leftJoin(EmailList::tableName(), 'el_id = upp_email_list_id');
        $queryUser->leftJoin(UserStatus::tableName(), 'us_user_id = u.id');

        $queryUser->andWhere(['<>', 'u.id', $this->userId]);

        $union = $queryClient->union($queryUser);

        $query = (new Query())
            ->select(['id', 'full_name', 'company_name', 'phone', 'type', 'is_company', 'user_call_phone_status', 'user_is_on_call'])
            ->from($union)
            ->andWhere([
                'OR',
                ['like', 'full_name', $q],
                ['like', 'company_name', $q],
                ['like', 'phone', $q],
                ['like', 'email', $q],
            ])
            ->orderBy(['full_name' => SORT_ASC, 'company_name' => SORT_ASC])
            ->groupBy(['id', 'full_name', 'company_name', 'phone', 'type', 'is_company', 'user_call_phone_status', 'user_is_on_call']);

        if ($limit) {
            $query->limit($limit);
        }

//        VarDumper::dump($query->createCommand()->getRawSql());die;

        return $query->all();
    }

    private function isRoleAdmin()
    {
        return ($this->user->isAdmin() || $this->user->isSuperAdmin());
    }
}
