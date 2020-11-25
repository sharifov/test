<?php

namespace frontend\controllers;

use common\models\Conference;
use sales\model\conference\entity\aggregate\ConferenceLogAggregate;
use sales\model\conference\entity\aggregate\log\HtmlFormatter;
use sales\model\conference\entity\conferenceEventLog\ConferenceEventLogQuery;
use sales\model\conference\entity\conferenceEventLog\EventFactory;
use sales\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats;
use sales\model\conference\form\DebugForm;
use sales\model\conference\useCase\saveParticipantStats\Command;
use sales\model\conference\useCase\saveParticipantStats\Handler;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

/**
 * Class ConferenceDebugController
 *
 * @property Handler $saveParticipantsHandler
 */
class ConferenceDebugController extends FController
{
    private Handler $saveParticipantsHandler;

    public function __construct($id, $module, Handler $saveParticipantsHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->saveParticipantsHandler = $saveParticipantsHandler;
    }

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

    public function actionIndex()
    {
        $model = new DebugForm();
        $content = '';

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            if ($model->action === DebugForm::ACTION_RAW_DATA) {
                $content = $this->getDawData($model->conferenceSid);
            } elseif ($model->action === DebugForm::ACTION_SHOW_HISTORY) {
                $content = $this->showHistory($model->conferenceSid);
            } elseif ($model->action === DebugForm::ACTION_RECALCULATE) {
                if ($conference = Conference::findOne(['cf_sid' => $model->conferenceSid])) {
                    ConferenceParticipantStats::deleteAll(['cps_cf_sid' => $model->conferenceSid]);
                    $this->saveParticipantsHandler->handle(new Command($conference->cf_sid, $conference->cf_id));
                    $content = 'Done';
                } else {
                    $content = 'Conference not found';
                }
            }
        }

        return $this->render('index', [
            'model' => $model,
            'content' => $content,
        ]);
    }

    private function getDawData(string $sid): string
    {
        $data = ConferenceEventLogQuery::getRawData($sid);
        if (!$data) {
            return 'Raw Data for conference SID: ' . $sid . ' is empty';
        }
        $out = VarDumper::dumpAsString($data);
        $out = str_replace('\"', '"', $out);
        $out = str_replace('\'
        ', '\',
        ', $out);
        $out = str_replace(']', '],', $out);
        return $out;
    }

    private function showHistory(string $sid): string
    {
        $data = ConferenceEventLogQuery::getRawData($sid);
        if (!$data) {
            return 'Raw Data for conference SID: ' . $sid . ' is empty';
        }

        $events = [];
        foreach ($data as $item) {
            $events[] = EventFactory::create($item['type'], $item['data']);
        }

        try {
            $aggregate = new ConferenceLogAggregate($events);
            $aggregate->run();
            $printer = new HtmlFormatter($aggregate->logs);
            $out = $printer->format();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return $out;
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
}
