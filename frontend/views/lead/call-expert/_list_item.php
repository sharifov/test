<?php
/* @var $this yii\web\View */
/* @var $model \common\models\LeadCallExpert */
?>

<div class="row">
    <div class="col-md-12" style="margin-bottom: 4px">
        Id: <?=$model->lce_id?>, <?=$model->getStatusLabel()?>
        <?php
            if($model->lce_response_lead_quotes) {
                echo ', Quotes: ';
                $json = @json_decode($model->lce_response_lead_quotes, true);
                $uIds = [];
                if(is_array($json) && $json) {
                    foreach ($json as $uid) {
                        $uIds[] = \yii\helpers\Html::a($uid, '#', ['data-pjax' => 0, 'class' => 'link2quote', 'data-uid' =>  $uid]);
                    }
                }

                if($uIds) {
                    echo implode(', ', $uIds);
                }
            }
        ?>
    </div>
    <div class="col-md-6">
        <table class="table table-striped table-bordered">
            <tr>
                <td><i class="fa fa-user"></i> <?=$model->lceAgentUser ? $model->lceAgentUser->username : '-'?>,
                    <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime($model->lce_request_dt) ?>
                </td>
            </tr>
            <tr>
                <td><?=nl2br(\yii\helpers\Html::encode($model->lce_request_text))?></td>
            </tr>
        </table>
    </div>

    <div class="col-md-6">
        <table class="table table-striped table-bordered">
            <tr>
                <td>
                    <?=$model->lce_expert_username ? '<i class="fa fa-user-secret"></i> ' . \yii\helpers\Html::encode($model->lce_expert_username) . ' ('.$model->lce_expert_user_id.')': ''?>
                    <?=$model->lce_response_dt ? ', <i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime($model->lce_response_dt): '' ?>
                </td>
            </tr>
            <tr>
                <td><?=nl2br(\yii\helpers\Html::encode($model->lce_response_text))?></td>
            </tr>
        </table>
    </div>
</div>