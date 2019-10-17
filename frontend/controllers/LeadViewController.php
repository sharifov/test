<?php

namespace frontend\controllers;

use common\models\Quote;
use sales\forms\lead\CloneQuoteByUidForm;
use sales\services\lead\LeadCloneQuoteService;
use Yii;
use common\models\Lead;
use common\models\search\lead\LeadSearchByIp;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class LeadViewController
 *
 * @property  LeadCloneQuoteService $leadCloneQuoteService
 */
class LeadViewController extends FController
{

    private $leadCloneQuoteService;

    /**
     * @param $id
     * @param $module
     * @param LeadCloneQuoteService $leadCloneQuoteService
     * @param array $config
     */
    public function __construct($id, $module, LeadCloneQuoteService $leadCloneQuoteService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadCloneQuoteService = $leadCloneQuoteService;
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     */
    public function actionSearchLeadsByIp(): string
    {
        $lead = $this->findLeadByGid((string)Yii::$app->request->get('gid'));
        if (!$lead->request_ip) {
            throw new NotFoundHttpException('Not found Lead with request ip');
        }

        $params[LeadSearchByIp::getShortName()]['requestIp'] = $lead->request_ip;

        $dataProvider = (new LeadSearchByIp())->search($params, Yii::$app->user->id);

        return $this->renderAjax('search_leads_by_ip', [
            'dataProvider' => $dataProvider,
            'lead' => $lead
        ]);
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCloneQuoteByUid(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new CloneQuoteByUidForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->leadCloneQuoteService->cloneByUid($form->uid, $form->leadGid);
            } catch (\DomainException $e) {
                Yii::warning($e->getMessage());
                return ['success' => false, 'message' => $e->getMessage()];
            }
            if (Yii::$app->getSession()->hasFlash('warning')) {
                $message = [];
                foreach (Yii::$app->getSession()->getFlash('warning') as $flash) {
                    $message[] = $flash;
                }
                return ['success' => true, 'message' => implode(PHP_EOL, $message)];
            }
            return ['success' => true];
        }
        return ['success' => false, 'message' =>  VarDumper::dumpAsString($form->errors)];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCloneQuoteByUidValidate(): array
    {
        $form = new CloneQuoteByUidForm();
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param string $gid
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLeadByGid($gid): Lead
    {
        if ($model = Lead::findOne(['gid' => $gid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $uid
     * @return Quote
     * @throws NotFoundHttpException
     */
    protected function findQuoteByUid($uid): Quote
    {
        if ($model = Quote::findOne(['uid' => $uid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
