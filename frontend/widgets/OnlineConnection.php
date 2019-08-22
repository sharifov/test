<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;


use common\models\Lead2;
use sales\entities\cases\Cases;
use Yii;

/**
 * OnlineConnection widget
 *
 * @author Alexandr <alex.connor@techork.com>
 */
class OnlineConnection extends \yii\bootstrap\Widget
{

    public function init()
    {
        parent::init();
    }

    public function run()
    {

        $leadId = null;
        $caseId = null;

        if(Yii::$app->controller->action->uniqueId === 'lead/view') {

            $leadId = Yii::$app->request->get('id');
            if(!$leadId) {
                $gid = Yii::$app->request->get('gid');
                if($gid) {
                    $lead = Lead2::find()->select(['id'])->where(['gid' => $gid])->asArray()->one();
                    if ($lead && $lead['id']) {
                        $leadId = $lead['id'];
                        unset($lead);
                    }
                }
            }
        }

        if(Yii::$app->controller->action->uniqueId === 'cases/view') {
            $gid = Yii::$app->request->get('gid');
            if($gid) {
                $case = Cases::find()->select(['cs_id'])->where(['cs_gid' => $gid])->limit(1)->asArray()->one();
                if ($case && $case['cs_id']) {
                    $caseId = $case['cs_id'];
                    unset($case);
                }
            }
        }

        return $this->render('online_connection', ['leadId' => $leadId, 'caseId' => $caseId]);
    }
}
