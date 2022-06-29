<?php

namespace src\entities\email;

use Yii;
use common\models\Language;
use common\models\EmailTemplateType;
use src\model\BaseActiveRecord;

/**
 * This is the model class for table "email_params".
 *
 * @property int $ep_id
 * @property int|null $ep_email_id
 * @property int|null $ep_template_type_id
 * @property string|null $ep_language_id
 * @property int $ep_priority
 *
 * @property Email $email
 * @property Language $language
 * @property EmailTemplateType $templateType
 */
class EmailParams extends BaseActiveRecord
{
    public function rules(): array
    {
        return [
            ['ep_email_id', 'integer'],
            ['ep_email_id', 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['ep_email_id' => 'e_id']],

            ['ep_language_id', 'string', 'max' => 5],
            ['ep_language_id', 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['ep_language_id' => 'language_id']],

            ['ep_priority', 'integer'],

            ['ep_template_type_id', 'integer'],
            //['ep_template_type_id', 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['ep_template_type_id' => 'etp_id']],
        ];
    }

    public function getEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Email::class, ['e_id' => 'ep_email_id']);
    }

    public function getLanguage(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'ep_language_id']);
    }

    public function getTemplateType(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailTemplateType::class, ['etp_id' => 'ep_template_type_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ep_id' => 'ID',
            'ep_email_id' => 'Email ID',
            'ep_template_type_id' => 'Template Type ID',
            'ep_language_id' => 'Language ID',
            'ep_priority' => 'Priority',
        ];
    }

    public static function tableName(): string
    {
        return 'email_params';
    }
}
