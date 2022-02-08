<?php

namespace frontend\models;

use common\components\validators\IsArrayValidator;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use modules\fileStorage\src\entity\fileCase\FileCaseQuery;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use src\entities\cases\Cases;
use src\model\email\useCase\send\fromCase\AbacEmailList;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class CasePreviewEmailForm
 * @package frontend\models
 *
 * @property integer $e_case_id
 * @property string $e_email_from
 * @property string $e_email_to
 * @property string $e_email_from_name
 * @property string $e_email_to_name
 * @property string $e_email_subject
 * @property string $e_email_subject_origin
 * @property string $e_email_message
 * @property bool $e_email_message_edited
 * @property integer $e_email_tpl_id
 * @property integer $e_user_id
 * @property string $e_language_id
 * @property array $e_content_data
 *
 * @property string $e_quote_list
 * @property string $coupon_list
 * @property boolean $is_send
 * @property string $keyCache
 *
 * @property array $files
 *
 * @property AbacEmailList $emailFromList
 *
 */

class CasePreviewEmailForm extends Model
{
    public $e_case_id;
    public $e_email_from;
    public $e_email_to;
    public $e_email_from_name;
    public $e_email_to_name;
    public $e_email_subject;
    public $e_email_subject_origin;
    public $e_email_message;
    public $e_email_message_edited;
    public $e_email_tpl_id;
    public $e_user_id;
    public $e_language_id;
    public $e_content_data = [];

    public $e_quote_list;

    public $is_send;

    public $coupon_list;
    public $keyCache;

    public $files;

    private $fileList;

    private AbacEmailList $emailFromList;

    public function __construct(AbacEmailList $emailFromList, $config = [])
    {
        parent::__construct($config);
        $this->emailFromList = $emailFromList;
//        if ($data) {
//            $this->e_case_id = $data['email_data']['case']['id'] ?? null;
//            $this->e_email_from = $data['email_from'] ?? null;
//            $this->e_email_to = $data['email_to'] ?? null;
//            $this->e_email_from_name = $data['email_from_name'] ?? null;
//            $this->e_email_to_name = $data['email_to_name'] ?? null;
//            $this->e_email_subject = $data['email_subject'] ?? null;
//            $this->e_email_message = $data['email_body_html'] ?? null;
//        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['e_case_id', 'e_email_from', 'e_email_to', 'e_email_message', 'e_email_subject'], 'required'],
            [['e_email_subject', 'e_email_message'], 'trim'],
            //[['e_type_id'], 'validateType'],
            [['e_email_to', 'e_email_from'], 'email'],
            [['e_email_tpl_id', 'e_case_id'], 'integer'],
            [['e_email_message', 'e_email_subject_origin', 'e_quote_list', 'coupon_list'], 'string'],
            [['e_email_message_edited'], 'boolean'],
            [['e_email_message_edited'], 'default', 'value' => false],
            [['e_email_subject'], 'string', 'max' => 200, 'min' => 5],
            [['e_email_from_name', 'e_email_to_name'], 'string', 'max' => 50],
            [['e_language_id'], 'string', 'max' => 5],
            [['e_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_user_id' => 'id']],
            [['e_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['e_language_id' => 'language_id']],
            [['e_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['e_case_id' => 'cs_id']],
            [['e_email_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['e_email_tpl_id' => 'etp_id']],
            ['keyCache', 'safe'],

            ['files', IsArrayValidator::class],
            ['files', 'each', 'rule' => ['integer'], 'skipOnError' => true, 'skipOnEmpty' => true],
            ['files', 'each', 'rule' => ['in', 'range' => array_keys($this->getFileList()), 'skipOnEmpty' => true], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['e_email_from', function () {
                if (!$this->emailFromList->isExist($this->e_email_from)) {
                    $this->addError('e_email_from', 'Email From is invalid');
                }
            }, 'skipOnError' => true, 'skipOnEmpty' => true],
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
            'e_case_id'         => 'Case Id',
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
        $fileCaseQuery = FileCaseQuery::getListByCase($this->e_case_id);
        return $this->fileList = ArrayHelper::map($fileCaseQuery, 'id', 'name');
    }

    public function getFilesPath(): array
    {
        $files = [];
        if (!$this->files) {
            return $files;
        }
        foreach ($this->files as $fileId) {
            if ($fileStorage = FileStorage::findOne(['fs_id' => $fileId])) {
                $files[] = new \modules\fileStorage\src\services\url\FileInfo(
                    $fileStorage->fs_name,
                    $fileStorage->fs_path,
                    $fileStorage->fs_uid,
                    $fileStorage->fs_title,
                    null
                );
            }
        }
        return $files;
    }

    public function attachCount(): int
    {
        return !empty($this->files) ? count($this->files) : 0;
    }

    public function isMessageEdited(): bool
    {
        return (bool)$this->e_email_message_edited;
    }

    public function isSubjectEdited(): bool
    {
        return strcmp($this->e_email_subject_origin, $this->e_email_subject) !== 0;
    }
}
