<?php

namespace sales\model\cases\useCases\cases\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\entities\cases\CaseCategory;
use sales\services\client\InternalPhoneValidator;
use sales\yii\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class CreateForm
 *
 * @property string $contact_email
 * @property string $contact_phone
 * @property int $category_id
 * @property string $order_uid
 * @property array $order_info
 * @property int $project_id
 * @property string|null $subject
 * @property string|null $description
 */
class CreateForm extends Model
{
    public $contact_email;
    public $contact_phone;
    public $category_id;
    public $order_uid;
    public $order_info;
    public $project_id;
    public $subject;
    public $description;

    public function formName(): string
    {
        return '';
    }

    public function __construct(int $project_id, $config = [])
    {
        parent::__construct($config);
        $this->project_id = $project_id;
    }

    public function rules(): array
    {
        return [
            ['contact_email', 'required'],
            ['contact_email', 'email'],
            ['contact_phone', 'string', 'max' => 160],

            ['contact_phone', 'required'],
            ['contact_phone', 'string', 'max' => 20],
            ['contact_phone', PhoneInputValidator::class],
            ['contact_phone', 'filter', 'filter' => static function ($value) {
                return str_replace(['-', ' '], '', trim($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['contact_phone', InternalPhoneValidator::class, 'skipOnError' => true, 'skipOnEmpty' => true],

            ['category_id', 'required'],
            ['category_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['category_id', 'exist', 'targetClass' => CaseCategory::class, 'targetAttribute' => ['category_id' => 'cc_id']],

            ['order_uid', 'required'],
            ['order_uid', 'string', 'min' => 5, 'max' => 7],
            ['order_uid', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],

            ['subject', 'default', 'value' => null],
            ['subject', 'string', 'max' => 255],

            ['description', 'default', 'value' => null],
            ['description', 'string', 'max' => 65000],

            ['order_info', 'default', 'value' => []],
            ['order_info', IsArrayValidator::class],
            ['order_info', function () {
                foreach ($this->order_info as $key => $value) {
                    if (!is_string($key) || !is_string($value)) {
                        $this->addError('order_info', 'Key and Value must be string.');
                        return;
                    }
                }
            }, 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function getDto(): Command
    {
        return new Command(
            $this->contact_email,
            $this->contact_phone,
            $this->category_id,
            $this->order_uid,
            $this->order_info,
            $this->project_id,
            $this->subject,
            $this->description
        );
    }
}
