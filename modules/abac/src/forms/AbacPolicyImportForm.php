<?php

namespace modules\abac\src\forms;

use modules\abac\src\FeatureFlagService;
use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * This is the AbacPolicyImportForm class for table "abac_policy".
 *
 * @property UploadedFile $importFile
 * @property int $import_type_id
 * @property array $ids
 */
class AbacPolicyImportForm extends Model
{
    public const SCENARIO_FILE = 'scenarioFile';
    public const SCENARIO_IDS = 'scenarioIDs';

    public $importFile;
    public $import_type_id;
    public $ids;
    public $import_ids;

    public const IMPORT_TYPE_ADD_REPLACE    = 1;
    public const IMPORT_TYPE_ADD            = 2;
    public const IMPORT_TYPE_REPLACE        = 3;

    public const IMPORT_TYPE_LIST = [
        self::IMPORT_TYPE_ADD_REPLACE   => 'Add & Replace',
        self::IMPORT_TYPE_ADD           => 'Only Add new',
        self::IMPORT_TYPE_REPLACE       => 'Only Replace',
    ];


    public const ACT_CREATE                 = 1;
    public const ACT_UPDATE                 = 2;
    public const ACT_EXISTS                 = 3;
    public const ACT_ERROR                  = 5;

    public const ACT_LIST = [
        self::ACT_CREATE    => 'Add',
        self::ACT_UPDATE    => 'Update',
        self::ACT_EXISTS    => 'Exists',
        self::ACT_ERROR     => 'Error',
    ];

    public const ACT_CLASS_LIST = [
        self::ACT_CREATE    => 'success',
        self::ACT_UPDATE    => 'warning',
        self::ACT_EXISTS    => 'info',
        self::ACT_ERROR     => 'danger',
    ];

    public function rules()
    {
        return [
            [['importFile'], 'required', 'on' => self::SCENARIO_FILE],
            [['import_ids'], 'required', 'on' => self::SCENARIO_IDS],
            [['importFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'json', 'on' => self::SCENARIO_FILE],
            [['import_type_id'], 'integer'],
            [['import_ids'], 'validateIds'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateIds($attribute, $params): void
    {
        if ($this->import_ids) {
            $data = @json_decode($this->import_ids, true);
            if (empty($data) || !is_array($data)) {
                $this->addError($attribute, 'Invalid Ids list');
            } else {
                $this->ids = $data;
            }
        } else {
            $this->addError($attribute, 'Ids list is empty');
        }
    }

    public function upload()
    {
        $fileName = $this->importFile->baseName . '.' . $this->importFile->extension;
        $filePath = Yii::getAlias('@runtime/' . $fileName);
        if ($this->importFile->saveAs($filePath)) {
            return $filePath;
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'importFile' => 'Import File',
            'import_type_id' => 'Import Type',
            'import_ids' => 'Import IDs',
        ];
    }

    /**
     * @param $actionId
     * @return string
     */
    public static function getActionName($actionId): string
    {
        return self::ACT_LIST[$actionId] ?? '-';
    }

    /**
     * @param $actionId
     * @return string
     */
    public static function getActionClass($actionId): string
    {
        return self::ACT_CLASS_LIST[$actionId] ?? 'default';
    }

    /**
     * @param $actionId
     * @return string
     */
    public static function getActionTitle($actionId): string
    {
        return '<span class="badge badge-' . (self::getActionClass($actionId)) . '">' . Html::encode(self::getActionName($actionId)) . '</span>';
    }
}
