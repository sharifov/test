<?php

namespace sales\model\clientAccount\form;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Currency;
use common\models\Language;
use common\models\Project;
use sales\model\clientAccount\entity\ClientAccount;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;

/**
 * Class ClientAccountUpdateApiForm
 * @property int|null $project_id
 * @property string $uuid
 * @property int $hid
 * @property string $username
 * @property string $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $nationality_country_code
 * @property string|null $dob
 * @property int|null $gender
 * @property string|null $phone
 * @property int|null $subscription
 * @property string|null $language_id
 * @property string|null $currency_code
 * @property string|null $timezone
 * @property string|null $created_ip
 * @property int|null $enabled
 * @property string|null $origin_created_dt
 * @property string|null $origin_updated_dt
 * @property string|null $email
 */
class ClientAccountUpdateApiForm extends Model
{
    public $project_id;
    public $uuid;
    public $hid;
    public $username;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $nationality_country_code;
    public $dob;
    public $gender;
    public $phone;
    public $subscription;
    public $language_id;
    public $currency_code;
    public $timezone;
    public $created_ip;
    public $enabled;
    public $origin_created_dt;
    public $origin_updated_dt;
    public $email;

    public function rules(): array
    {
        return [
            ['created_ip', 'string', 'max' => 40],

            ['currency_code', 'string', 'max' => 3],
            ['currency_code', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => ['currency_code' => 'cur_code'],
                'skipOnError' => true, 'skipOnEmpty' => true],

            ['enabled', 'boolean'],

            ['first_name', 'string', 'max' => 100],

            ['gender', 'integer'],
            ['gender', 'in', 'range' => array_keys(ClientAccount::GENDER_LIST)],

            ['hid', 'integer'],

            ['language_id', 'string', 'max' => 5],
            ['language_id', 'exist', 'targetClass' => Language::class, 'targetAttribute' => ['language_id' => 'language_id'],
                'skipOnError' => true, 'skipOnEmpty' => true],

            ['last_name', 'string', 'max' => 100],

            ['middle_name', 'string', 'max' => 100],

            ['nationality_country_code', 'string', 'max' => 2],

            ['phone', 'string', 'max' => 20],
            ['phone', PhoneInputValidator::class],

            ['project_id', 'integer'],
            ['project_id', 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id'],
                'skipOnError' => true, 'skipOnEmpty' => true],

            ['subscription', 'boolean'],

            ['timezone', 'string', 'max' => 50],

            ['username', 'string', 'max' => 100],

            'uuid_required' => ['uuid', 'required'],
            ['uuid', 'string', 'max' => 36],
            ['uuid', UuidValidator::class],

            ['origin_created_dt', 'safe'],

            ['origin_updated_dt', 'safe'],

            ['dob', 'safe'],

            ['email', 'string', 'max' => 100],
            ['email', 'email'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
