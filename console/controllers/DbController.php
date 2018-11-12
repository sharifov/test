<?php
namespace console\controllers;

use common\components\BackOffice;
use common\models\Airline;
use common\models\Airport;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use common\models\Note;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\Reason;
use common\models\Source;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class DbController extends Controller
{

    /**
     * @throws \yii\db\Exception
     */
    public function actionConvertCollate()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables
        $tables = $db->createCommand('SELECT table_name FROM information_schema.tables WHERE table_schema=:schema AND table_type = "BASE TABLE"', [
            ':schema' => $schema
        ])->queryAll();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        // Alter the encoding of each table
        foreach ($tables as $table) {
            $tableName = $table['table_name'];
            $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci")->execute();
            echo "tbl: " . $tableName . "\r\n";
        }
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * Remove Client Emails and Phones Duplicates
     */
    public function actionRemoveClientDuplicates()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();

        $duplicatesEmail = $db->createCommand('SELECT ce.id, count(client_id) as cnt, email, client_id FROM client_email ce GROUP BY email, client_id HAVING cnt > 1')->queryAll();

        if (count($duplicatesEmail) > 0) {
            foreach ($duplicatesEmail as $entry) {
                $db->createCommand('DELETE FROM client_email WHERE email = :email AND client_id = :client_id AND id != :id', [
                    'id' => $entry['id'],
                    'email' => $entry['email'],
                    'client_id' => $entry['client_id']
                ])->execute();
            }
            printf("\n--- Removed %s duplicates in client_email ---\n", count($duplicatesEmail));
        } else {
            printf("\n--- In client_email not found duplicates ---\n");
        }

        $duplicatesPhone = $db->createCommand('SELECT ce.id, count(client_id) as cnt, phone, client_id FROM client_phone ce GROUP BY phone, client_id HAVING cnt > 1')->queryAll();

        if (count($duplicatesPhone) > 0) {
            foreach ($duplicatesPhone as $entry) {
                $db->createCommand('DELETE FROM client_phone WHERE phone = :phone AND client_id = :client_id AND id != :id', [
                    'id' => $entry['id'],
                    'phone' => $entry['phone'],
                    'client_id' => $entry['client_id']
                ])->execute();
            }
            printf("\n--- Removed %s duplicates in client_phone ---\n", count($duplicatesPhone));
        } else {
            printf("\n--- In client_phone not found duplicates ---\n");
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * Update Airline cabin classes from airapi
     * 30   0  *  *  *     run-this-one php /var/www/sale/yii db/update-airline-cabin-classes
     */
    public function actionUpdateAirlineCabinClasses()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        Airline::syncCabinClasses();

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


    /**
     * @throws \yii\db\Exception
     */
    public function actionUpdateLeadFlow()
    {

        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();


        $sql = 'SELECT id,
(SELECT lf1.status FROM lead_flow AS lf1 WHERE lf1.lead_id = lf.lead_id AND lf1.id < lf.id ORDER BY lf1.id DESC LIMIT 1) AS from_status_id,
(SELECT lf2.created FROM lead_flow AS lf2 WHERE lf2.lead_id = lf.lead_id AND lf2.id > lf.id ORDER BY lf2.id ASC LIMIT 1) AS end_dt,
(UNIX_TIMESTAMP((SELECT lf2.created FROM lead_flow AS lf2 WHERE lf2.lead_id = lf.lead_id AND lf2.id > lf.id ORDER BY lf2.id ASC LIMIT 1)) - UNIX_TIMESTAMP(lf.created)) AS time_duration
FROM lead_flow AS lf
ORDER BY lf.lead_id, id';

        $logs = $db->createCommand($sql)->queryAll();

        if($logs) {
            foreach ($logs as $nr => $log) {
                LeadFlow::updateAll(['lf_from_status_id' => $log['from_status_id'], 'lf_end_dt' => $log['end_dt'], 'lf_time_duration' => $log['time_duration']], ['id' => $log['id']]);
                echo $nr.' - id: '.$log['id']."\n";
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * Update quotes from dump to trip + segments
     */
    public function actionUpdateQuotesFromDump()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        /* $db = Yii::$app->getDb();
        $sql = 'SELECT q.id, q.uid, q.reservation_dump FROM quotes q LEFT JOIN quote_trip qt ON qt.qt_quote_id = q.id WHERE qt.qt_id IS NULL';
        $quotes = $db->createCommand($sql)->queryAll();
        printf("\n Quotes to update: %d \n", count($quotes));

        if(count($quotes)){
            foreach ($quotes as $quote){
                $data = Quote::parseDump($quote['reservation_dump']);
                printf("\n %s\n", VarDumper::dumpAsString($data));
                break;
            }
        } */

        $quotes = Quote::find()->leftJoin('quote_trip','quote_trip.qt_quote_id = quotes.id')->where(['quote_trip.qt_id' => null])->all();
        printf("\n Quotes to update: %d \n", count($quotes));
        if(count($quotes)){
            $cntUpdated = 0;
            foreach ($quotes as $quote){
                if($quote->createQuoteTrips()){
                    $cntUpdated++;
                }
            }

            printf("\n Quotes updated: %d \n", $cntUpdated);
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

    }
}