<?php

namespace sales\model\call\useCase\conference\create;

use common\models\Call;
use common\models\Lead;
use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class CreateCallForm
 *
 * @property string $caller
 * @property string $called
 * @property string $from
 * @property int $user_id
 * @property int $project_id
 * @property int $lead_id
 * @property int $case_id
 * @property int $source_type_id
 */
class CreateCallForm extends Model
{
    public $caller;
    public $called;
    public $from;
    public $user_id;
    public $project_id;
    public $lead_id;
    public $case_id;
    public $source_type_id;

    public function rules(): array
    {
        return [
            ['caller', 'required'],
            ['caller', 'string'],

            ['called', 'required'],
            ['called', 'string'],

            ['from', 'required'],
            ['from', 'string'],

            ['user_id', 'required'],
            ['user_id', 'integer'],
            ['user_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

//            ['project_id', 'required'],
            ['project_id', 'integer'],
            ['project_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['lead_id', 'integer'],
            ['lead_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],

            ['case_id', 'integer'],
            ['case_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['case_id' => 'cs_id']],

            ['source_type_id', 'integer'],
            ['source_type_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['source_type_id', 'in', 'range' => array_keys(Call::SOURCE_LIST)],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
