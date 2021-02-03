<?php

namespace frontend\models;

use common\components\validators\IsArrayValidator;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use modules\fileStorage\src\entity\fileLead\FileLeadQuery;
use yii\base\Model;

/**
 * Class LeadPreviewEmailForm
 * @package frontend\models
 *
 * @property integer $e_lead_id
 * @property string $e_email_from
 * @property string $e_email_to
 * @property string $e_email_from_name
 * @property string $e_email_to_name
 * @property string $e_email_subject
 * @property string $e_email_message
 * @property integer $e_email_tpl_id
 * @property integer $e_user_id
 * @property string $e_language_id
 * @property array $e_content_data
 *
 * @property string $e_quote_list
 * @property boolean $is_send
 * @property string $keyCache
 *
 * @property array $files
 *
 */

class LeadPreviewEmailForm extends Model
{
    public $e_lead_id;
    public $e_email_from;
    public $e_email_to;
    public $e_email_from_name;
    public $e_email_to_name;
    public $e_email_subject;
    public $e_email_message;
    public $e_email_tpl_id;
    public $e_user_id;
    public $e_language_id;
    public $e_content_data = [];

    public $e_quote_list;

    public $is_send;
    public $keyCache;

    public $files;

    private $fileList;
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['e_lead_id', 'e_email_from', 'e_email_to', 'e_email_message', 'e_email_subject'], 'required'],
            [['e_email_subject', 'e_email_message'], 'trim'],
            //[['e_type_id'], 'validateType'],
            [['e_email_to', 'e_email_from'], 'email'],
            [['e_email_tpl_id', 'e_lead_id'], 'integer'],
            [['e_email_message', 'e_quote_list'], 'string'],
            [['e_email_subject'], 'string', 'max' => 200, 'min' => 5],
            [['e_email_from_name', 'e_email_to_name'], 'string', 'max' => 50],
            [['e_language_id'], 'string', 'max' => 5],
            [['e_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_user_id' => 'id']],
            [['e_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['e_language_id' => 'language_id']],
            [['e_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['e_lead_id' => 'id']],
            [['e_email_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['e_email_tpl_id' => 'etp_id']],
            ['keyCache', 'safe'],

            ['files', IsArrayValidator::class],
            ['files', 'each', 'rule' => ['integer'], 'skipOnError' => true, 'skipOnEmpty' => true],
            ['files', 'each', 'rule' => ['in', 'range' => array_keys($this->getFileList())], 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    /*public function validateType($attribute, $params, $validator)
    {
        if ($this->$attribute == self::TYPE_SMS) {
            //if()   $this->addError($attribute, 'The country must be either "USA" or "Indonesia".');
        }
    }*/


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'e_lead_id'         => 'Lead Id',
            'e_email_to'        => 'Email To',
            'e_email_from'      => 'Email From',
            'e_email_to_name'   => 'Email To Name',
            'e_email_from_name' => 'Email From Name',
            'e_email_tpl_id'    => 'Email Template',
            'e_email_message'   => 'Email Message',
            'e_email_subject'   => 'Subject',
            'e_language_id'     => 'Language',
            'e_user_id'         => 'Agent ID',
        ];
    }

    public function getFileList(): array
    {
        if ($this->fileList !== null) {
            return $this->fileList;
        }
        $this->fileList = FileLeadQuery::getListByLead($this->e_lead_id);
        return $this->fileList;
    }

    public function getFilesPath(): array
    {
        $files = [];
        if (!$this->files) {
            return $files;
        }
        $availableFiles = $this->getFileList();
        foreach ($this->files as $fileId) {
            if (array_key_exists($fileId, $availableFiles)) {
                $files[] = new \modules\fileStorage\src\services\url\FileInfo(
                    $availableFiles[$fileId]['name'],
                    $availableFiles[$fileId]['path'],
                    $availableFiles[$fileId]['uid'],
                    $availableFiles[$fileId]['title'],
                    null
                );
            }
        }
        return $files;
    }
}
