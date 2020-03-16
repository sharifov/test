<?php

namespace sales\model\cases\useCases\cases\updateInfo;

use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class CasesUpdateForm
 *
 * @property Cases $case
 * @property string $category
 * @property string $subject
 * @property string $description
 * @property string|null $orderUid
 * @property array $categoryList
 */
class UpdateInfoForm extends Model
{
    public $category;
    public $subject;
    public $description;
    public $orderUid;

    private $case;
    private $categoryList = [];

    public function __construct(
        Cases $case,
        array $categoryList,
        $config = [])
    {
        parent::__construct($config);
        $this->case = $case;
        $this->category = $case->cs_category;
        $this->orderUid = $case->cs_order_uid;
        $this->subject = $case->cs_subject;
        $this->description = $case->cs_description;
        $this->categoryList = $categoryList;
    }

    public function rules(): array
    {
        return [
            ['category', 'required'],
            ['category', 'string', 'max' => 100],
            ['category', 'in', 'range' => array_keys($this->getCategoryList())],

            ['subject', 'string', 'max' => 200],

            ['description', 'string'],

            ['orderUid', 'default', 'value' => null],
            ['orderUid', 'string', 'min'  => '7', 'max' => 7],
            ['orderUid', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'category' => 'Category',
            'subject' => 'Subject',
            'description' => 'Description',
            'orderUid' => 'Booking ID',
        ];
    }

    public function getCategoryList(): array
    {
        return $this->categoryList;
    }

    public function getCaseGid(): string
    {
        return $this->case->cs_gid;
    }

    public function getDto(): Command
    {
        return new Command(
            $this->case->cs_id,
            $this->category,
            $this->subject,
            $this->description,
            $this->orderUid
        );
    }
}
