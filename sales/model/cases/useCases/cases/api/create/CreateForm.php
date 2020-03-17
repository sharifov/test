<?php

namespace sales\model\cases\useCases\cases\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\entities\cases\CasesCategory;
use sales\services\client\InternalPhoneValidator;
use sales\yii\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class CreateForm
 *
 * @property string $email
 * @property string $phone
 * @property string $category
 * @property string $order_uid
 * @property array $order_info
 * @property int $project_id
 * @property string|null $subject
 * @property string|null $description
 */
class CreateForm extends Model
{
    public $email;
    public $phone;
    public $category;
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
            ['email', 'required'],
            ['email', 'email'],

            ['phone', 'required'],
            ['phone', 'string', 'max' => 100],
            ['phone', PhoneInputValidator::class],
            ['phone', 'filter', 'filter' => static function ($value) {
                return str_replace(['-', ' '], '', trim($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['phone', InternalPhoneValidator::class, 'skipOnError' => true, 'skipOnEmpty' => true],

            ['category', 'required'],
            ['category', 'exist', 'targetClass' => CasesCategory::class, 'targetAttribute' => ['category' => 'cc_key']],

            ['order_uid', 'required'],
            ['order_uid', 'string', 'min' => '5', 'max' => 7],
            ['order_uid', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],

            ['subject', 'default', 'value' => null],
            ['subject', 'string', 'max' => 255],

            ['description', 'default', 'value' => null],
            ['description', 'string'],

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
            $this->email,
            $this->phone,
            $this->category,
            $this->order_uid,
            $this->order_info,
            $this->project_id,
            $this->subject,
            $this->description
        );
    }
}
