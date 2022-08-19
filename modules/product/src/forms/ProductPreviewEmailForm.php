<?php

namespace modules\product\src\forms;

use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use src\entities\cases\Cases;
use frontend\models\EmailPreviewFromInterface;

class ProductPreviewEmailForm extends \yii\base\Model implements EmailPreviewFromInterface
{
    public $case_id;

    public $email_from;
    public $email_to;
    public $email_from_name;
    public $email_to_name;
    public $email_subject;
    public $email_message;
    public $email_tpl_id;
    public $user_id;
    public $language_id;
    public $content_data = [];

    public $is_send;
    public $keyCache;

    public function __construct(array $data = [], $config = [])
    {
        parent::__construct($config);

        if ($data) {
            $this->case_id = $data['email_data']['case']['id'] ?? null;
            $this->email_from = $data['email_from'] ?? null;
            $this->email_to = $data['email_to'] ?? null;
            $this->email_from_name = $data['email_from_name'] ?? null;
            $this->email_to_name = $data['email_to_name'] ?? null;
            $this->email_subject = $data['email_subject'] ?? null;
            $this->email_message = $data['email_body_html'] ?? null;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['case_id', 'email_from', 'email_to', 'email_message', 'email_subject'], 'required'],
            [['email_subject', 'email_message'], 'trim'],
            [['email_to', 'email_from'], 'email'],
            [['email_tpl_id', 'case_id'], 'integer'],
            [['email_message'], 'string'],
            [['email_subject'], 'string', 'max' => 200, 'min' => 5],
            [['email_from_name', 'email_to_name'], 'string', 'max' => 50],
            [['language_id'], 'string', 'max' => 5],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['user_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['language_id' => 'language_id']],
            [['case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['case_id' => 'cs_id']],
            [['email_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['email_tpl_id' => 'etp_id']],
            ['keyCache', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'case_id'         => 'Case Id',
            'email_to'        => 'Email To',
            'email_from'      => 'Email From',
            'email_to_name'   => 'Email To Name',
            'email_from_name' => 'Email From Name',
            'email_tpl_id'    => 'Email Template',
            'email_message'   => 'Email Message',
            'email_subject'   => 'Subject',
            'language_id'     => 'Language',
            'user_id'         => 'Agent ID',
        ];
    }

    public function getEmailFrom(): string
    {
        return $this->email_from;
    }

    public function getEmailTo(): string
    {
        return $this->email_to;
    }

    public function getEmailFromName(): ?string
    {
        return $this->email_from_name;
    }

    public function getEmailToName(): ?string
    {
        return $this->email_to_name;
    }

    public function getEmailSubject(): ?string
    {
        return $this->email_subject;
    }

    public function getEmailMessage(): ?string
    {
        return $this->email_message;
    }

    /**
     * @return int|null
     */
    public function getEmailTemplateId(): ?int
    {
        return $this->email_tpl_id ?: null;
    }

    public function getLanguageId(): ?string
    {
        return $this->language_id;
    }

    public function countLettersInEmailMessage(): int
    {
        return !empty($this->email_message) ? mb_strlen($this->email_message) : 0;
    }
}
