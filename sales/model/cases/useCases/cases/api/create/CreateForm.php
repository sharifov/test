<?php

namespace sales\model\cases\useCases\cases\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Project;
use sales\entities\cases\CaseCategory;
use sales\services\client\InternalPhoneValidator;
use common\components\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class CreateForm
 *
 * @property string $contact_email
 * @property string $contact_phone
 * @property int $category_id
 * @property string $order_uid
 * @property array $order_info
 * @property int|null $project_id
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $project_key
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
    public $project_key;

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
            ['contact_email', 'required'],
            ['contact_email', 'email'],
            ['contact_phone', 'string', 'max' => 160],

            ['contact_phone', 'required'],
            ['contact_phone', 'string', 'max' => 20],
            ['contact_phone', PhoneInputValidator::class],
            ['contact_phone', 'filter', 'filter' => static function ($value) {
                return str_replace(['-', ' '], '', trim($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['contact_phone', InternalPhoneValidator::class,
                'skipOnError' => true, 'skipOnEmpty' => true,
                'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],

            ['category_id', 'required'],
            ['category_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['category_id', 'exist', 'targetClass' => CaseCategory::class,
                'targetAttribute' => ['category_id' => 'cc_id']],

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

            ['project_key', 'default', 'value' => null],
            ['project_key', 'string', 'max' => 100],
            [['project_key'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class,
                'targetAttribute' => ['project_key' => 'project_key']]
        ];
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

        return new Command(
            $this->contact_email,
            $this->contact_phone,
            $this->category_id,
            $this->order_uid,
            $this->order_info,
            $this->project_id,
            $this->subject,
            $this->description,
            $this->project_key
        );
    }
}
