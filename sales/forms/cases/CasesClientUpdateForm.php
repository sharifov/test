<?php

namespace sales\forms\cases;

use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class CasesClientUpdateForm
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $caseGid
 */
class CasesClientUpdateForm extends Model
{
    public $first_name;
    public $last_name;
    public $middle_name;

    public $caseGid;

    /**
     * CasesChangeStatusForm constructor.
     * @param Cases $case
     * @param array $config
     */
    public function __construct(Cases $case, $config = [])
    {
        parent::__construct($config);
        $this->caseGid = $case->cs_gid;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['first_name', 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'min' => 3, 'max' => 100],
            [['first_name', 'last_name', 'middle_name'], 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            [['first_name', 'last_name', 'middle_name'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'middle_name' => 'Middle name',
        ];
    }
}