<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 15/11/2019
 * Time: 11:05 AM
 */

namespace console\controllers;

use common\components\AppService;
use common\models\Currency;
use src\helpers\app\AppHelper;
use src\model\sms\entity\smsDistributionList\SmsDistributionList;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * App Service List
 *
 */
class ServiceController extends Controller
{
    /**
     *  Run update currency list & rates
     */
    public function actionUpdateCurrency(): void
    {
        printf("\n --- Start (" . date('H:i:s') . ") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        try {
            $result = Currency::synchronization();

            if ($result) {
                if ($result['error']) {
                    Yii::error($result['error'], 'Console:ServiceController:actionUpdateCurrency:Throwable');
                    echo $this->ansiFormat('Error: ' . $result['error'], Console::FG_RED) . PHP_EOL;
                } else {
                    echo $this->ansiFormat('- Synchronization successful', Console::FG_BLUE) . PHP_EOL;
                    if ($result['created']) {
                        echo $this->ansiFormat('- Created currency: "' . implode(', ', $result['created']) . '"', Console::FG_YELLOW) . PHP_EOL;
                    }
                    if ($result['updated']) {
                        echo $this->ansiFormat('- Updated currency: "' . implode(', ', $result['updated']) . '"', Console::FG_GREEN) . PHP_EOL;
                    }
                }
            }
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableFormatter($throwable);
            Yii::error($message, 'Console:ServiceController:actionUpdateCurrency:Throwable');
            echo $this->ansiFormat('Error: ' . $message, Console::FG_RED) . PHP_EOL;
        }

        printf("\n --- End (" . date('H:i:s') . ") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     *  Send Sms from Distribution List
     */
    public function actionSendSms(): void
    {
        printf("\n --- Start (" . date('H:i:s') . ") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $count = Yii::$app->params['settings']['sms_distribution_count'] ?? 0;
        $n = 0;
        if ($count) {
            try {
                $smsList = SmsDistributionList::getSmsListForJob($count);
                if ($smsList) {
                    foreach ($smsList as $smsItem) {
                        $result = $smsItem->sendSms();
                        echo(++$n) . '. Id: ' . $smsItem->sdl_id . ' ';
                        echo VarDumper::dumpAsString($result) . PHP_EOL;
                    }
                } else {
                    echo $this->ansiFormat(' - SMS List is empty! -', Console::FG_RED);
                }
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableFormatter($throwable);
                Yii::error($message, 'Console:ServiceController:actionSendSmsDistributionList:Throwable');
                echo $this->ansiFormat('Error: ' . $message, Console::FG_RED) . PHP_EOL;
            }
        } else {
            printf("\n Setting %s is empty! \n", $this->ansiFormat('sms_distribution_count', Console::FG_YELLOW));
        }

        printf("\n --- End (" . date('H:i:s') . ") %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @return array
     */
    private function getAppGeneralInfoData(): array
    {
        $data[] = [
            'entity'    => 'Entity',
            'name'      => 'Name',
            'version'   => 'Version',
        ];

        $data[] = [
            'entity'    => '---',
            'name'      => '---',
            'version'   => '---',
        ];

        $data[] = [
            'entity'    => 'Programming language',
            'name'      => 'PHP',
            'version'   => '7.4',
        ];

        $data[] = [
            'entity'    => 'Programming language',
            'name'      => 'JS',
            'version'   => '',
        ];

        $data[] = [
            'entity'    => 'Framework',
            'name'      => 'YII2',
            'version'   => '2.0.45',
        ];

        $data[] = [
            'entity'    => 'Codebase storage',
            'name'      => 'Bitbucket',
            'version'   => '',
        ];

        $data[] = [
            'entity'    => 'Database',
            'name'      => 'MySQL',
            'version'   => '',
        ];

        $data[] = [
            'entity'    => 'Database',
            'name'      => 'Postgres',
            'version'   => '',
        ];

        $data[] = [
            'entity'    => 'Infrastructure provider',
            'name'      => 'AWS',
            'version'   => '',
        ];

        return $data;
    }


    /**
     * @return array
     */
    private function getDepData(): array
    {
        $data[] = [
            'name' => 'Beanstalk',
            'type' => 'service',
            'version' => 'latest',
            'license' => '',
            'source' => '',
            'comments' => '',
        ];

        $data[] = [
            'name' => 'Redis',
            'type' => 'service',
            'version' => 'latest',
            'license' => '',
            'source' => '',
            'comments' => '',
        ];
        $data[] = [
            'name' => 'Geonames',
            'type' => 'service',
            'version' => 'latest',
            'license' => '',
            'source' => '',
            'comments' => '',
        ];
        $data[] = [
            'name' => 'CDN',
            'type' => 'service',
            'version' => 'latest',
            'license' => '',
            'source' => '',
            'comments' => '',
        ];
        $data[] = [
            'name' => 'Neutrino',
            'type' => 'service',
            'version' => 'latest',
            'license' => '',
            'source' => '',
            'comments' => '',
        ];
        $data[] = [
            'name' => 'Amazon S3',
            'type' => 'service',
            'version' => 'latest',
            'license' => '',
            'source' => '',
            'comments' => '',
        ];

        return $data;
    }




    /**
     *  Run composer .lock-file info (extension list)
     */
    public function actionCreateGuides(): void
    {
        printf(
            "\n --- Start (" . date('H:i:s') . ") %s ---\n",
            $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW)
        );

        $dir = Yii::getAlias('@root/guides/');
        $generalInfoFile = $dir . 'content/application-general-info.md';
        $dependFile = $dir . 'content/application-dependencies.md';
        $infoFile = $dir . 'content/application-info.md';

        try {
            $genContent = //'[Application General Information] (# app-gen)' . PHP_EOL .
                '# Application General Information' . PHP_EOL;
            $data = $this->getAppGeneralInfoData();
            $rowContent = [];
            foreach ($data as $n => $item) {
                $rowContent[] = '|' . implode('|', $item) . '|';
            }
            $genContent .= implode(PHP_EOL, $rowContent);
            file_put_contents($generalInfoFile, $genContent);


            $depContent = //'[Application Dependencies] (# app-dep)' . PHP_EOL .
                '# Application Dependencies' . PHP_EOL;
            $rowContent = [];
            $header = [
                'nr'    => 'Nr.',
                'name' => 'Name',
                'type' => 'Type',
                'version' => 'Version',
                'license' => 'License',
                'source' => 'Source',
                'authors' => 'Authors',
                'comments' => 'Comments',
            ];

            $rowContent[] = '|' . implode('|', $header) . '|';

            $header = array_fill(0, count($header), '---');
            $rowContent[] = '|' . implode('|', $header) . '|';


            $data = AppService::getComposerLockData();
            $count = 1;
            foreach ($data as $n => $item) {
                $rowContent[] = '|' . ($count++) . '|' . implode('|', $item) . '|';
            }
            $depContent .= implode(PHP_EOL, $rowContent);
            file_put_contents($dependFile, $depContent);



            $servContent = //'[Application Services] (# app-serv)' . PHP_EOL .
                '# Application Services' . PHP_EOL;
            $rowContent = [];
            $header = [
                'nr'    => 'Nr.',
                'name' => 'Name',
                'type' => 'Type',
                'version' => 'Version',
                'license' => 'License',
                'source' => 'Source',
                'comments' => 'Comments',
            ];

            $rowContent[] = '|' . implode('|', $header) . '|';

            $header = array_fill(0, count($header), '---');
            $rowContent[] = '|' . implode('|', $header) . '|';


            $data = $this->getDepData();
            $count = 1;
            foreach ($data as $n => $item) {
                $rowContent[] = '|' . ($count++) . '|' . implode('|', $item) . '|';
            }
            $servContent .= implode(PHP_EOL, $rowContent);

            file_put_contents($infoFile, $genContent . PHP_EOL . PHP_EOL . $servContent
                . PHP_EOL . PHP_EOL . $depContent);

            echo $generalInfoFile . PHP_EOL;
            echo $dependFile . PHP_EOL;
            echo $infoFile . PHP_EOL;
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableFormatter($throwable);
            Yii::error($message, 'Console:ServiceController:actionCreateAppInfoGuides:Throwable');
            echo $this->ansiFormat('Error: ' . $message, Console::FG_RED) . PHP_EOL;
        }

        printf(
            "\n --- End (" . date('H:i:s') . ") %s ---\n",
            $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW)
        );
    }
}
