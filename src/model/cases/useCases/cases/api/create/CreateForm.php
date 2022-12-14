<?php

namespace src\model\cases\useCases\cases\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Project;
use src\entities\cases\CaseCategory;
use src\repositories\NotFoundException;
use src\services\client\InternalPhoneValidator;
use common\components\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class CreateForm
 *
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property int $category_id
 * @property string|null $order_uid
 * @property array $order_info
 * @property array $experiments
 * @property int|null $project_id
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $project_key
 * @property string|null $chat_visitor_id
 * @property string|null $contact_name
 * @property $category_key
 */
class CreateForm extends Model
{
    public $contact_email;
    public $contact_phone;
    public $category_id;
    public $order_uid;
    public $order_info;
    public $experiments;
    public $project_id;
    public $subject;
    public $description;
    public $project_key;
    public $chat_visitor_id;
    public $contact_name;
    public $category_key;

    public function formName(): string
    {
        return '';
    }

    /**
     * CreateForm constructor.
     * @param int|null $project_id
     * @param array $config
     */
    public function __construct(?int $project_id, $config = [])
    {
        parent::__construct($config);
        $this->project_id = $project_id;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
//            ['contact_email', 'required'],
            ['contact_email', 'email'],
            ['contact_phone', 'string', 'max' => 160],

//            ['contact_phone', 'required'],
            ['contact_phone', 'string', 'max' => 20],
            ['contact_phone', PhoneInputValidator::class],
            ['contact_phone', 'filter', 'filter' => static function ($value) {
                return str_replace(['-', ' '], '', trim($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['contact_phone', InternalPhoneValidator::class,
                'skipOnError' => true, 'skipOnEmpty' => true,
                'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],

            ['category_id', 'required', 'when' => function ($model) {
                return empty($model->category_key);
            }],
            ['category_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true,],
            ['category_id', 'exist', 'targetClass' => CaseCategory::class,
                'targetAttribute' => ['category_id' => 'cc_id'], 'skipOnEmpty' => true, 'skipOnError' => true,],

            ['category_key', 'required', 'when' => function ($model) {
                return empty($model->category_id);
            }],
            ['category_key', 'string', 'max' => 50],
            ['category_key', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true, 'skipOnError' => true,],
            ['category_key', 'exist', 'targetClass' => CaseCategory::class,
                'targetAttribute' => ['category_key' => 'cc_key'], 'skipOnEmpty' => true, 'skipOnError' => true,],

            ['order_uid', 'string', 'min' => 5, 'max' => 7],
            ['order_uid', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],

            ['chat_visitor_id', 'string', 'max' => 50],
            ['contact_name', 'string', 'max' => 100],

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

            ['experiments', IsArrayValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['project_key', 'default', 'value' => null],
            ['project_key', 'string', 'max' => 100],
            [['project_key'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class,
                'targetAttribute' => ['project_key' => 'project_key']]
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        /** @fflag FFlag::FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION, Exclude create api create case validation */
        if (!\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION)) {
            if (!$this->validateRequired()) {
                return false;
            }
        }

        return parent::validate($attributeNames, $clearErrors); // TODO: Change the autogenerated stub
    }

    /**
     * @return Command
     */
    public function getDto(): Command
    {
        if (empty($this->project_id) && $this->project_key) {
            $this->project_id = Project::find()
                ->select('id')
                ->where(['project_key' => $this->project_key])
                ->scalar();
        }

        if (empty($this->project_key) && $this->project_id) {
            $this->project_key = Project::find()
                ->select('api_key')
                ->where(['id' => $this->project_id])
                ->scalar();
        }

        return new Command(
            $this->contact_email,
            $this->contact_phone,
            $this->category_id,
            $this->order_uid,
            $this->order_info,
            $this->project_id,
            $this->subject,
            $this->description,
            $this->project_key,
            $this->chat_visitor_id,
            $this->contact_name
        );
    }

    public function validateRequired(): bool
    {
        if (!$this->contact_email && !$this->contact_phone && !$this->chat_visitor_id && !$this->order_uid) {
            $this->addError('contact_email', 'Phone or Email or Chat_visitor_id or Order_uid required');
            return false;
        }
        return true;
    }

    public function getCaseCategory(): CaseCategory
    {
        if ($this->category_key && $caseCategory = CaseCategory::findOne(['cc_key' => $this->category_key])) {
            return $caseCategory;
        }
        if ($this->category_id && $caseCategory = CaseCategory::findOne(['cc_id' => $this->category_id])) {
            return $caseCategory;
        }
        throw new NotFoundException('CaseCategory not found');
    }
}
