<?php

namespace frontend\controllers;

use sales\model\conference\entity\aggregate\ConferenceLogAggregate;
use sales\model\conference\entity\aggregate\log\HtmlFormatter;
use sales\model\conference\entity\conferenceEventLog\ConferenceEventLogQuery;
use sales\model\conference\entity\conferenceEventLog\EventFactory;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class ConferenceDebugController extends FController
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

    public function actionData()
    {
        $sid = (string)\Yii::$app->request->get('sid');
        if (!$sid) {
            throw new BadRequestHttpException('Not found sid');
        }
        $data = ConferenceEventLogQuery::getRawData($sid);
        if (!$data) {
            return $this->renderContent('Data for conference ' . $sid . ' is empty');
        }
        $out = VarDumper::dumpAsString($data);
        $out = str_replace('\"', '"', $out);
        $out = str_replace('\'
        ', '\',
        ', $out);
        $out = str_replace(']', '],', $out);
        return $this->renderContent($out);
    }

    public function actionFormat()
    {
        $events = [];
        $data = [];
        foreach ($data as $item) {
            $events[] = EventFactory::create($item['type'], $item['data']);
        }
        $aggregate = new ConferenceLogAggregate($events);
        $aggregate->run();
        $printer = new HtmlFormatter($aggregate->logs);
        return $this->renderContent($printer->format());
    }

    public function actionShow()
    {
        $sid = (string)\Yii::$app->request->get('sid');
        if (!$sid) {
            throw new BadRequestHttpException('Not found sid');
        }
        $data = ConferenceEventLogQuery::getRawData($sid);
        if (!$data) {
            return $this->renderContent('Data for conference ' . $sid . ' is empty');
        }
        $events = [];
        foreach ($data as $item) {
            $events[] = EventFactory::create($item['type'], $item['data']);
        }
        $aggregate = new ConferenceLogAggregate($events);
        $aggregate->run();
        $printer = new HtmlFormatter($aggregate->logs);
        return $this->renderContent($printer->format());
    }
}
