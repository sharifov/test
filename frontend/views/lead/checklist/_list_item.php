<?php
/* @var $this yii\web\View */
/* @var $model \common\models\LeadCallExpert */
?>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <td>
                    <span class="fa fa-check-square-o success"></span>
                </td>
                <td>
                    <i class="fa fa-user"></i> <?=$model->lceAgentUser ? $model->lceAgentUser->username : '-'?>
                </td>
                <td>
                    <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->lce_request_dt)) ?>
                </td>
            </tr>
        </table>
    </div>
</div>