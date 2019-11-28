<?php

namespace common\models;

use common\models\local\ContactInfo;
use common\models\query\ProjectQuery;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\httpclient\CurlTransport;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string $name
 * @property string $link
 * @property string $api_key
 * @property string $contact_info
 * @property boolean $closed
 * @property string $last_update
 * @property string $custom_data
 *
 * @property Sources[] $sources
 * @property ContactInfo $contactInfo
 */
class Project extends \yii\db\ActiveRecord
{
    public $contactInfo;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contact_info','custom_data'], 'string'],
            [['closed'], 'boolean'],
            [['last_update'], 'safe'],
            [['name', 'link', 'api_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
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
            'last_update' => 'Last Update',
            'custom_data' => 'Custom Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSources()
    {
        return $this->hasMany(Sources::class, ['project_id' => 'id']);
    }

    public function afterFind()
    {
        $this->contactInfo = new ContactInfo();
        if (!empty($this->contact_info)) {
            $this->contactInfo->attributes = json_decode($this->contact_info, true);
        }
        parent::afterFind();
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
    public static function getList() : array
    {
        $data = self::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data,'id', 'name');
    }


    /**
     * @param int $user_id
     * @return array
     */
    public static function getListByUser(int $user_id = 0) : array
    {
        $data = ProjectEmployeeAccess::find()->select(['project_id'])->with('project')->where(['employee_id' => $user_id])->all();
        return ArrayHelper::map($data,'project_id', 'project.name');
    }




    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function synchronizationProjects() : array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'error' => false
        ];

        $projectsData = self::getProjectListBO();
        if($projectsData && $projectsData['data']) {
            if(isset($projectsData['data']['data']) && $projectsData['data']['data']) {
                foreach ($projectsData['data']['data'] as $projectItem) {
                    $pr = self::findOne($projectItem['id']);
                    if(!$pr) {
                        $pr = new self();
                        $pr->id = $projectItem['id'];
                        $data['created'][] = $projectItem['id'];
                        //$pr->custom_data = @json_encode(['name' => $projectItem['name'], 'phone' => '', 'email' => '']);

                    } else {
                        $data['updated'][] = $projectItem['id'];
                    }

                    $pr->attributes = $projectItem;

                    $pr->name = $projectItem['name'];
                    $pr->link = $projectItem['link'];
                    $pr->closed = (bool) $projectItem['closed'];
                    $pr->last_update = date('Y-m-d H:i:s');
                    /*if(isset(Yii::$app->user) && Yii::$app->user->id) {
                        $pr->pr_updated_user_id = Yii::$app->user->id;
                    }*/
                    if(!$pr->save()) {
                        Yii::error(VarDumper::dumpAsString($pr->errors), 'Project:synchronizationProjects:Project:save');
                    } else {

                        if($projectItem['sources']) {
                            foreach ($projectItem['sources'] as $sourceId => $sourceAttr) {
                                $source = Sources::findOne(['id' => $sourceId]);
                                if (!$source) {
                                    $source = new Sources();
                                    $source->project_id = $pr->id;
                                }

                                $source->attributes = $sourceAttr;
                                if (!$source->save()) {
                                    Yii::error(VarDumper::dumpAsString($source->errors), 'Project:synchronizationProjects:Sources:save');
                                }
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
            $out['error'] = print_r($response->data, true);
        }

        return $out;
    }

    /**
     * @param string $apiKey
     * @param string $version
     * @return string
     */
    private static function getSignatureBO(string $apiKey = '', string $version = '') : string
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
}
