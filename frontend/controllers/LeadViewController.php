<?php

namespace frontend\controllers;

use Yii;
use common\models\Lead;
use common\models\search\lead\LeadSearchByIp;
use yii\web\NotFoundHttpException;

class LeadViewController extends FController
{

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

}
