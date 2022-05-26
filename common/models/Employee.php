<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\BackOffice;
use common\models\query\EmployeeQuery;
use common\models\search\EmployeeSearch;
use frontend\models\UserFailedLogin;
use modules\product\src\entities\productType\ProductType;
use src\access\EmployeeGroupAccess;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use src\model\clientChatUserAccess\event\UpdateChatUserAccessWidgetEvent;
use src\model\clientChatUserChannel\entity\ClientChatUserChannel;
use src\model\coupon\entity\couponSend\CouponSend;
use src\model\leadUserRating\entity\LeadUserRating;
use src\model\leadRedial\entity\CallRedialUserAccess;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use src\model\user\entity\Access;
use src\model\user\entity\AccessCache;
use src\model\user\entity\ShiftTime;
use src\model\user\entity\StartTime;
use src\model\user\entity\UserCache;
use src\model\user\entity\UserRelations;
use src\model\user\entity\userStatus\UserStatus;
use src\model\userClientChatData\entity\UserClientChatData;
use src\model\userData\entity\UserData;
use src\model\userData\entity\UserDataKey;
use src\validators\SlugValidator;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\helpers\Json;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
use src\logger\db\LogDTO;
use common\models\GlobalLog;
use src\logger\db\GlobalLogInterface;
use src\services\log\GlobalEntityAttributeFormatServiceService;
use modules\shiftSchedule\src\entities\shift\Shift;

/**
 * This is the model class for table "employees".
 *
 * @property int $id
 * @property string $username
 * @property string $full_name
 * @property string $nickname
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_activity
 * @property boolean $acl_rules_activated
 *
 * @property bool $make_user_project_params
 *
 * @property array $roles
 * @property array $roles_raw
 * @property array $rolesName
 * @property array $form_roles
 * @property array $departmentAccess
 * @property array $projectAccess
 * @property ShiftTime $shiftTime
 *
 * @property Lead[] $leads
 * @property Department[] $departments
 * @property DepartmentPhoneProject[] $departmentPhoneProjects
 * @property UserDepartment[] $userDepartments
 * @property Department[] $udDeps
 * @property EmployeeAcl[] $employeeAcl
 * @property ProjectEmployeeAccess[] $projectEmployeeAccesses
 * @property Project[] $projects
 * @property ClientChatUserChannel[] $clientChatUserChannel
 *
 * @property UserGroupAssign[] $userGroupAssigns
 * @property UserGroup[] $ugsGroups
 * @property UserParams $userParams
 * @property UserProductType $userProductType
 * @property UserFailedLogin $userFailedLogin
 *
 * @property UserProjectParams[] $userProjectParams
 * @property Project[] $uppProjects
 * @property UserProfile $userProfile
 * @property UserClientChatData $userClientChatData
 * @property UserOnline $userOnline
 * @property UserStatus $userStatus
 * @property CouponSend[] $couponSend
 * @property LeadUserRating[] $leadUserRatings
 * @property UserShiftAssign[] $userShiftAssigns
 * @property Shift[] $shifts
 *
 * @property string|bool|null $timezone
 * @property bool $isAllowCallExpert
 * @property int $callExpertCountByShiftTime
 * @property int $callExpertCount
 *
 * @property array $cache
 * @property Access|null $access
 *
 * @property ActiveQuery $productType
 *
 * @property UserRelations|null $userRelations
 */
class Employee extends \yii\db\ActiveRecord implements IdentityInterface
{
    public const ROLE_SUPER_ADMIN = 'superadmin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_AGENT = 'agent';
    public const ROLE_SUPERVISION = 'supervision';
    public const ROLE_QA = 'qa';
    public const ROLE_QA_SUPER = 'qa_super';
    public const ROLE_USER_MANAGER = 'userManager';
    public const ROLE_SUP_AGENT = 'sup_agent';
    public const ROLE_SUP_SUPER = 'sup_super';
    public const ROLE_EX_AGENT = 'ex_agent';
    public const ROLE_EX_SUPER = 'ex_super';
    /** admin child role  */
    public const ROLE_SALES_SENIOR = 'sales_senior';
    public const ROLE_EXCHANGE_SENIOR = 'exchange_senior';
    public const ROLE_SUPPORT_SENIOR = 'support_senior';

    public const SCENARIO_REGISTER = 'register';

    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 10;
    public const STATUS_BLOCKED = 20;

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BLOCKED => 'Blocked',
        self::STATUS_DELETED => 'Deleted',
    ];

    public const PROFIT_BONUSES = [
        11000 => 500,
        8000 => 300,
        5000 => 150
    ];

    private const CALL_EXPERT_SHIFT_MINUTES = 12 * 60;

    private const LEVEL_PERMISSION_IS_AGENT = 'isAgent';

    public $password;
    public $deleted;
    public $make_user_project_params;

    public $roles = null;
    public $roles_raw = null;
    public $rolesName = null;
    public $form_roles = [];

    public $viewItemsEmployeeAccess;

    public $user_groups;
    public $user_projects;
    public $user_departments;
    public $client_chat_user_channel;
    public $user_shift_assigns;

    private $shiftTime;
    public $currentShiftTaskInfoSummary = [];

    private $cache = [];

    private $_timezone;
    private $_isAllowCallExpert;
    private $_callExpertCountByShiftTime;
    private $_callExpertCount;

    private $departmentAccess = [];
    private $projectAccess = [];

    private $access;
    private $permissionList = [];

    private ?UserRelations $userRelations = null;

    public function loadCache(UserCache $cache): void
    {
        $this->cache = $cache->getData();
    }

    /**
     * @return Access
     */
    public function getAccess(): Access
    {
        if ($this->access !== null) {
            return $this->access;
        }
        $this->access = new Access($this, new AccessCache($this->cache));
        return $this->access;
    }

    /**
     * @param string $hash
     * @return array|null
     */
    public function getProjectAccess(string $hash): ?array
    {
        if (isset($this->projectAccess['data']) && !empty($this->projectAccess['hash']) && $hash === $this->projectAccess['hash']) {
            return $this->projectAccess['data'];
        }
        return null;
    }

    /**
     * @param array $data
     * @param string $hash
     */
    public function setProjectAccess(array $data, string $hash): void
    {
        $this->projectAccess['data'] = $data;
        $this->projectAccess['hash'] = $hash;
    }

    /**
     * @param string $hash
     * @return array|null
     */
    public function getDepartmentAccess(string $hash): ?array
    {
        if (isset($this->departmentAccess['data']) && !empty($this->departmentAccess['hash']) && $hash === $this->departmentAccess['hash']) {
            return $this->departmentAccess['data'];
        }
        return null;
    }

    /**
     * @param array $data
     * @param string $hash
     */
    public function setDepartmentAccess(array $data, string $hash): void
    {
        $this->departmentAccess['data'] = $data;
        $this->departmentAccess['hash'] = $hash;
    }

    /**
     * @return bool
     */
    public function isExSuper(): bool
    {
        return in_array(self::ROLE_EX_SUPER, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isExAgent(): bool
    {
        return in_array(self::ROLE_EX_AGENT, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isSupSuper(): bool
    {
        return in_array(self::ROLE_SUP_SUPER, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isSupAgent(): bool
    {
        return in_array(self::ROLE_SUP_AGENT, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return in_array(self::ROLE_SUPER_ADMIN, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return
            in_array(self::ROLE_ADMIN, $this->getRoles(true), true)
            || in_array(self::ROLE_SALES_SENIOR, $this->getRoles(true), true)
            || in_array(self::ROLE_EXCHANGE_SENIOR, $this->getRoles(true), true)
            || in_array(self::ROLE_SUPPORT_SENIOR, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isOnlyAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isAgent(): bool
    {
        if (isset($this->permissionList[self::LEVEL_PERMISSION_IS_AGENT])) {
            return $this->permissionList[self::LEVEL_PERMISSION_IS_AGENT];
        }
        $this->permissionList[self::LEVEL_PERMISSION_IS_AGENT] = Yii::$app->authManager->checkAccess($this->id, self::LEVEL_PERMISSION_IS_AGENT);
        return $this->permissionList[self::LEVEL_PERMISSION_IS_AGENT];
//        return in_array(self::ROLE_AGENT, $this->getRoles(true), true);
    }

    public function isSimpleAgent(): bool
    {
        return !($this->isSuperAdmin() || $this->isAdmin() || $this->isSupSuper() || $this->isExSuper());
    }

    /**
     * @return bool
     */
    public function isAnyAgent(): bool
    {
        return $this->isAgent() || $this->isExAgent() || $this->isSupAgent();
    }

    /**
     * @return bool
     */
    public function isSupervision(): bool
    {
        return in_array(self::ROLE_SUPERVISION, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isAnySupervision(): bool
    {
        return $this->isSupervision() || $this->isExSuper() || $this->isSupSuper() || $this->isQaSuper();
    }

    public function isQaSuper(): bool
    {
        return in_array(self::ROLE_QA_SUPER, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isQa(): bool
    {
        return in_array(self::ROLE_QA, $this->getRoles(true), true);
    }

    /**
     * @return bool
     */
    public function isUserManager(): bool
    {
        return in_array(self::ROLE_USER_MANAGER, $this->getRoles(true), true);
    }

    public function isAnySenior(): bool
    {
        return
            in_array(self::ROLE_SALES_SENIOR, $this->getRoles(true), true)
            || in_array(self::ROLE_EXCHANGE_SENIOR, $this->getRoles(true), true)
            || in_array(self::ROLE_SUPPORT_SENIOR, $this->getRoles(true), true);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getCache(string $key)
    {
        return $this->cache[$key] ?? null;
    }

    /**
     * @param string $key
     * @param $data
     * @return mixed
     */
    public function setCache(string $key, $data)
    {
        $this->cache[$key] = $data;
        return $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'form_roles', 'full_name', 'nickname'], 'required'],
            [['password'], 'required', 'on' => self::SCENARIO_REGISTER],
            [['email', 'password', 'username', 'full_name', 'nickname'], 'trim'],
            [['password'], 'string', 'min' => 8],
            [['status'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            [['status'], 'integer'],
            [['password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['username', 'full_name', 'nickname'], 'string', 'min' => 3, 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
            ['email', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true],
            [['username'], 'match' ,'pattern' => '/^[a-z0-9_\-\.]+$/i', 'message' => 'Username can contain only characters ("a-z", "0-9", "_", "-", ".")'],
            [['make_user_project_params'], 'boolean'],
            [['password_reset_token'], 'unique'],
            [
                [
                    'created_at', 'updated_at', 'last_activity', 'user_groups',
                    'user_projects', 'deleted', 'user_departments', 'client_chat_user_channel', 'user_shift_assigns',
                ],
                'safe',
            ],

            ['acl_rules_activated', 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_groups' => 'User groups',
            'user_projects' => 'Projects access',
            'form_roles' => 'Roles',
            'user_departments' => 'Departments',
            'make_user_project_params' => 'Make user project params (automatic)',
            'full_name' => 'Full Name',
            'client_chat_user_channel' => 'Client chat user channel',
            'user_shift_assigns' => 'User Shift Assign',
        ];
    }


    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Gets query for [[UserOnline]].
     *
     * @return ActiveQuery
     */
    public function getUserOnline(): ActiveQuery
    {
        return $this->hasOne(UserOnline::class, ['uo_user_id' => 'id']);
    }

    /**
     * Gets query for [[UserStatus]].
     *
     * @return ActiveQuery
     */
    public function getUserStatus(): ActiveQuery
    {
        return $this->hasOne(UserStatus::class, ['us_user_id' => 'id']);
    }

    public function getCouponSend(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CouponSend::class, ['cus_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(Department::class, ['dep_updated_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDepartmentPhoneProjects()
    {
        return $this->hasMany(DepartmentPhoneProject::class, ['dpp_updated_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserDepartments()
    {
        return $this->hasMany(UserDepartment::class, ['ud_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getClientChatUserChannel()
    {
        return $this->hasMany(ClientChatUserChannel::class, ['ccuc_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getClientChatChannel()
    {
        return $this->hasMany(ClientChatChannel::class, ['ccc_id' => 'ccuc_channel_id'])->viaTable('client_chat_user_channel', ['ccuc_user_id' => 'id']);
    }

    public function getClientChatUserChannelList(): array
    {
        if ($clientChatChannel = $this->clientChatChannel) {
            return \yii\helpers\ArrayHelper::map($clientChatChannel, 'ccc_id', 'ccc_name');
        }

        return [];
    }

    /**
     * @return ActiveQuery
     */
    public function getUdDeps()
    {
        return $this->hasMany(Department::class, ['dep_id' => 'ud_dep_id'])->viaTable('user_department', ['ud_user_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return $this
     */
    public function setActive(): Employee
    {
        $this->status = self::STATUS_ACTIVE;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * @return $this
     */
    public function setBlocked(): Employee
    {
        $this->status = self::STATUS_BLOCKED;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getTimezone()
    {
        if ($this->_timezone === null) {
            $params = $this->userParams;
            if ($params && $params->up_timezone) {
                $this->_timezone = $params->up_timezone;
            } else {
                $this->_timezone = false;
            }
        }
        return $this->_timezone;
    }

    /**
     * @return bool
     */
    public function getIsAllowCallExpert(): bool
    {
        if ($this->_isAllowCallExpert === null) {
            $this->_isAllowCallExpert = false;
            $params = $this->userParams;
            if ($params && (int) $params->up_call_expert_limit >= 0) {
                $this->_isAllowCallExpert = true;
            }
        }
        return $this->_isAllowCallExpert;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCallExpertCountByShiftTime(): int
    {
        if ($this->_callExpertCountByShiftTime === null) {
            //$this->_callExpertCountByShiftTime = 0;

            $shiftTime = $this->getShiftTime();

            if ($shiftTime->startUtcDt) {
                $startShiftDateTime = date('Y-m-d', strtotime($shiftTime->startUtcDt) - (3 * 60 * 60));
            } else {
                $startShiftDateTime = date('Y-m-d', strtotime('-3 hours'));
            }


            $this->_callExpertCountByShiftTime = LeadCallExpert::find()->where(['lce_agent_user_id' => $this->id])->andWhere(['>=', 'lce_request_dt', $startShiftDateTime])->count();
        }
        return $this->_callExpertCountByShiftTime;
    }

    /**
     * @param int $minutes
     * @return int
     */
    public function getCallExpertCount(int $minutes = self::CALL_EXPERT_SHIFT_MINUTES): int
    {
        if ($this->_callExpertCount === null) {
            $startShiftDateTime = date('Y-m-d H:i:s', strtotime('-' . $minutes . ' minutes'));

            $this->_callExpertCount = LeadCallExpert::find()->where(['lce_agent_user_id' => $this->id])->andWhere(['>=', 'lce_request_dt', $startShiftDateTime])->count();
        }
        return $this->_callExpertCount;
    }

    /**
     * @return bool
     */
    public function isEnableCallExpert(): bool
    {
        $params = $this->userParams;

        if ($params) {
            if ((int)$params->up_call_expert_limit === 0) {
                return true;
            }

            if ((int)$params->up_call_expert_limit > 0 && $this->callExpertCount >= $params->up_call_expert_limit) {
                return false;
            }
        }

        return true;
    }






    public function afterFind()
    {
        parent::afterFind();

        //var_dump(\webapi\models\ApiUser::class); die;

        /*if (isset(Yii::$app->user)) {
            if (Yii::$app->user && Yii::$app->user->identityClass === \webapi\models\ApiUser::class) {
                $this->roles = [];
            } else {
                $roles = $this->getRoles();
                $this->roles = array_keys($roles);
            }
        }*/
    }


    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->status === self::STATUS_DELETED;
    }


    /**
     * @param string $role
     * @return bool
     */
    public function canRole(string $role = ''): bool
    {
        if ($this->roles === null) {
            $roles = $this->getRoles();
            $this->roles = array_keys($roles);
        }

        return in_array($role, $this->roles, false);
    }

    /**
     * @param array $roles
     * @return bool
     */
    public function canRoles(array $roles = []): bool
    {
        foreach ($roles as $role) {
            if ($this->canRole($role)) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param string $url
     * @return bool
     */
    public function canRoute(string $url = ''): bool
    {
        if ($url && $url[0] !== '/') {
            $url = '/' . $url;
        }
        if (Yii::$app->user->can('/*')) {
            //for superAdmin or Dashboard
            return true;
        } elseif (Yii::$app->user->can($url)) {
            return true;
        } else {
            $chunks = explode('/', $url);

            if (isset($chunks[0]) && $chunks[0]) {
                $url2 = '/' . $chunks[0] . '/*';
                if (Yii::$app->user->can($url2)) {
                    return true;
                }
            }
        }
        return false;
    }

    /*public function afterValidate()
    {
        parent::afterValidate();

        $this->updated_at = date('Y-m-d H:i:s');
    }*/

    /**
     * @return ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['up_user_id' => 'id']);
    }

    public function getUserClientChatData(): ActiveQuery
    {
        return $this->hasOne(UserClientChatData::class, ['uccd_employee_id' => 'id']);
    }

    public function canCall()
    {
        return ($this->userProfile && $this->userProfile->canCall());
    }

    public function isKpiEnable()
    {
        return ($this->userProfile && $this->userProfile->isKpiEnable());
    }

    /**
     * @return ActiveQuery
     */
    public function getEmployeeAcl()
    {
        return $this->hasMany(EmployeeAcl::class, ['employee_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectEmployeeAccesses()
    {
        return $this->hasMany(ProjectEmployeeAccess::class, ['employee_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getUserProjectParams()
    {
        return $this->hasMany(UserProjectParams::class, ['upp_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUppProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'upp_project_id'])->viaTable('user_project_params', ['upp_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadUserRatings()
    {
        return $this->hasMany(LeadUserRating::class, ['lur_user_id' => 'id']);
    }

    public function getUserShiftAssigns(): ActiveQuery
    {
        return $this->hasMany(UserShiftAssign::class, ['usa_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getShifts()
    {
        return $this->hasMany(Shift::class, ['sh_id' => 'usa_sh_id'])->viaTable('user_shift_assign', ['usa_user_id' => 'id']);
    }

    /**
    * @return array
    */
    public function getUserShiftAssignList(): array
    {
        if ($shifts = $this->shifts) {
            return \yii\helpers\ArrayHelper::map($shifts, 'sh_id', 'sh_name');
        }

        return [];
    }

    /**
     * @return ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'project_id'])->viaTable('project_employee_access', ['employee_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getUserProjectList(): array
    {
        if ($projects = $this->projects) {
            return \yii\helpers\ArrayHelper::map($projects, 'id', 'name');
        }

        return [];
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProductType(): ActiveQuery
    {
        return $this->hasMany(ProductType::class, ['pt_id' => 'upt_product_type_id'])
            ->viaTable(UserProductType::tableName(), ['upt_user_id' => 'id'], static function ($query) {
                /* @var ActiveQuery $query */
                $query->andWhere(['upt_product_enabled' => true]);
            });
    }

    /**
     * @return ActiveQuery
     */
    public function getUserProductType()
    {
        return $this->hasMany(UserProductType::class, ['upt_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserFailedLogin()
    {
        return $this->hasMany(UserFailedLogin::class, ['ufl_user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @param int $status
     * @return static|null
     */
    public static function findByUsername(string $username, int $status = self::STATUS_ACTIVE): ?Employee
    {
        return static::findOne(['username' => $username, 'status' => $status]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }


    /**
     * @return ActiveQuery
     */
    public function getUserParams()
    {
        return $this->hasOne(UserParams::class, ['up_user_id' => 'id']);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @return array
     */
    public static function getAllEmployees(): array
    {
        return ArrayHelper::map(self::find()->select(['id', 'username'])->where(['status' => [self::STATUS_ACTIVE, self::STATUS_BLOCKED]])->asArray()->all(), 'id', 'username');
    }

    /**
     * @param Employee $user
     * @return array
     */
    public static function getAllRoles(Employee $user): array
    {
        $auth = \Yii::$app->authManager;

        /*$query = new Query();
        $result = $query->select(['name', 'description'])
            ->from('auth_item')->where(['type' => 1])
            ->all();*/

        $result = $auth->getRoles();
        $roles = ArrayHelper::map($result, 'name', 'description');

        if ($user->isUserManager()) {
            if (isset($roles[self::ROLE_ADMIN])) {
                unset($roles[self::ROLE_ADMIN]);
            }
            if (isset($roles[self::ROLE_SUPER_ADMIN])) {
                unset($roles[self::ROLE_SUPER_ADMIN]);
            }
            return $roles;
        }

        if ((!$user->isAdmin() && !$user->isSuperAdmin()) || $user->isAnySenior()) {
            if (isset($roles[self::ROLE_ADMIN])) {
                unset($roles[self::ROLE_ADMIN]);
            }
            if (isset($roles[self::ROLE_SALES_SENIOR])) {
                unset($roles[self::ROLE_SALES_SENIOR]);
            }
            if (isset($roles[self::ROLE_EXCHANGE_SENIOR])) {
                unset($roles[self::ROLE_EXCHANGE_SENIOR]);
            }
            if (isset($roles[self::ROLE_SUPPORT_SENIOR])) {
                unset($roles[self::ROLE_SUPPORT_SENIOR]);
            }
        }

        if (!$user->isAdmin() && !$user->isSuperAdmin() && !$user->isUserManager()) {
            if (isset($roles[self::ROLE_QA])) {
                unset($roles[self::ROLE_QA]);
            }
        }

        if (!$user->isSuperAdmin()) {
            if (isset($roles[self::ROLE_SUPER_ADMIN])) {
                unset($roles[self::ROLE_SUPER_ADMIN]);
            }
        }

        //VarDumper::dump($roles, 10, true);

        return $roles;
    }


    /**
     * @return ActiveQuery
     */
    public function getLeads()
    {
        return $this->hasMany(Lead::class, ['employee_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param $attr
     * @return bool
     */
    public function prepareSave($attr)
    {
        $this->username = $attr['username'];
        $this->email = $attr['email'];
        $this->full_name = $attr['full_name'];

        $this->password = $attr['password'];
        if (!empty($attr['password'])) {
            $this->setPassword($attr['password']);
        }

        /*if (isset($attr['deleted'])) {
            $this->status = empty($attr['deleted'])
                ? self::STATUS_ACTIVE : self::STATUS_DELETED;
        }*/

        if (isset($attr['status']) && is_numeric($attr['status'])) {
            $this->status = (int) $attr['status'];
        }

        if (isset($attr['acl_rules_activated'])) {
            $this->acl_rules_activated = $attr['acl_rules_activated'];
        }

//        if (!empty($attr['form_roles'])) {
//            foreach ($attr['form_roles'] as $role) {
//                $this->form_roles[] = $role;
//            }
//        }

        if (!$this->auth_key) {
            $this->generateAuthKey();
        }

        return $this->isNewRecord;
    }


    /**
     * @param bool $isNew
     * @throws \Exception
     */
    public function addRole(bool $isNew = false): void
    {
        $auth = \Yii::$app->authManager;

        if (!$isNew) {
            $auth->revokeAll($this->id);
        }

        if ($this->form_roles) {
            foreach ($this->form_roles as $role) {
                $authorRole = $auth->getRole($role);
                $auth->assign($authorRole, $this->id);
            }
        }
    }

    public function updateRoles(array $roles): void
    {
        $auth = \Yii::$app->authManager;

        $auth->revokeAll($this->id);

        foreach ($roles as $role) {
            $authorRole = $auth->getRole($role);
            $auth->assign($authorRole, $this->id);
        }
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getFirstUserProjectParam(): ActiveQuery
    {
        return $this->hasOne(UserProjectParams::class, ['upp_user_id' => 'id']);
    }

    /**
     * @param bool $onlyNames
     * @return array
     */
    public function getRoles($onlyNames = false): array
    {
        if ($this->rolesName === null) {
            //todo
            $query = (new Query())->select('b.*')
                ->from(['a' => 'auth_assignment', 'b' => 'auth_item'])
                ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
                ->andWhere(['a.user_id' => (string) $this->id])
                ->andWhere(['b.type' => 1]);
            $this->rolesName = ArrayHelper::map($query->all(), 'name', 'description');
//            $this->rolesName = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($this->id), 'name', 'description');
        }
        if ($onlyNames) {
            return array_keys($this->rolesName);
        }
        return $this->rolesName;
    }

    public function roleUpdate($uID, $role)
    {
        Yii::$app->db->createCommand()->update('auth_assignment', ['item_name' => $role], "user_id = $uID")->execute();
    }

    public function removeAllRoles()
    {
        Yii::$app->db->createCommand()->delete('auth_assignment', 'user_id = :user_id', [':user_id' => $this->id])->execute();
    }

    public function removeRoles(array $roles)
    {
        foreach ($roles as $role) {
            Yii::$app->db->createCommand()->delete('auth_assignment', 'user_id = :user_id AND item_name = :item_name', [':user_id' => $this->id, ':item_name' => $role])->execute();
        }
    }

    public function addNewRoles(array $roles)
    {
        $data = [];
        foreach ($roles as $role) {
            $data[] = [
                'item_name' => $role,
                'user_id' => $this->id,
                'created_at' => strtotime(date('Y-m-d H:i:s'))
            ];
        }
        Yii::$app->db->createCommand()->batchInsert('auth_assignment', ['item_name', 'user_id', 'created_at'], $data)->execute();
    }

    public function removeAllDepartments()
    {
        Yii::$app->db->createCommand()->delete(UserDepartment::tableName(), 'ud_user_id = :ud_user_id', [':ud_user_id' => $this->id])->execute();
    }

    public function removeDepartments(array $departments)
    {
        UserDepartment::deleteAll([
            'ud_user_id' => $this->id,
            'ud_dep_id' => $departments,
        ]);
    }

    public function addNewDepartments(array $departments)
    {
        $data = [];
        foreach ($departments as $department) {
            $data[] = [
                'ud_user_id' => $this->id,
                'ud_dep_id' => (int)$department,
                'ud_updated_dt' => date('Y-m-d H:i:s')
            ];
        }
        Yii::$app->db->createCommand()->batchInsert(UserDepartment::tableName(), ['ud_user_id', 'ud_dep_id', 'ud_updated_dt'], $data)->execute();
    }

    public function removeAllClientChatChanels()
    {
        Yii::$app->db->createCommand()->delete(ClientChatUserChannel::tableName(), 'ccuc_user_id = :ccuc_user_id', [':ccuc_user_id' => $this->id])->execute();
    }

    public function addClientChatChanels(array $channels, ?int $createdUserId = null)
    {
        $data = [];
        foreach ($channels as $channel) {
            $data[] = [
                'ccuc_user_id' => $this->id,
                'ccuc_channel_id' => (int)$channel,
                'ccuc_created_dt' => date('Y-m-d H:i:s'),
                'ccuc_created_user_id' => $createdUserId,
            ];
        }
        Yii::$app->db->createCommand()->batchInsert(ClientChatUserChannel::tableName(), ['ccuc_user_id', 'ccuc_channel_id', 'ccuc_created_dt', 'ccuc_created_user_id'], $data)->execute();
    }

    public function getRolesRaw()
    {
        if (!$this->id) {
            return [];
        }
        if (null !== $this->roles_raw) {
            return $this->roles_raw;
        }
        $items = [];
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand("SELECT item_name FROM auth_assignment WHERE user_id = " . $this->id);
        $items = $command->queryAll();
        if (count($items)) {
            return ArrayHelper::map($items, 'item_name', 'item_name');
        }
        return $items;
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        return self::find()->select(['username', 'id'])->orderBy(['username' => SORT_ASC])->indexBy('id')->cache(600)->asArray()->column();
    }

    /**
     * @return array
     */
    public static function getListByRole($role = self::ROLE_AGENT): array
    {
        $data = self::find()->leftJoin('auth_assignment', 'auth_assignment.user_id = id')->andWhere(['auth_assignment.item_name' => $role])->orderBy(['username' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'username');
    }

    /**
     * @param array $role
     * @return array
     */
    public static function getListSplitProfitByRole(array $role = [self::ROLE_AGENT]): array
    {
        $data = self::find()->leftJoin('auth_assignment', 'auth_assignment.user_id = id')->andWhere(['in', 'auth_assignment.item_name', $role])->orderBy(['username' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'username');
    }

    /**
     * @param int $userId
     * @return array
     */
    public static function getListByUserId(int $userId): array
    {
        return self::find()->select(['username', 'id'])
            ->andWhere(['id' => EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId)])
            ->orderBy(['username' => SORT_ASC])->asArray()->indexBy('id')->column();
    }

    /**
     * @return array
     */
    public static function getActiveUsersList(): array
    {
        return self::find()->select(['username', 'id', 'status'])
            ->active()
            ->orderBy(['username' => SORT_ASC])
            ->indexBy('id')->asArray()->column();
    }

    /**
     * @return array
     *
     *  [
     *      '1' => 'UserName (Role1, Role2)'
     *      '34' => 'UserName2 (Role2)'
     *  ]
     */
    public static function getActiveUsersListWithRoles(): array
    {
        $q = (new Query())->select(['u.username', 'u.id', 'u.status', 'a.item_name', 'i.name', 'i.description'])
            ->andWhere(['u.status' => self::STATUS_ACTIVE])
            ->from('{{%auth_assignment}} a')
            ->leftJoin('{{%employees}} u', 'a.user_id = u.id')
            ->leftJoin('{{%auth_item}} i', 'a.item_name = i.name')
            ->orderBy(['u.username' => SORT_ASC])
            ->all();
        $list = [];
        foreach ($q as $item) {
            if (isset($list[$item['id']])) {
                $list[$item['id']] .=  ', ' . $item['description'];
            } else {
                $list[$item['id']] = $item['username'] . ' (' . $item['description'];
            }
        }
        foreach ($list as $k => $item) {
            $list[$k] .= ')';
        }
        return $list;
    }

    /**
     * @param int $userId
     * @return array
     */
    public static function getActiveUsersListFromCommonGroups(int $userId): array
    {
        return self::find()->select(['username', 'id', 'status'])->active()
            ->andWhere(['id' => EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId)])
            ->orderBy(['username' => SORT_ASC])->asArray()->indexBy('id')->column();
    }

    /**
     * @return array
     */
    public static function getListByProject($projectId, $withExperts = false): array
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        if ($user->isAdmin() || $user->isSupervision()) {
            $data = self::find()
                ->orderBy(['username' => SORT_ASC])
                ->asArray()->all();
        } else {
            $data = self::find()
                ->joinWith('projectEmployeeAccesses')
                ->where(['=', 'project_id', $projectId])
                ->orderBy(['username' => SORT_ASC])
                ->asArray()->all();
        }

        if ($withExperts) {
            $experts = Yii::$app->cache->get(sprintf('list-of-experts-from-BO'));
            if ($experts === false) {
                $result = BackOffice::sendRequest('default/experts');
                if (!empty($result)) {
                    $experts = $result;
                    Yii::$app->cache->set(sprintf('list-of-experts-from-BO'), $experts, 21600);
                }
            }
            if (is_array($experts)) {
                $employeesGroup = [
                    'Sales' => ArrayHelper::map($data, 'id', 'username'),
                    'Experts' => $experts
                ];
                $options = [];
                foreach ($employeesGroup as $type => $group) {
                    $child_options = [];
                    foreach ($group as $id => $employee) {
                        $employeeId = ($type != 'Experts')
                            ? $id
                            : $employee;

                        //if(is_int($employeeId)){
                        $child_options[$employeeId] = $employee;
                        //}
                    }
                    $options[$type] = $child_options;
                }
                return $options;
            }
        }

        return ArrayHelper::map($data, 'id', 'username');
    }


    /**
     * @return ActiveQuery
     */
    public function getUserGroupAssigns()
    {
        return $this->hasMany(UserGroupAssign::class, ['ugs_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUgsGroups()
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'ugs_group_id'])
            ->viaTable('user_group_assign', ['ugs_user_id' => 'id'])
            ->orderBy(['ug_name' => SORT_ASC]);
    }

    public function isUserGroupIntersection(array $groups): bool
    {
        foreach ($this->getUserGroupList() as $groupId => $groupName) {
            if (in_array($groupId, $groups, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getUserGroupList(): array
    {
        if ($groupsModel = $this->ugsGroups) {
            return \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');
        }

        return [];
    }

    /**
     * @return array
     */
    public function getUserDepartmentList(): array
    {
        if ($model = $this->udDeps) {
            return \yii\helpers\ArrayHelper::map($model, 'dep_id', 'dep_name');
        }

        return [];
    }


    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function getCurrentShiftTaskInfoSummary()
    {
        if ($this->currentShiftTaskInfoSummary) {
            return $this->currentShiftTaskInfoSummary;
        }

        $shiftTime = $this->getShiftTime();

        $stats = [];
        if ($shiftTime->startUtcDt) {
            $startShiftDate = date('Y-m-d', strtotime($shiftTime->startUtcDt));
        } else {
            $startShiftDate = date('Y-m-d');
        }

        //VarDumper::dump($startShiftDate, 10, true); exit;

        $taskListAllQuery = \common\models\LeadTask::find()
            ->where(['lt_user_id' => $this->id])
            ->andWhere(['=', 'lt_date', $startShiftDate]);

        $taskListCheckedQuery = \common\models\LeadTask::find()
            ->where(['lt_user_id' => $this->id])
            ->andWhere(['IS NOT', 'lt_completed_dt', null])
            ->andWhere(['=', 'lt_date', $startShiftDate]);


        $taskListAllQuery->joinWith(['ltLead' => function ($q) {
//            $q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
            $q->where(['leads.status' => Lead::STATUS_PROCESSING]);
        }]);

        $taskListCheckedQuery->joinWith(['ltLead' => function ($q) {
//            $q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
            $q->where(['leads.status' => Lead::STATUS_PROCESSING]);
        }]);


        $completedTasksCount = (int)$taskListCheckedQuery->count();
        $allTasksCount = (int)$taskListAllQuery->count();


        $stats['completedTasksCount'] = $completedTasksCount;
        $stats['allTasksCount'] = $allTasksCount;


        if ($allTasksCount > 0) {
            $completedTasksPercent = round($completedTasksCount * 100 / $allTasksCount);
        } else {
            $completedTasksPercent = 0;
        }

        $stats['completedTasksPercent'] = $completedTasksPercent;
        $this->currentShiftTaskInfoSummary = $stats;
        return $stats;
    }

    /**
     * @param array $flowDescriptions
     * @param array $fromStatuses
     * @return string
     */
    public function getLastTakenLeadDt(array $flowDescriptions = [], array $fromStatuses = []): string
    {
        if ($leadFlow = LeadFlow::find()->lastTakenByUserId($this->id, $flowDescriptions, $fromStatuses)->one()) {
            return $leadFlow['created'];
        }
        return '';
    }

    /**
     * @param string|null $start_dt
     * @param string|null $end_dt
     * @return string
     */
    public function getTaskStats(string $start_dt = null, string $end_dt = null): string
    {
        if ($start_dt) {
            $start_dt = date('Y-m-d', strtotime($start_dt));
        } else {
            $start_dt = null;
        }

        if ($end_dt) {
            $end_dt = date('Y-m-d', strtotime($end_dt));
        } else {
            $end_dt = null;
        }

        $taskListAllQuery = \common\models\LeadTask::find()->with('ltTask')->select(['COUNT(*) AS field_cnt', 'lt_task_id'])
            ->where(['lt_user_id' => $this->id])
            ->andFilterWhere(['>=', 'lt_date', $start_dt])
            ->andFilterWhere(['<=', 'lt_date', $end_dt])
            ->groupBy(['lt_task_id'])
            ->orderBy(['lt_task_id' => SORT_ASC]);

        $taskListCheckedQuery = \common\models\LeadTask::find()->with('ltTask')->select(['COUNT(*) AS field_cnt', 'lt_task_id'])
            ->where(['lt_user_id' => $this->id])
            ->andWhere(['IS NOT', 'lt_completed_dt', null])
            ->andFilterWhere(['>=', 'lt_date', $start_dt])
            ->andFilterWhere(['<=', 'lt_date', $end_dt])
            ->groupBy(['lt_task_id']);

        $taskListAllQuery->joinWith(['ltLead' => function ($q) {
            //$q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
            $q->where(['leads.status' => Lead::STATUS_PROCESSING]);
        }]);

        $taskListCheckedQuery->joinWith(['ltLead' => function ($q) {
            //$q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
            $q->where(['leads.status' => Lead::STATUS_PROCESSING]);
        }]);

        $taskListAll = $taskListAllQuery->all();
        $taskListChecked = $taskListCheckedQuery->all();

        //return $taskListChecked->createCommand()->getRawSql();

        $completed = [];
        if ($taskListChecked) {
            foreach ($taskListChecked as $taskItem) {
                $completed[$taskItem->lt_task_id] = $taskItem->field_cnt;
            }
        }

        //$itemHeader = [];
        $item = [];


        if ($taskListAll) {
            foreach ($taskListAll as $task) {
                //$itemHeader[] = Html::encode($task->ltTask->t_name);
                //$item[] = ''.($completed[$task->lt_task_id] ?? 0).'/'. $task->field_cnt.'';

                $completedTasks = $completed[$task->lt_task_id] ?? 0;

                $str = '<tr><td><small>' . Html::encode($task->ltTask->t_name) . '</small></td><td><small>' . $completedTasks . ' / ' . Html::a(
                    $task->field_cnt,
                    ['lead-task/index', 'LeadTaskSearch[lt_task_id]' => $task->lt_task_id, 'LeadTaskSearch[lt_user_id]' => $this->id],
                    ['data-pjax' => 0, 'target' => '_blank']
                ) . '</small></td>';


                if ($task->field_cnt > 0) {
                    $percent = (int)($completedTasks * 100 / $task->field_cnt);
                } else {
                    $percent = 0;
                }

                $str .= '<td width="100"><div class="progress" style="margin-bottom: 0" title="' . $percent . '%">
                          <div class="progress-bar" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $percent . '%;">
                            ' . $percent . '%
                          </div>
                        </div></td></tr>';


                $item[] = $str;
            }
        }


        if ($item) {
            $str = '<table class="table table-bordered table-condensed">';
            //$str .= '<tr><th class="text-center">'.implode('</th><th class="text-center">', $itemHeader).'</th></tr>';

            //$str .= '<tr>';
            //$str .= '<td class="text-center">' . implode('</td><td class="text-center">', $item) . '</td>';
            //$str .= '</tr>';
            $str .= implode('', $item);
            $str .= '</table>';
        } else {
            $str = '-';
        }

        return $str;
    }

    /**
     * @param string|null $start_dt
     * @param string|null $end_dt
     * @param int $userID
     * @return string
     */
    public static function getTaskStatsSupervision(string $start_dt = null, string $end_dt = null, int $userID): string
    {
        if ($start_dt) {
            $start_dt = date('Y-m-d', strtotime($start_dt));
        } else {
            $start_dt = null;
        }

        if ($end_dt) {
            $end_dt = date('Y-m-d', strtotime($end_dt));
        } else {
            $end_dt = null;
        }

        $taskListAllQuery = \common\models\LeadTask::find()->with('ltTask')->select(['COUNT(*) AS field_cnt', 'lt_task_id'])
            ->where(['lt_user_id' => $userID])
            ->andFilterWhere(['>=', 'lt_date', $start_dt])
            ->andFilterWhere(['<=', 'lt_date', $end_dt])
            ->groupBy(['lt_task_id'])
            ->orderBy(['lt_task_id' => SORT_ASC]);

        $taskListCheckedQuery = \common\models\LeadTask::find()->with('ltTask')->select(['COUNT(*) AS field_cnt', 'lt_task_id'])
            ->where(['lt_user_id' => $userID])
            ->andWhere(['IS NOT', 'lt_completed_dt', null])
            ->andFilterWhere(['>=', 'lt_date', $start_dt])
            ->andFilterWhere(['<=', 'lt_date', $end_dt])
            ->groupBy(['lt_task_id']);

        $taskListAllQuery->joinWith(['ltLead' => function ($q) {
            //$q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
            $q->where(['leads.status' => Lead::STATUS_PROCESSING]);
        }]);

        $taskListCheckedQuery->joinWith(['ltLead' => function ($q) {
            //$q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
            $q->where(['leads.status' => Lead::STATUS_PROCESSING]);
        }]);

        $taskListAll = $taskListAllQuery->all();
        $taskListChecked = $taskListCheckedQuery->all();

        $completed = [];
        if ($taskListChecked) {
            foreach ($taskListChecked as $taskItem) {
                $completed[$taskItem->lt_task_id] = $taskItem->field_cnt;
            }
        }
        $item = [];

        if ($taskListAll) {
            foreach ($taskListAll as $task) {
                $completedTasks = $completed[$task->lt_task_id] ?? 0;

                $str = '<tr><td><small>' . Html::encode($task->ltTask->t_name) . '</small></td><td><small>' . $completedTasks . ' / ' . Html::a(
                    $task->field_cnt,
                    ['lead-task/index', 'LeadTaskSearch[lt_task_id]' => $task->lt_task_id, 'LeadTaskSearch[lt_user_id]' => $userID],
                    ['data-pjax' => 0, 'target' => '_blank']
                ) . '</small></td>';

                if ($task->field_cnt > 0) {
                    $percent = (int)($completedTasks * 100 / $task->field_cnt);
                } else {
                    $percent = 0;
                }

                $str .= '<td width="100"><div class="progress" style="margin-bottom: 0" title="' . $percent . '%">
                          <div class="progress-bar" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $percent . '%;">
                            ' . $percent . '%
                          </div>
                        </div></td></tr>';
                $item[] = $str;
            }
        }


        if ($item) {
            $str = '<table class="table table-bordered table-condensed">';
            //$str .= '<tr><th class="text-center">'.implode('</th><th class="text-center">', $itemHeader).'</th></tr>';

            //$str .= '<tr>';
            //$str .= '<td class="text-center">' . implode('</td><td class="text-center">', $item) . '</td>';
            //$str .= '</tr>';
            $str .= implode('', $item);
            $str .= '</table>';
        } else {
            $str = '-';
        }

        return $str;
    }

    /**
     * @return array
     */
    public function paramsForSalary(): array
    {
        $data = [];
        if ($this->userParams) {
            $data['base_amount'] = is_numeric($this->userParams->up_base_amount) ? (float) $this->userParams->up_base_amount : 0;
            $data['commission_percent'] = is_numeric($this->userParams->up_commission_percent) ? (float) $this->userParams->up_commission_percent : 0;
            $data['bonus_active'] = is_numeric($this->userParams->up_bonus_active) ? $this->userParams->up_bonus_active : 0;
        } else {
            $data['base_amount'] = 200;
            $data['commission_percent'] = 10;
            $data['bonus_active'] = 1;
        }

        $data['profit_bonuses'] = $this->getProfitBonuses();
        if (empty($data['profit_bonuses'])) {
            $data['profit_bonuses'] = self::PROFIT_BONUSES;
        }

        return $data;
    }

    /*
     * @param startDate DateTime
     * @param endDat DateTime
     *
     * */
    public function calculateSalaryBetween($startDate, $endDate)
    {
        $paramsForSalary = $this->paramsForSalary();
        $base = $paramsForSalary['base_amount'];
        $commission = $paramsForSalary['commission_percent'];
        $bonusActive = $paramsForSalary['bonus_active'];
        $bonus = 0;

        $query = new Query();
        $query->select([
            'lead_id' => 'l.id',
            'final_profit' => 'l.final_profit',
            'agents_processing_fee' => 'l.agents_processing_fee',
            'q_id' => 'q.id',
            'pax_cnt' => '(l.adults + l.children)',
            'fare_type' => 'q.fare_type',
            'check_payment' => 'q.check_payment',
            'tips' => 'l.tips',
            'agent_type' => "(CASE WHEN l.employee_id = $this->id THEN 'main' ELSE 'split' END)",
            'minus_percent_profit' => 'SUM(ps.ps_percent)',
            'split_percent_profit' => "SUM(CASE WHEN ps.ps_user_id = $this->id THEN ps.ps_percent ELSE 0 END)",
            'minus_percent_tips' => 'SUM(ts.ts_percent)',
            'split_percent_tips' => "SUM(CASE WHEN ts.ts_user_id = $this->id THEN ts.ts_percent ELSE 0 END)"
        ])
            ->from(Lead::tableName() . ' l')
            ->leftJoin(Quote::tableName() . ' q', 'q.lead_id = l.id')
            ->leftJoin(ProfitSplit::tableName() . ' ps', 'ps.ps_lead_id = l.id')
            ->leftJoin(TipsSplit::tableName() . ' ts', 'ts.ts_lead_id = l.id')
            ->where(['l.status' => Lead::STATUS_SOLD, 'q.status' => Quote::STATUS_APPLIED])
            ->andWhere('l.employee_id = ' . $this->id . ' OR ps.ps_user_id = ' . $this->id . ' OR ts.ts_user_id = ' . $this->id)
            ->groupBy(['q.id', 'l.id']);

        if ($startDate !== null || $endDate !== null) {
            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = l.status AND lead_flow.lead_id = l.id');
            if ($startDate !== null) {
                $subQuery->andFilterWhere(['>=', 'DATE(lead_flow.created)', $startDate->format('Y-m-d')]);
            }
            if ($endDate !== null) {
                $subQuery->andFilterWhere(['<=', 'DATE(lead_flow.created)', $endDate->format('Y-m-d')]);
            }
            $query->andWhere(['IN', 'l.id', $subQuery]);
        }

        //echo $query->createCommand()->getRawSql();die;
        $res = $query->all();

        $profit = 0;
        foreach ($res as $entry) {
            $entry['minus_percent_profit'] = intval($entry['minus_percent_profit']);
            $entry['minus_percent_tips'] = intval($entry['minus_percent_tips']);
            $quote = Quote::findOne(['id' => $entry['q_id']]);
            if ($entry['final_profit'] !== null) {
                $totalProfit = $entry['final_profit'];
                $agentsProcessingFee = !is_null($quote->agent_processing_fee) ? $quote->agent_processing_fee : $entry['pax_cnt'] * SettingHelper::processingFee();
                $totalProfit -= $agentsProcessingFee;
            } else {
                $totalProfit = $quote->getEstimationProfit();
            }
            $totalTips = $entry['tips'] / 2;
            if ($entry['agent_type'] == 'main') {
                $agentProfit = $totalProfit * (100 - $entry['minus_percent_profit']) / 100;
                $agentTips = ($totalTips > 0) ? ($totalTips * (100 - $entry['minus_percent_tips']) / 100) : 0;
            } else {
                $agentProfit = $totalProfit * $entry['split_percent_profit'] / 100;
                $agentTips = ($totalTips > 0) ? ($totalTips * $entry['split_percent_tips'] / 100) : 0;
            }
            $profit += $agentProfit + $agentTips;
        }

        if ($bonusActive) {
            $profitBonuses = $paramsForSalary['profit_bonuses'];
            foreach ($profitBonuses as $profKey => $bonusVal) {
                if ($profit >= $profKey) {
                    $bonus = $bonusVal;
                    break;
                }
            }
        }

        $startProfit = $profit;
        $profit = $profit * $commission / 100;

        return [
            'salary' => $profit + $base + $bonus,
            'base' => $base,
            'bonus' => $bonus,
            'startProfit' => $startProfit,
            'commission' => $commission,
        ];
    }


    /**
     * @param array $statusList
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function getLeadCountByStatus(array $statusList = [], string $startDate = null, string $endDate = null): int
    {
        if ($startDate) {
            $startDate = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
        }

        if ($endDate) {
            $endDate = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));
        }

        $query = LeadFlow::find()->select('COUNT(DISTINCT(lead_id))')->where(['lf_owner_id' => $this->id, 'status' => $statusList]);
        $query->andFilterWhere(['>=', 'created', $startDate]);
        $query->andFilterWhere(['<=', 'created', $endDate]);
        $count = $query->asArray()->scalar();
        return $count;
    }

    /**
     * @param array $statusList
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $userID
     * @return int
     */
    public static function getLeadCountByStatusSupervision(array $statusList = [], string $startDate = null, string $endDate = null, int $userID = 0): int
    {
        if ($startDate) {
            $startDate = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
        }

        if ($endDate) {
            $endDate = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));
        }

        $query = LeadFlow::find()->select('COUNT(DISTINCT(lead_id))')->where(['lf_owner_id' => $userID, 'status' => $statusList]);
        $query->andFilterWhere(['>=', 'created', $startDate]);
        $query->andFilterWhere(['<=', 'created', $endDate]);
        $count = $query->asArray()->scalar();
        return $count;
    }

    /**
     * @param array $statusList
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function getLeadCountByStatusAndEmployee(array $statusList = [], string $startDate = null, string $endDate = null): int
    {
        if ($startDate) {
            $startDate = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
        }

        if ($endDate) {
            $endDate = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));
        }

        $query = LeadFlow::find()->select('COUNT(DISTINCT(lead_id))')->where(['employee_id' => $this->id, 'status' => $statusList]);
        $query->andFilterWhere(['>=', 'created', $startDate]);
        $query->andFilterWhere(['<=', 'created', $endDate]);
        $count = $query->asArray()->scalar();
        return $count;
    }

    /**
     * @param $employeeId
     * @param $callType
     * @param $status
     * @param $source
     * @param $searchModel EmployeeSearch;
     * @return array|Call[]
     */
    public function getCallsCount($employeeId, $callType, $status, $source, $searchModel)
    {
        $query = Call::find();
        $query->select("COUNT(*) AS cnt, SUM(c_call_duration) AS duration")
            ->where([
                'c_created_user_id' => $employeeId,
                'c_call_type_id' => $callType,
            ]);
        if ($status != null) {
            $query->andWhere([
                'c_call_status' => $status
            ]);
        }

        /*if ($callType == Call::CALL_TYPE_IN){
            $query->andWhere(['NOT',['c_parent_id' => null]]);
        }*/

        if ($source != null) {
            $query->andWhere([
                'c_source_type_id' => $source
            ]);
        }

        $count = $query->asArray()->all();

        return $count;
    }

    /**
     * @param array $statusList
     * @param int|null $from_status_id
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function getLeadCountByStatuses(array $statusList = [], int $from_status_id = null, string $startDate = null, string $endDate = null): int
    {
        if ($startDate) {
            $startDate = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
        }

        if ($endDate) {
            $endDate = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));
        }

        $query = LeadFlow::find()->select('COUNT(DISTINCT(lead_id))')->where(['lf_owner_id' => $this->id, 'status' => $statusList]);

        if ($from_status_id > 0) {
            $query->andWhere(['lf_from_status_id' => $from_status_id]);
        }

        $query->andFilterWhere(['>=', 'created', $startDate]);
        $query->andFilterWhere(['<=', 'created', $endDate]);
        $count = $query->asArray()->scalar();
        return $count;
    }

    public function getProfitBonuses()
    {
        $pb = ProfitBonus::find()->where(['pb_user_id' => $this->id])->orderBy(['pb_min_profit' => SORT_DESC])->all();
        $data = [];
        foreach ($pb as $entry) {
            $data[$entry['pb_min_profit']] = $entry['pb_bonus'];
        }
        return $data;
    }

    /**
     * @param $formatLong
     * @return array
     * @throws \Exception
     */
    public static function timezoneList($formatLong): array
    {
        $timezoneIdentifiers = \DateTimeZone::listIdentifiers(\DateTimeZone:: ALL);
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $tempTimezones = array();
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new \DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = array(
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            );
        }

        // Sort the array by offset,identifier ascending
        usort($tempTimezones, function ($a, $b) {
            return ($a['offset'] === $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        $timezoneList = array();
        foreach ($tempTimezones as $tz) {
            $sign = ($tz['offset'] > 0) ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = ($formatLong)
                ? '(' . $sign . $offset . ') ' . $tz['identifier']
                : $sign . $offset;
        }

        return $timezoneList;
    }

    /**
     * @return ShiftTime
     * @throws \Exception
     */
    public function getShiftTime(): ShiftTime
    {
        if ($this->shiftTime !== null) {
            return $this->shiftTime;
        }

        if ($this->userParams) {
            $this->userParams->up_work_minutes = $this->userParams->up_work_minutes ?: 480;
            $startTime = $this->userParams->up_work_start_tm;
            $workSeconds = (int)$this->userParams->up_work_minutes * 60;

            if ($startTime && $workSeconds) {
                $this->shiftTime = new ShiftTime(
                    new StartTime($startTime),
                    $workSeconds,
                    ($this->userParams->up_timezone ?: 'UTC')
                );
            }

//            if ($startTime && $workHours) {
//                $currentTimeUTC = new \DateTime();
//                $currentTimeUTC->setTimezone(new \DateTimeZone('UTC'));
//
//                $startShiftTimeUTC = new \DateTime(date('Y-m-d') . ' ' . $startTime, new \DateTimeZone($timeZone));
//                $startShiftTimeUTC->setTimezone(new \DateTimeZone('UTC'));
//
//                $endShiftTimeUTC = clone $startShiftTimeUTC;
//                $endShiftTimeUTC->add(new \DateInterval('PT' . $workHours . 'S'));
//
//                $endShiftMinutes = $endShiftTimeUTC->format('H')*60 + $endShiftTimeUTC->format('i');
//                $currentMinutes = $currentTimeUTC->format('H')*60 + $currentTimeUTC->format('i');
//
//                if($startShiftTimeUTC->format('d') != $endShiftTimeUTC->format('d')){
//                    //var_dump($currentMinutes, $endShiftMinutes, ($currentMinutes >= 0 && $endShiftMinutes >= $currentMinutes));
//                    if($currentMinutes >= 0 && $endShiftMinutes >= $currentMinutes){
//                        $startShiftTimeUTC->modify('-1 day');
//                        $endShiftTimeUTC->modify('-1 day');
//                    }
//
//                }
//                // $startShiftTimeDt = $startShiftTimeUTC->format('Y-m-d H:i:s');
//                // $endShiftTimeDt = $endShiftTimeUTC->format('Y-m-d H:i:s');
//                // echo $startShiftTimeUTC.' - '.$endShiftTimeUTC; exit;
//
//                $startTS = $startShiftTimeUTC->getTimestamp();
//                $endTS = $endShiftTimeUTC->getTimestamp();
//
//                $shiftData['start_utc_ts'] = $startTS;
//                $shiftData['end_utc_ts'] = $endTS;
//
//                $shiftData['start_period_utc_ts'] = $endTS - (24 * 60 * 60);
//
//                $shiftData['start_utc_dt'] = $startShiftTimeUTC->format('Y-m-d H:i:s');
//                $shiftData['end_utc_dt'] = $endShiftTimeUTC->format('Y-m-d H:i:s');
//
//                $shiftData['start_period_utc_dt'] = date('Y-m-d H:i:s', $shiftData['start_period_utc_ts']);
//            }
//            $this->shiftData = $shiftData;
        }

        if ($this->shiftTime === null) {
            $this->shiftTime = new ShiftTime();
        }

        return $this->shiftTime;
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public function checkShiftTime(): bool
    {
        $shiftTime = $this->getShiftTime();

        if (!$shiftTime->isEmpty()) {
            $currentTS = time();
            $startTS = $shiftTime->startUtcTs ?: 0;
            $endTS = $shiftTime->endUtcTs ?: 0;
            if ($startTS <= $currentTS && $endTS >= $currentTS) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $flowDescriptions ['Manual create', 'Call AutoCreated Lead']
     * @return int|string
     * @throws \Exception
     */
    public function getCountNewLeadCurrentShift(array $flowDescriptions = [])
    {
        $shiftTime = $this->getShiftTime();

        $default = [LeadFlow::DESCRIPTION_TAKE];
        $descriptions = array_merge($default, $flowDescriptions);

        $query = LeadFlow::find()
            ->where(['>=', 'created', $shiftTime->endLastPeriodDt])
            ->andWhere(['<=', 'created', $shiftTime->endUtcDt])
//            ->andWhere(['employee_id' => $this->id])
            ->andWhere(['lf_owner_id' => $this->id])
            ->andWhere(['lf_description' => $descriptions]);

        if (!in_array(LeadFlow::DESCRIPTION_MANUAL_CREATE, $descriptions, false)) {
            $query->andWhere(['lf_from_status_id' => Lead::STATUS_PENDING]);
        }

        $query->andWhere(['status' => Lead::STATUS_PROCESSING]);

        $count = $query->count();
        return $count;
    }

    /**
     * @param array $flowDescriptions
     * @return bool
     * @throws \Exception
     */
    public function accessTakeNewLead(array $flowDescriptions = []): bool
    {
        $access = false;
        //$shift = $this->getShiftTime();


        if ($params = $this->userParams) {
            if (!$params->up_min_percent_for_take_leads) {
                $access = true;
            } else {
                $currentShiftTaskInfoSummary = $this->getCurrentShiftTaskInfoSummary();
                if ($currentShiftTaskInfoSummary['completedTasksPercent'] >= $params->up_min_percent_for_take_leads) {
                    $access = true;
                } else {
                    $countNewLeads = $this->getCountNewLeadCurrentShift($flowDescriptions);

                    if (!$params->up_default_take_limit_leads) {
                        $access = true;
                    } elseif ($countNewLeads < $params->up_default_take_limit_leads) {
                        $access = true;
                    }
                }
            }
        }

        return $access;
    }

    /**
     * @param array $flowDescriptions
     * @param array $fromStatuses
     * @return array
     * @throws \Exception
     */
    public function accessTakeLeadByFrequencyMinutes(array $flowDescriptions = [], array $fromStatuses = []): array
    {
        $access = true;
        $takeDt = new \DateTime();

        if (
            ($params = $this->userParams)
            && ($frequencyMinutes = $params->up_frequency_minutes)
            && ($lastTakenDt = $this->getLastTakenLeadDt($flowDescriptions, $fromStatuses))
        ) {
            $nextTakeUTC = (new \DateTime($lastTakenDt, new \DateTimeZone('UTC')))
                ->add(new \DateInterval('PT' . $frequencyMinutes . 'M'));

            $nowUTC = new \DateTime('now', new \DateTimeZone('UTC'));

            if ($nextTakeUTC > $nowUTC) {
                $access = false;
                $takeDt = $nextTakeUTC;
            }
        }

        $takeDt->setTimezone(new \DateTimeZone($this->userParams->up_timezone ?: 'UTC'));
        $takeDtUTC = $takeDt->setTimezone(new \DateTimeZone('UTC'));

        return ['access' => $access, 'takeDt' => $takeDt, 'takeDtUTC' => $takeDtUTC];
    }

    public static function getAllEmployeesByRole($role = self::ROLE_AGENT)
    {
        return self::find()->leftJoin('auth_assignment', 'auth_assignment.user_id = id')->andWhere(['auth_assignment.item_name' => $role])->all();
    }

    /**
     * @param int $user_id
     * @param bool $onyEnabled
     * @return array
     */
    public static function getPhoneList(int $user_id, $onyEnabled = false): array
    {
//        $phoneList = [];
//
//
//        $phones = UserProjectParams::find()->select(['DISTINCT(upp_tw_phone_number)'])->where(['upp_user_id' => $user_id])
//            ->andWhere(['and', ['<>', 'upp_tw_phone_number', ''], ['IS NOT', 'upp_tw_phone_number', null]])
//            ->asArray()->all();
//
//        if($phones) {
//            $phoneList = ArrayHelper::map($phones, 'upp_tw_phone_number', 'upp_tw_phone_number');
//        }

        $phoneList = UserProjectParams::find()
            ->select(['pl_phone_number', 'upp_phone_list_id'])
            ->byUserId($user_id)
            ->innerJoinWith(['phoneList' => static function (\src\model\phoneList\entity\Scopes $query) use ($onyEnabled) {
                if ($onyEnabled) {
                    $query->andOnCondition(['pl_enabled' => true]);
                }
            }], false)
            ->indexBy('pl_phone_number')
            ->column();

        return $phoneList;
    }

    /**
     * @param int $user_id
     * @param bool $onyEnabled
     * @return array
     */
    public static function getEmailList(int $user_id, $onyEnabled = false): array
    {
        $emailList = UserProjectParams::find()
            ->select(['el_email', 'upp_email_list_id'])
            ->byUserId($user_id)
            ->innerJoinWith(['emailList' => static function (\src\model\emailList\entity\Scopes $query) use ($onyEnabled) {
                if ($onyEnabled) {
                    $query->andOnCondition(['el_enabled' => true]);
                }
            }], false)
            ->indexBy('el_email')
            ->column();

        return $emailList;
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->userOnline ? true : false;
        //return UserOnline::find()->where(['uo_user_id' => $this->id])->exists();
    }

    /**
     * @return bool
     */
    public function isCallStatusReady(): bool
    {
        $status = $this->userStatus;

        if ($status && $status->us_call_phone_status) {
            return true;
        }

        /*$isReady = true;
        $ucs = UserCallStatus::find()->where(['us_user_id' => $this->id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();
        if($ucs) {
            if((int) $ucs->us_type_id === UserCallStatus::STATUS_TYPE_OCCUPIED) {
                $isReady = false;
            }
        }*/

        return false;
    }

    /**
     * @return bool
     */
    public function isCallFree(): bool
    {
        $status = $this->userStatus;

        if ($status && $status->us_is_on_call) {
            return false;
        }

        /*$callExist = Call::find()->where(['c_created_user_id' => $this->id, 'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]])->exists(); //Call::CALL_STATUS_QUEUE, andWhere(['<>', 'c_parent_id', null])

        if ($callExist) {
            return false;
        }*/

        return true;
    }



    /**
     * @param int $user_id
     * @param int|null $supervision_id
     * @return bool
     */
    public static function isSupervisionAgent(int $user_id, int $supervision_id = null): bool
    {
        //$exist = false;
        if (!$supervision_id) {
            $supervision_id = Yii::$app->user->id;
        }

        $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $supervision_id]);
        $exist = UserGroupAssign::find()->select(['ugs_user_id'])->where(['IN', 'ugs_group_id', $subQuery1])->andWhere(['ugs_user_id' => $user_id])->exists();

        return $exist;
    }


    /**
     * @param Call $call
     * @return array
     */
    public static function getUsersForRedirectCall(Call $call): array
    {
        $query = UserConnection::find();
        $subQuery1 = UserProfile::find()->select(['up_call_type_id'])->where('up_user_id = user_connection.uc_user_id');

        $query->select([
            'tbl_user_id' => 'user_connection.uc_user_id',
            'tbl_call_type_id' => $subQuery1,
        ]);

        $subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $call->c_project_id]);
        $query->andWhere(['IN', 'user_connection.uc_user_id', $subQuery]);
        $query->groupBy(['user_connection.uc_user_id']);

        $allow_cross_department_call_transfers = Yii::$app->params['settings']['allow_cross_department_call_transfers'] ?? false;

        if (!$allow_cross_department_call_transfers) {
            if ($call->c_dep_id) {
                $subQueryUd = UserDepartment::find()->usersByDep($call->c_dep_id);
                $query->andWhere(['IN', 'user_connection.uc_user_id', $subQueryUd]);
            }
        }

        $generalQuery = (new Query())->select([
            '*',
            'tbl_has_lead_redial_access' => new Expression('if ((redial.redial_count is null or redial.redial_count = 0), 0, 1)')
        ]);
        $generalQuery->from(['tbl' => $query]);
        $generalQuery->leftJoin([
                'redial' => CallRedialUserAccess::find()->select([
                    'count(*) as redial_count',
                    'crua_user_id'
                ])->groupBy(['crua_user_id'])
        ], 'redial.crua_user_id = tbl_user_id');
        $generalQuery->andWhere(['AND', ['<>', 'tbl_call_type_id', UserProfile::CALL_TYPE_OFF], ['IS NOT', 'tbl_call_type_id', null]]);

        //$sqlRaw = $generalQuery->createCommand()->getRawSql();
        //VarDumper::dump($sqlRaw, 10, true); exit;
        $users = $generalQuery->all();
        return $users;
    }

    /**
     * @param Call $call
     * @param int $limit
     * @param int $hours
     * @param array|null $exceptUserIds
     * @return array
     */
    public static function getUsersForCallQueueOld(Call $call, int $limit = 0, int $hours = 1, ?array $exceptUserIds = null): array
    {
        $project_id = $call->c_project_id;
        $department_id = $call->c_dep_id;


        $query = UserConnection::find();
        $date_time = date('Y-m-d H:i:s', strtotime('-' . $hours . ' hours'));

        $subQuery2 = UserCallStatus::find()->select(['us_type_id'])->where('us_user_id = user_connection.uc_user_id')->orderBy(['us_id' => SORT_DESC])->limit(1);
        // $subQuery3 = Call::find()->select(['c_status_id'])->where('c_created_user_id = user_connection.uc_user_id')->orderBy(['c_id' => SORT_DESC])->limit(1);
        $subQuery3 = Call::find()->select('COUNT(*)')->where('c_created_user_id = user_connection.uc_user_id')->andWhere(['c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]])->limit(1);
        $subQuery4 = UserProfile::find()->select(['up_call_type_id'])->where('up_user_id = user_connection.uc_user_id');
        $subQuery5 = Call::find()->select(['COUNT(*)'])
            ->where('c_created_user_id = user_connection.uc_user_id')
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(['c_status_id' => Call::STATUS_COMPLETED])
            ->andWhere(['c_project_id' => $project_id])
            ->andWhere(['>=', 'c_created_dt', $date_time]);


        $query->select(
            [
                'tbl_user_id' => 'user_connection.uc_user_id',
                'tbl_call_status_id' => $subQuery2,
                'tbl_calls_count_process' => $subQuery3,
                'tbl_call_type_id' => $subQuery4,
                'tbl_calls_count' => $subQuery5,
            ]
        );

        //$subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $project_id]);
        //$query->andWhere(['IN', 'user_connection.uc_user_id', $subQuery]);

        $subQuery = CallUserAccess::find()->select(['DISTINCT(cua_user_id)'])->where(['cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING]);
        $query->andWhere(['NOT IN', 'user_connection.uc_user_id', $subQuery]);

        $subQueryUpp = UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])->where(['upp_project_id' => $project_id, 'upp_allow_general_line' => true]);
        $query->andWhere(['IN', 'user_connection.uc_user_id', $subQueryUpp]);

        if ($exceptUserIds) {
            $query->andWhere(['NOT IN', 'user_connection.uc_user_id', $exceptUserIds]);
        }

        if ($department_id) {
            $subQueryUd = UserDepartment::find()->usersByDep($department_id);
            $query->andWhere(['IN', 'user_connection.uc_user_id', $subQueryUd]);
        }

        if ($call->cugUgs) {
            $groupIds = ArrayHelper::map($call->cugUgs, 'ug_id', 'ug_id');
            if ($groupIds) {
                $subQueryUGroup = UserGroupAssign::find()->select('ugs_user_id')->distinct('ugs_user_id')->where(['ugs_group_id' => $groupIds]);
                $query->andWhere(['IN', 'user_connection.uc_user_id', $subQueryUGroup]);
            }
        }


        $query->groupBy(['user_connection.uc_user_id']);
        $query->orderBy(['tbl_calls_count' => SORT_ASC]);

        $generalQuery = new Query();
        $generalQuery->from(['tbl' => $query]);
        // $generalQuery->andWhere(['OR', ['NOT IN', 'tbl_last_status_id', [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]], ['tbl_last_status_id' => null]]);
        $generalQuery->andWhere(['OR', ['tbl_calls_count_process' => 0], ['tbl_calls_count_process' => null]]);
        $generalQuery->andWhere(['OR', ['tbl_call_status_id' => UserCallStatus::STATUS_TYPE_READY], ['tbl_call_status_id' => null]]);
        $generalQuery->andWhere(['AND', ['=', 'tbl_call_type_id', UserProfile::CALL_TYPE_WEB], ['IS NOT', 'tbl_call_type_id', null]]);
        $generalQuery->orderBy(['tbl_calls_count' => SORT_ASC]);

        if ($limit > 0) {
            $generalQuery->limit($limit);
        }

        //$sqlRaw = $generalQuery->createCommand()->getRawSql();
        //echo '<pre>'.print_r($sqlRaw, true).'</pre>';  exit;
        //VarDumper::dump($sqlRaw, 10, true); exit;
        $users = $generalQuery->all();
        return $users;
    }



    /**
     * @param Call $call
     * @param int $limit
     * @param int $hours
     * @param array|null $exceptUserIds
     * @return array
     */
    public static function getUsersForCallQueue(Call $call, int $limit = 0, int $hours = 1, ?array $exceptUserIds = null): array
    {
        $project_id = $call->c_project_id;
        $department_id = $call->c_dep_id;

        $query = UserOnline::find()->alias('uo');

        $query->select(['tbl_user_id' => 'uo.uo_user_id']);

        $query->innerJoin('user_status AS us', 'uo.uo_user_id = us.us_user_id');
        $query->innerJoin('user_profile AS up', 'uo.uo_user_id = up.up_user_id');
        $query->innerJoin('user_project_params AS upp', 'uo.uo_user_id = upp.upp_user_id');

        $query->andWhere(['us.us_call_phone_status' => true]);

        if (!SettingHelper::isGeneralLinePriorityEnable()) {
            $query->andWhere(['us.us_has_call_access' => false]);
            $query->andWhere(['us.us_is_on_call' => false]);
        }

        $query->andWhere(['up.up_call_type_id' => UserProfile::CALL_TYPE_WEB]);
        $query->andWhere(['upp.upp_allow_general_line' => true, 'upp.upp_project_id' => $project_id]);

        if ($exceptUserIds) {
            $query->andWhere(['NOT IN', 'uo.uo_user_id', $exceptUserIds]);
        }

        if ($department_id) {
            $query->innerJoin('user_department AS ud', 'uo.uo_user_id = ud.ud_user_id');
            $query->andWhere(['ud.ud_dep_id' => $department_id]);
        }

        if ($call->cugUgs) {
            $groupIds = ArrayHelper::map($call->cugUgs, 'ug_id', 'ug_id');
            if ($groupIds) {
                $query->innerJoin('user_group_assign AS ugs', 'uo.uo_user_id = ugs.ugs_user_id');
                $query->andWhere(['ugs.ugs_group_id' => $groupIds]);
            }
        }

        $query->leftJoin(
            ['udata' => UserData::tableName()],
            'udata.ud_user_id = uo.uo_user_id and udata.ud_key = :key',
            [':key' => UserDataKey::GROSS_PROFIT]
        );

        $query->innerJoin(['uparams' => UserParams::tableName()], 'uparams.up_user_id = uo.uo_user_id');

        //$query->groupBy(['uo.uo_user_id']);
        $sort = self::getUsersForCallQueueSort($call);

        $sortAttributes = [
            'general_line_call_count' => 'us.us_gl_call_count',
            'phone_ready_time' => 'us.us_phone_ready_time',
            'priority_level' => 'uparams.up_call_user_level',
            'gross_profit' => 'udata.ud_value',
        ];

        if (!empty($sort)) {
            foreach ($sort as $key => $item) {
                if (isset($sortAttributes[$key])) {
                    $query->addOrderBy([$sortAttributes[$key] => $item]);
                }
            }
        } else {
            $query->addOrderBy(['us.us_phone_ready_time' => SORT_ASC]);
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $query->indexBy(['tbl_user_id']);

//        $sqlRaw = $query->createCommand()->getRawSql();
//        echo '<pre>'.print_r($sqlRaw, true).'</pre>';  exit;
//        VarDumper::dump($sqlRaw, 10, true); exit;

        $users = $query->asArray()->all();
        return $users;
    }

    public static function convertTimeFromUtcToUserTime($timezone, int $time, ?string $format = 'Y-m-d H:i:s'): string
    {
        if (!$timezone) {
            $timezone = 'UTC';
        }
        try {
            return (new \DateTimeImmutable(date('Y-m-d H:i:s', $time), new \DateTimeZone('UTC')))
                ->setTimezone(new \DateTimeZone($timezone))
                ->format($format);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * @param int $time
     * @return string
     */
    public static function convertTimeFromUserDtToUTC(int $time, ?Employee $employee = null): string
    {
        $dateTime = '';

        if ($time >= 0) {
            $user = $employee ?: \Yii::$app->user->identity ?? null;
            if (!$user) {
                throw new \RuntimeException('User is empty in method convertTimeFromUserDtToUTC');
            }
            $timezone = $user->timezone;
            $dateTime = date('Y-m-d H:i:s', $time);

            try {
                if ($timezone) {
                    $date = new \DateTime($dateTime, new \DateTimeZone($timezone));
                    $date->setTimezone(new \DateTimeZone('UTC'));
                    $dateTime = $date->format('Y-m-d H:i:s');
                }
            } catch (\Throwable $throwable) {
                $dateTime = '';
            }
        }

        return $dateTime;
    }

    public static function convertToUTC(int $time, string $timezone): string
    {
        $dateTime = '';

        if ($time >= 0) {
            $dateTime = date('Y-m-d H:i:s', $time);

            try {
                if ($timezone) {
                    $date = new \DateTime($dateTime, new \DateTimeZone($timezone));
                    $date->setTimezone(new \DateTimeZone('UTC'));
                    $dateTime = $date->format('Y-m-d H:i:s');
                }
            } catch (\Throwable $throwable) {
                $dateTime = '';
            }
        }

        return $dateTime;
    }

    /**
     * $time_zone ex. = 'Europe/Chisinau'
     */
    public static function getUtcOffsetDst($time_zone, $dateToCheck)
    {
        // Set UTC as default time zone.
        //date_default_timezone_set( 'UTC' );
        $utc = new \DateTime($dateToCheck);
        // Calculate offset.
        $current = timezone_open($time_zone);
        $offset_s  = timezone_offset_get($current, $utc); // seconds
        $offset_s  = (string) $offset_s;
        $sign = ($offset_s > 0) ? '+' : '-';
        $hours = floor(abs($offset_s) / 3600);
        $minutes = floor((abs($offset_s) / 60) % 60);
        return $sign . sprintf("%02d:%02d", $hours, $minutes);
    }

    /**
     * @return string|null
     */
    public function findEmployeeIp(): ?string
    {
        return Yii::$app->request->remoteIP;
    }

    public function checkIfUsersIpIsAllowed(): bool
    {
        $allowedIp = Yii::$app->params['settings']['test_allow_ip_address_list'] ?? Yii::$app->params['test_allow_ip_address_list'];

        return in_array($this->findEmployeeIp(), $allowedIp ?? [], false);
    }

    /**
     * @return EmployeeQuery
     */
    public static function find(): EmployeeQuery
    {
        return new EmployeeQuery(static::class);
    }

    /**
     * @return string[]
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->status] ?? '-';
    }

    /**
     * @param int $s
     * @param string $default
     * @return string
     */
    public function getGravatarUrl(int $s = 128, string $default = 'robohash'): string
    {
        if ($this->email) {
            $url = '//www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?d=' . $default . '&s=' . $s;
        } else {
            $url = '//www.gravatar.com/avatar/?d=' . $default . '&s=60';
        }
        return $url;
    }

    public function getAvatar(): string
    {
        return strtoupper($this->full_name[0] ?? '');
    }

    public function getProjectsToArray(): array
    {
        $projects = [];
        foreach ($this->projects as $project) {
            $projects[$project->id] = [
                'id' => $project->id,
                'name' => $project->name,
                'key' => $project->project_key,
                'closed' => $project->closed,
            ];
        }
        return $projects;
    }

    public function getDepartmentsToArray(): array
    {
        $departments = [];
        foreach ($this->udDeps as $department) {
            $departments[$department->dep_id] = [
                'dep_id' => $department->dep_id,
                'dep_key' => $department->dep_key,
                'dep_name' => $department->dep_name,
            ];
        }
        return $departments;
    }

    public function getGroupsToArray(): array
    {
        $groups = [];
        foreach ($this->ugsGroups as $group) {
            $groups[$group->ug_id] = [
                'ug_id' => $group->ug_id,
                'ug_name' => $group->ug_name,
                'ug_disable' => $group->ug_disable,
            ];
        }
        return $groups;
    }

    /**
     * @return UserStatus|null
     */
    public function initUserStatus(): ?UserStatus
    {
        $last_hours = (int)(Yii::$app->params['settings']['general_line_last_hours'] ?? 1);
        $date_time = date('Y-m-d H:i:s', strtotime('-' . $last_hours . ' hours'));

        $onCall = false;
        $calls = Call::find()
            ->byCreatedUser($this->id)
            ->andWhere(['c_status_id' => [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING]])
            ->all();
        foreach ($calls as $call) {
            $creatorType = $call->getDataCreatorType();
            if ($creatorType->isAgent() || $creatorType->isUser()) {
                $onCall = true;
                break;
            }
        }

        $glCallCount = (int) Call::find()->select('COUNT(*)')->where(['c_created_user_id' => $this->id, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_status_id' => Call::STATUS_COMPLETED])
            ->andWhere(['IS NOT', 'c_parent_id', null])
            ->andWhere(['>=', 'c_created_dt', $date_time])
            ->andWhere(['c_source_type_id' => Call::SOURCE_GENERAL_LINE])
            ->scalar();

        $lastUserCallStatus = UserCallStatus::find()->where(['us_user_id' => $this->id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();

        if ($lastUserCallStatus && (int) $lastUserCallStatus->us_type_id === UserCallStatus::STATUS_TYPE_READY) {
            $callPhoneStatus = true;
        } else {
            $callPhoneStatus = false;
        }

        $callAccess = CallUserAccess::find()->where(['cua_user_id' => $this->id, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])->exists();

        $userStatus = UserStatus::findOne($this->id);

        if (!$userStatus) {
            $userStatus = new UserStatus();
            $userStatus->us_user_id = $this->id;
        }

        $userStatus->us_gl_call_count = $glCallCount;
        $userStatus->us_is_on_call = $onCall;
        $userStatus->us_call_phone_status = $callPhoneStatus;
        $userStatus->us_has_call_access = $callAccess;

        if (!$userStatus->save()) {
            \Yii::error(
                VarDumper::dumpAsString($userStatus->errors),
                'Employee:initUserStatus:UserStatus:save'
            );
        }

        return $userStatus;
    }

    /**
     * @param string $permission
     * @param array $params
     * @return bool
     */
    public function can(string $permission, array $params = []): bool
    {
        return Yii::$app->authManager->checkAccess($this->id, $permission, $params);
    }


    /**
     * @param bool $onlyNames
     * @return array
     */
    public function getRoleList(): array
    {
        if ($this->rolesName === null) {
            //todo
            $query = (new Query())->select('b.*')
                ->from(['a' => 'auth_assignment', 'b' => 'auth_item'])
                ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
                ->andWhere(['a.user_id' => (string) $this->id])
                ->andWhere(['b.type' => 1]);
            $this->rolesName = ArrayHelper::map($query->all(), 'name', 'description');
//            $this->rolesName = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($this->id), 'name', 'description');
        }
        if ($onlyNames) {
            return array_keys($this->rolesName);
        }
        return $this->rolesName;
    }


    /**
     * @return array
     */
    public static function getEnvListWOCache(): array
    {
        $data = self::find()->orderBy(['ug_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'ug_name', 'ug_name');
    }


    /**
     * @return array
     */
    public static function getEnvList(): array
    {
        if (self::CACHE_KEY) {
            $list = Yii::$app->cache->get(self::CACHE_KEY);
            if ($list === false) {
                $list = self::getEnvListWOCache();

                Yii::$app->cache->set(
                    self::CACHE_KEY,
                    $list,
                    0,
                    new TagDependency(['tags' => self::CACHE_TAG_DEPENDENCY])
                );
            }
        } else {
            $list = self::getEnvListWOCache();
        }

        return $list;
    }

    private static function getUsersForCallQueueSort(Call $call): array
    {
        if ($call->c_dep_id && $params = $call->cDep->getParams()) {
            if ($params->queueDistribution->callDistributionSort && $params->queueDistribution->callDistributionSort->generalLineCallCount) {
                $sort['general_line_call_count'] = $params->queueDistribution->callDistributionSort->generalLineCallCount;
            }

            if ($params->queueDistribution->callDistributionSort && $params->queueDistribution->callDistributionSort->phoneReadyTime) {
                $sort['phone_ready_time'] = $params->queueDistribution->callDistributionSort->phoneReadyTime;
            }

            if ($params->queueDistribution->callDistributionSort && $params->queueDistribution->callDistributionSort->priorityLevel) {
                $sort['priority_level'] = $params->queueDistribution->callDistributionSort->priorityLevel;
            }

            if ($params->queueDistribution->callDistributionSort && $params->queueDistribution->callDistributionSort->grossProfit) {
                $sort['gross_profit'] = $params->queueDistribution->callDistributionSort->grossProfit;
            }

            if (!empty($sort)) {
                return $sort;
            }
        }
        $sort = SettingHelper::getCallDistributionSort();
        return $sort;
    }

    public function getRelations(): UserRelations
    {
        if ($this->userRelations !== null) {
            return $this->userRelations;
        }
        $this->userRelations = new UserRelations($this);
        return $this->userRelations;
    }

    public function addLog($appId, $targetId, $oldAttr, $newAttr)
    {
        $globalLogFormatAttrService = \Yii::createObject(GlobalEntityAttributeFormatServiceService::class);

        (\Yii::createObject(GlobalLogInterface::class))->log(
            new LogDTO(
                get_class($this),
                $this->id,
                $appId,
                $targetId,
                JSON::encode($oldAttr),
                JSON::encode($newAttr),
                $globalLogFormatAttrService->formatAttr(get_class($this), JSON::encode($oldAttr), JSON::encode($newAttr)),
                GlobalLog::ACTION_TYPE_UPDATE
            )
        );
    }

    public function isSameUser(Employee $user): bool
    {
        return $this->id === $user->id;
    }

    public function isSameUserGroup(array $groups): bool
    {
        foreach ($this->getUserGroupList() as $groupId => $groupName) {
            if (in_array($groupId, $groups, true)) {
                return true;
            }
        }
        return false;
    }

    public function isSameDepartment(array $departments): bool
    {
        foreach ($this->getUserDepartmentList() as $departmentId => $departmentName) {
            if (in_array($departmentId, $departments, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all supervision ids by current user
     * @return array
     */
    public function getSupervisionIdsByCurrentUser(): array
    {
        $userList = [];
        if (!($this->isSupervision() || $this->isAdmin())) {
            foreach ($this->ugsGroups as $group) {
                foreach ($group->ugsUsers as $userInGroup) {
                    if ($userInGroup->isSupervision()) {
                        $userList[] = $userInGroup->id;
                    }
                }
            }
        } else {
            $userList[] = $this->id;
        }

        return $userList;
    }
}
