<?php

namespace src\services\cleaner;

use common\models\ApiLog;
use common\models\Call;
use common\models\GlobalLog;
use common\models\Log;
use common\models\Notifications;
use DateTime;
use modules\requestControl\models\UserSiteActivity;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\services\cleaner\cleaners\ApiLogCleaner;
use src\services\cleaner\cleaners\CallCleaner;
use src\services\cleaner\cleaners\GlobalLogCleaner;
use src\services\cleaner\cleaners\LeadPoorProcessingLogCleaner;
use src\services\cleaner\cleaners\LogCleaner;
use src\services\cleaner\cleaners\NotificationsCleaner;
use src\services\cleaner\cleaners\UserMonitorCleaner;
use src\services\cleaner\cleaners\UserSiteActivityCleaner;
use yii\base\Exception;
use src\model\user\entity\monitor\UserMonitor;
use src\services\cleaner\form\DbCleanerParamsForm;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class DbCleanerService
 */
class DbCleanerService
{
    public const ALLOWED_PARAMS = [
        'hour', 'day', 'month', 'year',
        'date', 'datetime', 'strict_date',
    ];

    public const FORMAT_DATE_TIME = 'Y-m-d H:i:s';
    public const FORMAT_DATE = 'Y-m-d';

    private array $classMap = [];

    public function __construct()
    {
        $this->setClassMap();
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return DateTime
     * @throws \Exception
     */
    public static function generateDate(DbCleanerParamsForm $form): DateTime
    {
        if ($form->strict_date) {
            return new DateTime($form->strict_date);
        }
        if ($form->datetime) {
            return new DateTime($form->datetime);
        }
        if ($form->date) {
            return new DateTime($form->date);
        }

        $dateTime = new DateTime('now');
        if ($form->year) {
            $dateTime->modify('-' . $form->year . ' years');
        }
        if ($form->month) {
            $dateTime->modify('-' . $form->month . ' months');
        }
        if ($form->day) {
            $dateTime->modify('-' . $form->day . ' days');
        }
        if ($form->hour) {
            $dateTime->modify('-' . $form->hour . ' hours');
        }
        return $dateTime;
    }

    public static function generateQuery(DbCleanerParamsForm $form, Query $query): Query
    {
        return $query->andWhere(self::generateRestriction($form));
    }

    /**
     * @param DbCleanerParamsForm $form
     * @return array
     * @throws \Exception
     */
    public static function generateRestriction(DbCleanerParamsForm $form): array
    {
        $dtOlder = self::generateDate($form);

        if ($form->strict_date) {
            return ['DATE(' . $form->column . ')' => $dtOlder->format(self::FORMAT_DATE)];
        }
        if ($form->datetime) {
            return ['<', $form->column, $dtOlder->format(self::FORMAT_DATE_TIME)];
        }
        if ($form->date) {
            return ['<', 'DATE(' . $form->column . ')', $dtOlder->format(self::FORMAT_DATE)];
        }
        return ['<', $form->column, $dtOlder->format(self::FORMAT_DATE_TIME)];
    }

    public static function generateRestrictionTimestamp(DbCleanerParamsForm $form): array
    {
        $dateTime = self::generateDate($form);
        $beginDate = new DateTime($dateTime->format('Y-m-d') . ' 00:00:00');
        $endDate = new DateTime($dateTime->format('Y-m-d') . ' 23:59:59');

        if ($form->strict_date) {
            return ['BETWEEN', $form->column, $beginDate->getTimestamp(), $endDate->getTimestamp()];
        }
        if ($form->datetime) {
            return ['<', $form->column, $dateTime->getTimestamp()];
        }
        if ($form->date) {
            return ['<', $form->column, $beginDate->getTimestamp()];
        }
        return ['<', $form->column, $dateTime->getTimestamp()];
    }

    public function getClassMap(): array
    {
        return $this->classMap;
    }

    public function setClassMap(array $classMap = []): DbCleanerService
    {
        $map = [
            Log::tableName() => LogCleaner::class,
            Call::tableName() => CallCleaner::class,
            ApiLog::tableName() => ApiLogCleaner::class,
            GlobalLog::tableName() => GlobalLogCleaner::class,
            UserMonitor::tableName() => UserMonitorCleaner::class,
            Notifications::tableName() => NotificationsCleaner::class,
            UserSiteActivity::tableName() => UserSiteActivityCleaner::class,
            LeadPoorProcessingLog::tableName() => LeadPoorProcessingLogCleaner::class,
        ];

        $this->classMap = ArrayHelper::merge($classMap, $map);
        return $this;
    }

    /**
     * @param string $table
     * @return mixed
     * @throws Exception
     */
    protected function getClassNameByTable(string $table): string
    {
        if (ArrayHelper::keyExists($table, $this->getClassMap())) {
            return $this->classMap[$table];
        }
        throw new Exception('Class processing not found by table: ' . $table);
    }

    /**
     * @param string $table
     * @return CleanerInterface
     * @throws Exception
     */
    public function initClass(string $table): CleanerInterface
    {
        $nameClass = $this->getClassNameByTable($table);

        if (class_exists($nameClass)) {
            return new $nameClass();
        }
        throw new Exception('Class processing not found by table: ' . $table);
    }

    public static function daysToBeforeDate(int $days): string
    {
        return (new \DateTime('now'))
                ->modify('-' . $days . ' days')
                ->format('Y-m-d');
    }
}
