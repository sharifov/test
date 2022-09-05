<?php

namespace src\model\cases\useCases\cases\api\create;

use common\models\Project;
use src\entities\cases\CaseCategory;
use src\repositories\NotFoundException;
use yii\base\Model;

/**
 * Class CreateMinifyForm
 *
 * @property int $category_id
 * @property int|null $project_id
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $project_key
 */
class CreateMinifyForm extends Model
{
    public ?int $category_id;
    public ?int $project_id;
    public ?string $subject;
    public ?string $description;
    public ?string $project_key;

    /**
     * CreateMinifyForm constructor.
     * @param int|null $project_id
     * @param array $config
     */
    public function __construct(?int $project_id, array $config = [])
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
            ['category_id', 'required'],
            ['category_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true,],
            ['category_id', 'exist', 'targetClass' => CaseCategory::class,
                'targetAttribute' => ['category_id' => 'cc_id'], 'skipOnEmpty' => true, 'skipOnError' => true,],

            ['project_key', 'required'],
            ['project_key', 'string', 'max' => 100],
            [['project_key'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class,
                'targetAttribute' => ['project_key' => 'api_key']],

            ['subject', 'default', 'value' => null],
            ['subject', 'string', 'max' => 255],

            ['description', 'default', 'value' => null],
            ['description', 'string', 'max' => 65000],
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @return CaseCategory
     */
    public function getCaseCategory(): CaseCategory
    {
        if ($this->category_id && $caseCategory = CaseCategory::findOne(['cc_id' => $this->category_id])) {
            return $caseCategory;
        }
        throw new NotFoundException('CaseCategory not found');
    }

    /**
     * @return MinifyCommand
     */
    public function getDto(): MinifyCommand
    {
        if (empty($this->project_id) && $this->project_key) {
            $this->project_id = Project::find()
                ->select('id')
                ->where(['project_key' => $this->project_key])
                ->scalar();
        }

        return new MinifyCommand(
            $this->category_id,
            $this->project_id,
            $this->subject,
            $this->description,
            true
        );
    }
}
