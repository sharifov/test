<?php

namespace src\services\system;

use src\model\dbDataSensitiveView\entity\DbDataSensitiveView;
use src\helpers\setting\SettingHelper;
use Yii;
use yii\db\ColumnSchema;
use yii\db\Connection;
use yii\db\Exception;

/**
 * Class DbViewCryptService
 *
 * @property Connection $db
 * @property string $tableName
 * @property array $cryptColumns
 * @property string $viewName
 * @property string $settingBlockEncryptionMode
 * @property string $settingKeyStr
 * @property string tableSchema
 * @property array $encryptColumns
 * @property string $settingInitVector
 */
class DbViewCryptService
{
    public const VIEW_SEPARATOR = '_';
    public const MASK_YEAR = "year";
    public const MASK_MAIL = "mail";
    public const MASK_PHONE = "phone";
    public const MASK_REGEXP = "regexp";

    private ?string $settingBlockEncryptionMode;
    private ?string $settingKeyStr;
    private ?string $settingInitVector;

    private Connection $db;
    private string $tableName;
    private array $cryptColumns;
    private string $viewName;
    private array|ColumnSchema|null $tableSchema;
    private array $encryptColumns;

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
        $this->tableSchema = Yii::$app->db->getTableSchema($this->tableName)->columns;
        $this->encryptColumns = $cryptColumns;
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
        DbDataSensitiveView::deleteAll(['ddv_view_name' => $this->getViewName()]);
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
        foreach ($this->cryptColumns as $column) {
            if (isset($column['column'])) {
                if ($this->needMask($column)) {
                    $columns[] = $this->maskColumn($column);
                }
                if (empty($column['path']) || !$this->isExistsColumn($column['column'])) {
                    continue;
                }
                if (isset($column['path'])) {
                    $encryptJson = [];
                    foreach ($column['path'] as $jsonPath) {
                        $pathsToEncrypt = [];
                        $this->processPathIntervals($jsonPath, $pathsToEncrypt);

                        foreach ($pathsToEncrypt as $path) {
                            $encryptJson[] = sprintf(
                                "'%s', TO_BASE64(AES_ENCRYPT(LOWER(JSON_VALUE(`%s`, '%s')), %s, '%s'))",
                                $path,
                                $column['column'],
                                $path,
                                $column['column'],
                                $this->settingInitVector
                            );
                        }
                    }
                    if ($encryptJson) {
                        $columns[] = sprintf(
                            "IF(JSON_VALID(`%s`), JSON_REPLACE(`%s`, %s), `%s`) as `%s`",
                            $column['column'],
                            $column['column'],
                            implode(',', $encryptJson),
                            $column['column'],
                            $column['column']
                        );
                    }
                }
            } elseif (is_string($column) && $this->isExistsColumn($column)) {
                $columns[] = "TO_BASE64(AES_ENCRYPT(LOWER({$column}), '" . $this->settingKeyStr . "', '" . $this->settingInitVector . "')) AS {$column}";
            }
        }
        return implode(',', $columns);
    }

    protected function getEncryptColumnKeys(): array
    {
        return array_map(fn($v): string => ($v['column'] ?? $v), $this->encryptColumns) ?? [];
    }

    private function getNonCryptColumns(): array
    {
        $columns = array_diff(array_keys($this->db->getTableSchema($this->tableName)->columns), $this->getEncryptColumnKeys());
        array_walk($columns, fn(&$itm, $key) => $itm = sprintf("`%s`", $itm));
        return $columns;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    private function getPreparedSelectColumns(): string
    {
        $columns = $this->getNonCryptColumns();
        foreach ($this->cryptColumns as $column) {
            $columns[] = "FROM_BASE64(CAST(AES_DECRYPT({$column}, '" . $this->settingKeyStr . "', '" . $this->settingInitVector . "') AS CHAR(10000) CHARACTER SET utf8)) AS {$column}";
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

    protected function isExistsColumn(string $column): bool
    {
        return isset($this->tableSchema[$column]);
    }

    protected function processPathIntervals(string $path, array &$paths): void
    {
        $processed = false;
        preg_match('/\{(\d+\.\.\d+)\}/', $path, $intervals);
        if (count($intervals) > 1) {
            $intervalFullString = $intervals[0];
            $intervalString = $intervals[1];
            $intervalValues = explode('..', $intervalString);
            if (count($intervalValues) === 2) {
                for ($i = $intervalValues[0]; $i <= $intervalValues[1]; $i++) {
                    $newPath = strtr($path, [
                        $intervalFullString => $i
                    ]);
                    $this->processPathIntervals($newPath, $paths);
                }
                $processed = true;
            }
        }
        if (!$processed) {
            $paths[] = $path;
        }
    }

    private function needMask($column): bool
    {
        return isset($column['mask']) && $this->isExistsColumn($column['column']);
    }

    private function maskColumn($column)
    {
        return match ($column['mask']) {
            self::MASK_YEAR => $this->maskYear($column['column']),
            self::MASK_MAIL => $this->maskMail($column['column']),
            self::MASK_PHONE => $this->maskPhone($column['column'], $column['start'] ?? 1, $column['length'] ?? null),
            self::MASK_REGEXP => $this->maskRegexp($column['column'], $column['pattern'] ?? null, $column['replace'] ?? null),
        };
    }

    private function maskYear(string $columnName)
    {
        return sprintf(
            "(SELECT EXTRACT(YEAR FROM `%s`)) AS `%s`",
            $columnName,
            $columnName
        );
    }

    private function maskMail(string $columnName)
    {
        return sprintf(
            "(SELECT RIGHT(%s, LENGTH(%s)-INSTR(%s, '@'))) AS `%s_mask`",
            $columnName,
            $columnName,
            $columnName,
            $columnName
        );
    }

    private function maskPhone(string $columnName, int $start, ?int $length = null)
    {
        if (!$length) {
            throw new Exception('Length is required in mask: ' . self::MASK_PHONE . ', check config');
        }
        return sprintf(
            "(SELECT SUBSTRING(%s, %s, %s))  AS `%s_mask`",
            $columnName,
            $start,
            $length,
            $columnName
        );
    }

    private function maskRegexp(string $columnName, ?string $pattern = null, ?string $replace = null)
    {
        if (!$pattern || $replace === null) {
            throw new Exception('Pattern or replace string is required in mask: ' . self::MASK_REGEXP . ', check config');
        }
        return sprintf(
            "(SELECT REGEXP_REPLACE( %s, '%s' , '%s'))  AS `%s_mask`",
            $columnName,
            $pattern,
            $replace,
            $columnName
        );
    }
}
