<?php

namespace console\controllers;

use common\models\Airline;
use common\models\Airport;
use common\models\Project;
use common\models\Source;
use yii\console\Controller;
use Yii;

class SyncController extends Controller
{
    public function actionProjects()
    {
        $result = $this->sendRequest('default/projects');
        if (isset($result['data'])) {
            foreach ($result['data'] as $projectId => $projectAttr) {
                $project = Project::findOne(['id' => $projectId]);
                if ($project === null) {
                    $project = new Project();
                }
                $project->attributes = $projectAttr;
                if (!$project->save()) {
                    var_dump($project->getErrors());
                    exit;
                }
                foreach ($projectAttr['sources'] as $sourceId => $sourceAttr) {
                    $source = Source::findOne(['id' => $sourceId]);
                    if ($source === null) {
                        $source = new Source();
                    }
                    $source->attributes = $sourceAttr;
                    if (!$source->save()) {
                        var_dump($source->getErrors());
                        exit;
                    }
                }
                echo 'Sync success project id: ' . $projectId . PHP_EOL;
            }
        }
    }

    public function actionAirports()
    {
        $result = $this->sendRequest('default/airports');
        if (isset($result['data'])) {
            foreach ($result['data'] as $airportId => $airportAttr) {
                $airport = Airport::findOne(['id' => $airportId]);
                if ($airport === null) {
                    $airport = new Airport();
                }
                $airport->attributes = $airportAttr;
                if (!$airport->save()) {
                    var_dump($airport->getErrors());
                    exit;
                }
                echo 'Sync success airport id: ' . $airportId . PHP_EOL;
            }
        }
    }

    public function actionAirlines()
    {
        $result = $this->sendRequest('default/airlines');
        if (isset($result['data'])) {
            foreach ($result['data'] as $airlineId => $airlineAttr) {
                $airline = Airline::findOne(['id' => $airlineId]);
                if ($airline === null) {
                    $airline = new Airline();
                }
                $airline->attributes = $airlineAttr;
                if (!$airline->save()) {
                    var_dump($airline->getErrors());
                    exit;
                }
                echo 'Sync success airport id: ' . $airlineId . PHP_EOL;
            }
        }
    }

    private function sendRequest($endpoint, $type = 'GET', $fields = null)
    {
        $url = sprintf('%s/%s', Yii::$app->params['sync']['serverUrl'], $endpoint);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_POST, true);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'version: ' . Yii::$app->params['sync']['ver'],
            'signature: ' . $this->getSignature()
        ]);
        $result = curl_exec($ch);

        Yii::warning(sprintf("Request:\n%s\n\nDump:\n%s\n\nResponse:\n%s",
            print_r($fields, true),
            print_r(curl_getinfo($ch), true),
            print_r($result, true)
        ), 'SyncController->actionProjects()');

        return json_decode($result, true);
    }

    private function getSignature()
    {
        $expired = time() + 3600;
        $md5 = md5(sprintf('%s:%s:%s', Yii::$app->params['sync']['apiKey'], Yii::$app->params['sync']['ver'], $expired));
        return implode('.', [md5($md5), $expired, $md5]);
    }
}