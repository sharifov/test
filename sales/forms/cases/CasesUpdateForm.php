<?php

namespace sales\forms\cases;

use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class CasesUpdateForm
 *
 * @property string $category
 * @property string $caseGid
 */
class CasesUpdateForm extends Model
{
    public $category;
    public $caseGid;

    /**
     * CasesUpdateForm constructor.
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
            ['category', 'required'],
            [['category'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'category_id' => 'Category',
        ];
    }
}