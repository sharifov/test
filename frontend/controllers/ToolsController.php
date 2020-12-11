<?php

namespace frontend\controllers;

use common\models\Quote;
use sales\services\parsingDump\lib\ParsingDump;
use sales\services\parsingDump\lib\worldSpan\Baggage;
use sales\services\parsingDump\lib\worldSpan\Pricing;
use sales\services\parsingDump\lib\worldSpan\Reservation;
use sales\services\parsingDump\lib\worldSpan\WorldSpan;
use sales\services\parsingDump\ReservationService;
use Yii;
use common\models\ApiLog;
use common\models\search\ApiLogSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ToolsController implements the CRUD actions for ApiLog model.
 */
class ToolsController extends FController
{

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @return \yii\web\Response
     * @throws \yii\base\ErrorException
     */
    public function actionClearCache()
    {

        $successItems = [];
        $warningItems = [];

        if (Yii::$app->cache->flush()) {
            $successItems[] = 'Cache is flushed';
        } else {
            $warningItems[] = 'Cache is not flushed!';
        }

        Yii::$app->db->schema->refresh();
        $successItems[] = 'DB schema refreshed!';


        $fcDir = Yii::getAlias('@frontend/runtime/cache');
        $ccDir = Yii::getAlias('@console/runtime/cache');
        $wcDir = Yii::getAlias('@webapi/runtime/cache');

        FileHelper::removeDirectory($fcDir);
        FileHelper::removeDirectory($ccDir);
        FileHelper::removeDirectory($wcDir);

        if (!file_exists($fcDir)) {
            $successItems[] = 'Removed dir ' . $fcDir;
        } else {
            $warningItems[] = 'Not Removed dir ' . $fcDir;
        }

        if (!file_exists($ccDir)) {
            $successItems[] = 'Removed dir ' . $ccDir;
        } else {
            $warningItems[] = 'Not Removed dir ' . $ccDir;
        }

        if (!file_exists($wcDir)) {
            $successItems[] = 'Removed dir ' . $wcDir;
        } else {
            $warningItems[] = 'Not Removed dir ' . $wcDir;
        }

        if ($successItems) {
            Yii::$app->session->setFlash('success', implode('<br>', $successItems));
        }

        if ($warningItems) {
            Yii::$app->session->setFlash('warning', implode('<br>', $warningItems));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSupervisor(): string
    {

        //$supervisor = new \Supervisor\Api('127.0.0.1', 9001, 'supervisor', 'Supervisor2019!');

        //$processes = $supervisor->getAllProcessInfo();
        /*foreach ($processes as $processInfo) {
            print_r($processInfo);
        }*/

        // Call Supervisor API
        //VarDumper::dump($supervisor->getAllProcessInfo(), 10, true);
         //exit;

        return $this->render('supervisor');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionCheckFlightDump(): string
    {
        $data = [];
        $dump = Yii::$app->request->post('dump');
        $type = Yii::$app->request->post('type', ParsingDump::PARSING_DEFAULT_TYPE);
        $gds = Yii::$app->request->post('gds');
        $prepareSegment = (int) Yii::$app->request->post('prepare_segment', 0);

        if ($dump) {
            if ($prepareSegment === 1 && $type === ParsingDump::PARSING_TYPE_RESERVATION) {
                $data = (new ReservationService($gds))->parseReservation($dump, false);
            } elseif ($obj = ParsingDump::initClass($gds, $type)) {
                $data = $obj->parseDump($dump);
            } else {
                throw new \DomainException('Class (' . $gds . '\\' . $type . ') not found');
            }
        }

        return $this->render('check-flight-dump', [
            'dump' => $dump,
            'data' => $data,
            'type' => $type,
            'gds' => $gds,
            'prepareSegment' => $prepareSegment,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionCheckExcludeIp(): string
    {
        $data = [];
        $ip = substr(trim(Yii::$app->request->get('ip', '')), 0, 50);

        if ($ip) {
            $data[] = $ip;
        }

        return $this->render('check-exclude-ip', [
            'ip' => $ip,
            'data' => $data,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionStashLogFile(): string
    {
        $lines = 10;
        $frontendData = '';
        $consoleData = '';
        $webapiData = '';

        $fnFrontend = Yii::getAlias('@frontend/runtime/logs/stash.log');
        $fnConsole = Yii::getAlias('@console/runtime/logs/stash.log');
        $fnWebApi = Yii::getAlias('@webapi/runtime/logs/stash.log');

        if (file_exists($fnFrontend)) {
            $frontendData = $this->tailCustom($fnFrontend, $lines);
        }

        if (file_exists($fnConsole)) {
            $consoleData = $this->tailCustom($fnConsole, $lines);
        }

        if (file_exists($fnWebApi)) {
            $webapiData = $this->tailCustom($fnWebApi, $lines);
        }


        return $this->render('stash-log-file', [
            'frontendData' => $frontendData,
            'consoleData' => $consoleData,
            'webapiData' => $webapiData,
            'fnFrontend' => $fnFrontend,
            'fnConsole' => $fnConsole,
            'fnWebapi' => $fnWebApi,
            'lines' => $lines,
        ]);
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function actionDbInfo(): string
    {

        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables
        $tables = $db->createCommand('SELECT * FROM information_schema.tables WHERE table_schema=:schema AND table_type = "BASE TABLE"', [
            ':schema' => $schema,
        ])->queryAll();

        //VarDumper::dump($tables); exit;

        // Alter the encoding of each table
//        foreach ($tables as $id => $table) {
//            $tableName = $table['TABLE_NAME'];
//            if($tableName) {
//                $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET " . $collate . " COLLATE " . $collation)->execute();
//                echo $id." - tbl: " . $tableName . " - " . $table['TABLE_COLLATION'] . " \r\n";
//            }
//        }

        return $this->render('db-info', [
            'tables' => $tables,
            'schema' => $schema,
        ]);
    }

    /**
     * @param $filepath
     * @param int $lines
     * @param bool $adaptive
     * @return false|string
     */
    private function tailCustom($filepath, $lines = 1, $adaptive = true)
    {

        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) {
            return false;
        }

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }

        // Close file and return
        fclose($f);
        return trim($output);
    }
}
