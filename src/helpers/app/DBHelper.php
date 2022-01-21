<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 10/01/2022
 * Time: 17:05
 */

namespace src\helpers\app;

use DateTime;
use RuntimeException;
use yii\db\Connection;
use yii\db\Exception;

class DBHelper
{
    /**
     * Calculate from and to dates from a given date.
     * Given date -> from = start of the month, to = next month start date
     *
     * @param DateTime $date partition start date
     * @return array DateTime table_name created table
     * @throws RuntimeException any errors occurred during execution
     */
    public static function partitionDatesFrom(DateTime $date): array
    {
        $monthBegin = date('Y-m-d', strtotime(date_format($date, 'Y-m-1')));
        if (!$monthBegin) {
            throw new RuntimeException('Invalid partition start date');
        }

        $partitionStartDate = date_create_from_format('Y-m-d', $monthBegin);
        $partitionEndDate = date_create_from_format('Y-m-d', $monthBegin);

        date_add($partitionEndDate, date_interval_create_from_date_string('1 month'));

        return [$partitionStartDate, $partitionEndDate];
    }


    /**
     * Create a partition table with indicated from and to date
     *
     * @param Connection $dbObject
     * @param string $dbTableName
     * @param DateTime $partFromDateTime
     * @param DateTime $partToDateTime
     * @return string
     * @throws Exception
     */
    public static function createMonthlyPartition(
        Connection $dbObject,
        string $dbTableName,
        DateTime $partFromDateTime,
        DateTime $partToDateTime
    ): string {
        $partTableName = $dbTableName . '_' . date_format($partFromDateTime, 'Y_m');
        $cmd = $dbObject->createCommand('create table ' . $partTableName . ' PARTITION OF ' . $dbTableName .
            " FOR VALUES FROM ('" . date_format($partFromDateTime, 'Y-m-d') . "') TO ('" .
            date_format($partToDateTime, 'Y-m-d') . "')");
        $cmd->execute();
        return $partTableName;
    }
}
