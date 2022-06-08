<?php

namespace src\services\system;

use common\models\DateSensitiveView;
use src\helpers\setting\SettingHelper;
use yii\db\Connection;

/**
 * Class DbViewCryptService
 *
 * @property Connection $db
 * @property string $tableName
 * @property array $cryptColumns
 * @property string $viewName
 * @property string $settingBlockEncryptionMode
 * @property string $settingKeyStr
 * @property string $settingInitVector
 */
class DbViewCryptService
{
    public const VIEW_SEPARATOR = '_';

    private ?string $settingBlockEncryptionMode;
    private ?string $settingKeyStr;
    private ?string $settingInitVector;

    private Connection $db;
    private string $tableName;
    private array $cryptColumns;
    private string $viewName;

    /**
     * @param Connection $db
     * @param string $tableName
     * @param array $cryptColumns
     */
    public function __construct(Connection $db, string $tableName, string $postFix, array $cryptColumns)
    {
        $this->db = $db;
        $this->tableName = $tableName;
        $this->cryptColumns = $cryptColumns;
        $this->viewName = $this->tableName . self::VIEW_SEPARATOR . $postFix;
        $this->settingBlockEncryptionMode = SettingHelper::getDbCryptBlockEncryptionMode();
        $this->settingKeyStr = hash('sha256', SettingHelper::getDbCryptKeyStr());
        $this->settingInitVector = SettingHelper::getDbCryptInitVector();
    }

    public function getCreateSql(): string
    {
        return $this->getBlockEncryptionModeSql() .
            "CREATE VIEW {$this->viewName}
            AS
            SELECT 
                {$this->getPreparedCreateColumns()}
            FROM
                `{$this->tableName}`; ";
    }

    public function getDropSql(): string
    {
        DateSensitiveView::deleteAll(['dv_view_name' => $this->getViewName()]);
        return "DROP VIEW IF EXISTS {$this->viewName}; ";
    }

    public function getReInitSql(): string
    {
        return $this->getDropSql() . $this->getCreateSql();
    }

    public function getBlockEncryptionModeSql(): string
    {
        return "SET block_encryption_mode = '" . $this->settingBlockEncryptionMode . "'; ";
    }

    private function getPreparedCreateColumns(): string
    {
        $columns = $this->getNonCryptColumns();
        foreach ($this->cryptColumns as $columnName) {
            $columns[] = "TO_BASE64(AES_ENCRYPT({$columnName}, '" . $this->settingKeyStr . "', '" . $this->settingInitVector . "')) AS {$columnName}";
        }
        return implode(',', $columns);
    }

    private function getNonCryptColumns(): array
    {
        $columns = array_keys($this->db->getTableSchema($this->tableName)->columns);
        $columns = array_diff($columns, $this->cryptColumns);
        $columns = array_unique($columns);
        foreach ($columns as $key => $value) {
            $columns[$key] = '`' . $value . '`';
        }
        return $columns;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    private function getPreparedSelectColumns(): string
    {
        $columns = $this->getNonCryptColumns();
        foreach ($this->cryptColumns as $columnName) {
            $columns[] = "FROM_BASE64(CAST(AES_DECRYPT({$columnName}, '" . $this->settingKeyStr . "', '" . $this->settingInitVector . "') AS CHAR(10000) CHARACTER SET utf8)) AS {$columnName}";
        }
        return implode(',', $columns);
    }

    /**
     * @return string
     * @example $service = new DbViewCryptService($db, 'table', ['column']);
     * $db->createCommand($service->getBlockEncryptionModeSql())->execute();
     * $db->createCommand($service->->getSelectSql())->queryAll();
     */
    public function getSelectSql(): string
    {
        return "SELECT 
                {$this->getPreparedSelectColumns()}
            FROM
                {$this->viewName}; ";
    }

    public static function viewCheck(string $viewName, array $tables): bool
    {
        $tableName = str_replace(DbViewCryptDictionary::VIEW_POST_FIX, '', $viewName);
        return array_key_exists($tableName, $tables);
    }
}
