<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;


use common\models\Lead2;
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

        return $this->render('online_connection', ['leadId' => $leadId]);
    }
}
