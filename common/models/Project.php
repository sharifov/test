<?php

namespace common\models;

use common\models\local\ContactInfo;
use common\models\query\ProjectQuery;
use modules\qaTask\src\entities\qaTask\QaTask;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\phoneLine\phoneLine\entity\PhoneLine;
use sales\model\project\entity\params\Params;
use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\CurlTransport;
use common\components\validators\IsArrayValidator;

/**
 * Class Project
 * @package common\models
 *
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $link
 * @property string|null $api_key
 * @property string|null $contact_info
 * @property int|null $closed
 * @property string|null $last_update
 * @property int|null $sort_order
 * @property string|null $email_postfix
 * @property string|null $ga_tracking_id
 * @property string|null $project_key
 * @property int|null $p_update_user_id
 * @property array|string|null $p_params_json
 *
 * @property ContactInfo $contactInfo
 * @property Params|null $params
 *
 * @property ApiUser[] $apiUsers
 * @property Call[] $calls
 * @property ClientChatChannel[] $clientChatChannels
 * @property ClientChatProjectConfig $clientChatProjectConfig
 * @property ClientChat[] $clientChats
 * @property ClientProject[] $clientProjects
 * @property Client[] $cpClients
 * @property DepartmentEmailProject[] $departmentEmailProjects
 * @property DepartmentPhoneProject[] $departmentPhoneProjects
 * @property EmailUnsubscribe[] $emailUnsubscribes
 * @property Email[] $emails
 * @property EmployeeContactInfo[] $employeeContactInfos
 * @property Lead[] $leads
 * @property Employee $pUpdateUser
 * @property PhoneLine[] $phoneLines
 * @property ProjectEmailTemplate[] $projectEmailTemplates
 * @property ProjectEmployeeAccess[] $projectEmployeeAccesses
 * @property ProjectWeight $projectWeight
 * @property QaTask[] $qaTasks
 * @property Sms[] $sms
 * @property SmsDistributionList[] $smsDistributionLists
 * @property Sources[] $sources
 * @property Employee[] $uppUsers
 * @property UserProjectParams[] $userProjectParams
 * @property VisitorLog[] $visitorLogs
 */
class Project extends \yii\db\ActiveRecord
{
    private ContactInfo $_contactInfo;

    private ?Params $params = null;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'projects';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer', 'min' => 0, 'max' => 100],
            [['p_update_user_id'], 'integer'],
            [['closed'], 'boolean'],
            [['contact_info'], 'string'],
            [['last_update'], 'safe'],
            [['name', 'link', 'api_key', 'ga_tracking_id'], 'string', 'max' => 255],
            [['email_postfix'], 'string', 'max' => 100],
            [['project_key'], 'string', 'max' => 50],
            [['project_key'], 'unique'],
            [['p_update_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['p_update_user_id' => 'id']],
            ['p_params_json', IsArrayValidator::class],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_update'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'p_update_user_id',
                'updatedByAttribute' => 'p_update_user_id',
            ],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'link' => 'Link',
            'api_key' => 'Api Key',
            'contact_info' => 'Contact Info',
            'closed' => 'Closed',
            'last_update' => 'Updated Dt',
            'sort_order' => 'Sort',
            'email_postfix' => 'Email postfix',
            'ga_tracking_id' => 'GA Tracking Id',
            'project_key' => 'Project Key',
            'p_update_user_id' => 'Updated User',
            'p_params_json' => 'Parameters',
        ];
    }


//    public function afterFind()
//    {
//        $this->contactInfo = new ContactInfo();
//        if (!empty($this->contact_info)) {
//            $this->contactInfo->attributes = json_decode($this->contact_info, true);
//        }
//        parent::afterFind();
//    }

    /**
     * @return ContactInfo
     * @throws \JsonException
     */
    public function getContactInfo(): ContactInfo
    {
        if (empty($this->_contactInfo)) {
            $this->_contactInfo = new ContactInfo();
            $contactArr = @json_decode($this->contact_info, true, 512, JSON_THROW_ON_ERROR);
            if ($contactArr) {
                $this->_contactInfo->attributes = $contactArr;
            }
        }
        return $this->_contactInfo;
    }

    public function getParams(): Params
    {
        if ($this->params !== null) {
            return $this->params;
        }
        $this->params = Params::fromArray($this->p_params_json ?: []);
        return $this->params;
    }

    /**
     * @return ProjectEmailTemplate[]
     */
    public function getEmailTemplates()
    {
        return ProjectEmailTemplate::findAll(['project_id' => $this->id]);
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'name');
    }

    public static function getSmsEnabledList(): array
    {
        $projects = [];
        foreach (self::find()->all() as $item) {
            if ($item->getParams()->sms->isEnabled()) {
                $projects[$item->id] = $item->name;
            }
        }
        return $projects;
    }

    /**
     * @param int $user_id
     * @return array
     */
    public static function getListByUser(int $user_id = 0): array
    {
        $data = ProjectEmployeeAccess::find()->select(['project_id'])->with('project')->where(['employee_id' => $user_id])->all();
        return ArrayHelper::map($data, 'project_id', 'project.name');
    }

    public static function getListByUserWithProjectKeys(int $user_id = 0): array
    {
        return self::find()->select(['project' => 'name', 'projectKey' => 'project_key'])->innerJoin(ProjectEmployeeAccess::tableName(), 'project_id = id and employee_id = :userId', ['userId' => $user_id])->asArray()->all();
    }


    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function synchronizationProjects(): array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'error' => false
        ];

        $projectsData = self::getProjectListBO();
        //VarDumper::dump($projectsData, 10, true); exit;
        if ($projectsData) {
            if ($projectsData['error']) {
                $data['error'] = 'Error: ' . $projectsData['error'];
            } elseif (isset($projectsData['data']['data']) && $projectsData['data']['data']) {
                foreach ($projectsData['data']['data'] as $projectItem) {
                    $pr = self::findOne($projectItem['id']);
                    if (!$pr) {
                        $pr = new self();
                        $pr->id = $projectItem['id'];
                        $pr->p_params_json = Params::default();
                        $data['created'][] = $projectItem['id'];
                    } else {
                        $data['updated'][] = $projectItem['id'];
                    }

                    $pr->attributes = $projectItem;

                    $pr->name = $projectItem['name'];
                    $pr->project_key = $projectItem['project_key'];
                    $pr->link = $projectItem['link'];
                    $pr->closed = (bool)$projectItem['closed'];
                    /* $pr->last_update = date('Y-m-d H:i:s');
                     if (isset(Yii::$app->user) && Yii::$app->user->id) {
                         $pr->p_update_user_id = Yii::$app->user->id;
                     }*/
                    if (!$pr->save()) {
                        Yii::error(
                            VarDumper::dumpAsString($pr->errors),
                            'Project:synchronizationProjects:Project:save'
                        );
                    } elseif ($projectItem['sources']) {
                        foreach ($projectItem['sources'] as $sourceId => $sourceAttr) {
                            $source = Sources::findOne(['id' => $sourceId]);

                            if (!$source) {
                                $source = new Sources();
                                $source->id = $sourceId;
                                $source->project_id = $pr->id;
                            }

                            $source->scenario = Sources::SCENARIO_SYNCH;

                            $source->attributes = $sourceAttr;
                            if (!$source->save()) {
                                Yii::error(
                                    VarDumper::dumpAsString($source->errors),
                                    'Project:synchronizationProjects:Sources:save'
                                );
                            }
                        }
                    }
                }
            }
        } else {
            $data['error'] = 'Not found response data';
        }

        return $data;
    }


    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function getProjectListBO()
    {
        $out['data'] = false;
        $out['error'] = false;

        $uri = Yii::$app->params['backOffice']['serverUrl'] . '/default/projects';
        $signature = self::getSignatureBO(Yii::$app->params['backOffice']['apiKey'], Yii::$app->params['backOffice']['ver']);

        $client = new \yii\httpclient\Client([
            'transport' => CurlTransport::class,
            'responseConfig' => [
                'format' => \yii\httpclient\Client::FORMAT_JSON
            ]
        ]);

        /*$headers = [
            //"Content-Type"      => "text/xml;charset=UTF-8",
            //"Accept"            => "gzip,deflate",
            //"Cache-Control"     => "no-cache",
            //"Pragma"            => "no-cache",
            //"Authorization"     => "Basic ".$this->api_key,
            //"Content-length"    => mb_strlen($xmlRequest),
        ];*/


        $headers = [
            'version'   => Yii::$app->params['backOffice']['ver'],
            'signature' => $signature
        ];

        //$requestData['cid'] = $this->api_cid;

        $response = $client->createRequest()
            ->setMethod('GET')
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setUrl($uri)
            ->addHeaders($headers)
            //->setContent($json)
            //->setData($requestData)
            ->setOptions([
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 30,
            ])
            ->send();


        //VarDumper::dump($response->content, 10, true); exit;

        if ($response->isOk) {
            $out['data'] = $response->data;
        } else {
            $out['error'] = VarDumper::dumpAsString($response->content, 10);
        }

        return $out;
    }

    /**
     * @param string $apiKey
     * @param string $version
     * @return string
     */
    private static function getSignatureBO(string $apiKey = '', string $version = ''): string
    {
        $expired = time() + 3600;
        $md5 = md5(sprintf('%s:%s:%s', $apiKey, $version, $expired));
        return implode('.', [md5($md5), $expired, $md5]);
    }

    /**
     * @return ProjectQuery the active query used by this AR class.
     */
    public static function find(): ProjectQuery
    {
        return new ProjectQuery(static::class);
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getEmailPostfix(int $id): ?string
    {
        $emailPostfix = self::find()->select('email_postfix')->where(['id' => $id])->asArray()->one();
        return $emailPostfix['email_postfix'] ?? null;
    }


    //---------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Gets query for [[ApiUsers]].
     *
     * @return ActiveQuery
     */
    public function getApiUsers(): ActiveQuery
    {
        return $this->hasMany(ApiUser::class, ['au_project_id' => 'id']);
    }

    /**
     * Gets query for [[Calls]].
     *
     * @return ActiveQuery
     */
    public function getCalls(): ActiveQuery
    {
        return $this->hasMany(Call::class, ['c_project_id' => 'id']);
    }

    /**
     * Gets query for [[ClientChatChannels]].
     *
     * @return ActiveQuery
     */
    public function getClientChatChannels(): ActiveQuery
    {
        return $this->hasMany(ClientChatChannel::class, ['ccc_project_id' => 'id']);
    }

    /**
     * Gets query for [[ClientChatProjectConfig]].
     *
     * @return ActiveQuery
     */
    public function getClientChatProjectConfig(): ActiveQuery
    {
        return $this->hasOne(ClientChatProjectConfig::class, ['ccpc_project_id' => 'id']);
    }

    /**
     * Gets query for [[ClientChats]].
     *
     * @return ActiveQuery
     */
    public function getClientChats(): ActiveQuery
    {
        return $this->hasMany(ClientChat::class, ['cch_project_id' => 'id']);
    }

    /**
     * Gets query for [[ClientProjects]].
     *
     * @return ActiveQuery
     */
    public function getClientProjects(): ActiveQuery
    {
        return $this->hasMany(ClientProject::class, ['cp_project_id' => 'id']);
    }

    /**
     * Gets query for [[CpClients]].
     *
     * @return ActiveQuery
     */
    public function getCpClients(): ActiveQuery
    {
        return $this->hasMany(Client::class, ['id' => 'cp_client_id'])->viaTable('client_project', ['cp_project_id' => 'id']);
    }

    /**
     * Gets query for [[DepartmentEmailProjects]].
     *
     * @return ActiveQuery
     */
    public function getDepartmentEmailProjects(): ActiveQuery
    {
        return $this->hasMany(DepartmentEmailProject::class, ['dep_project_id' => 'id']);
    }

    /**
     * Gets query for [[DepartmentPhoneProjects]].
     *
     * @return ActiveQuery
     */
    public function getDepartmentPhoneProjects(): ActiveQuery
    {
        return $this->hasMany(DepartmentPhoneProject::class, ['dpp_project_id' => 'id']);
    }

    /**
     * Gets query for [[EmailUnsubscribes]].
     *
     * @return ActiveQuery
     */
    public function getEmailUnsubscribes(): ActiveQuery
    {
        return $this->hasMany(EmailUnsubscribe::class, ['eu_project_id' => 'id']);
    }

    /**
     * Gets query for [[Emails]].
     *
     * @return ActiveQuery
     */
    public function getEmails(): ActiveQuery
    {
        return $this->hasMany(Email::class, ['e_project_id' => 'id']);
    }

    /**
     * Gets query for [[EmployeeContactInfos]].
     *
     * @return ActiveQuery
     */
    public function getEmployeeContactInfos(): ActiveQuery
    {
        return $this->hasMany(EmployeeContactInfo::class, ['project_id' => 'id']);
    }

    /**
     * Gets query for [[Leads]].
     *
     * @return ActiveQuery
     */
    public function getLeads(): ActiveQuery
    {
        return $this->hasMany(Lead::class, ['project_id' => 'id']);
    }

    /**
     * Gets query for [[PUpdateUser]].
     *
     * @return ActiveQuery
     */
    public function getPUpdateUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'p_update_user_id']);
    }

    /**
     * Gets query for [[PhoneLines]].
     *
     * @return ActiveQuery
     */
    public function getPhoneLines(): ActiveQuery
    {
        return $this->hasMany(PhoneLine::class, ['line_project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectEmailTemplates]].
     *
     * @return ActiveQuery
     */
    public function getProjectEmailTemplates(): ActiveQuery
    {
        return $this->hasMany(ProjectEmailTemplate::class, ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectEmployeeAccesses]].
     *
     * @return ActiveQuery
     */
    public function getProjectEmployeeAccesses(): ActiveQuery
    {
        return $this->hasMany(ProjectEmployeeAccess::class, ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectWeight]].
     *
     * @return ActiveQuery
     */
    public function getProjectWeight(): ActiveQuery
    {
        return $this->hasOne(ProjectWeight::class, ['pw_project_id' => 'id']);
    }

    /**
     * Gets query for [[QaTasks]].
     *
     * @return ActiveQuery
     */
    public function getQaTasks(): ActiveQuery
    {
        return $this->hasMany(QaTask::class, ['t_project_id' => 'id']);
    }

    /**
     * Gets query for [[Sms]].
     *
     * @return ActiveQuery
     */
    public function getSms(): ActiveQuery
    {
        return $this->hasMany(Sms::class, ['s_project_id' => 'id']);
    }

    /**
     * Gets query for [[SmsDistributionLists]].
     *
     * @return ActiveQuery
     */
    public function getSmsDistributionLists(): ActiveQuery
    {
        return $this->hasMany(SmsDistributionList::class, ['sdl_project_id' => 'id']);
    }

    /**
     * Gets query for [[Sources]].
     *
     * @return ActiveQuery
     */
    public function getSources(): ActiveQuery
    {
        return $this->hasMany(Sources::class, ['project_id' => 'id']);
    }

    /**
     * Gets query for [[UppUsers]].
     *
     * @return ActiveQuery
     */
    public function getUppUsers(): ActiveQuery
    {
        return $this->hasMany(Employee::class, ['id' => 'upp_user_id'])->viaTable('user_project_params', ['upp_project_id' => 'id']);
    }

    /**
     * Gets query for [[UserProjectParams]].
     *
     * @return ActiveQuery
     */
    public function getUserProjectParams(): ActiveQuery
    {
        return $this->hasMany(UserProjectParams::class, ['upp_project_id' => 'id']);
    }

    /**
     * Gets query for [[VisitorLogs]].
     *
     * @return ActiveQuery
     */
    public function getVisitorLogs(): ActiveQuery
    {
        return $this->hasMany(VisitorLog::class, ['vl_project_id' => 'id']);
    }
}
