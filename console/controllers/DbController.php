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

class DbController extends Controller
{
    public function actionConvertCollate()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat($this->className() . ' - ' . $this->action->id, Console::FG_YELLOW));
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
            echo "tbl: ".$tableName. "\r\n";
        }
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        printf("\n --- End %s ---\n", $this->ansiFormat($this->className() . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


}