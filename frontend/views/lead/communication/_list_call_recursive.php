<?php

use common\models\Employee;
use yii\helpers\Html;
use \common\models\Call;

/* @var $callList Call[] */

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>
<?php if ($callList):?>
    <table class="table table-condensed" style="background-color: rgba(255, 255,255, .7)">
        <?php foreach ($callList as $callItem):?>
            <tr>
                <td style="width:80px">
                    <?php if ($user->isAdmin()):?>
                        <u title="SID: <?=Html::encode($callItem->c_call_sid)?>"><?=Html::a($callItem->c_id, ['call/view', 'id' => $callItem->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u>
                    <?php endif; ?>

                    <?php if ($callItem->cDep):?>
                        <br>
                        <?= Html::encode($callItem->cDep->dep_name)?>
                    <?php endif; ?>
                </td>
                <td class="text-left">
                    <?=$callItem->getStatusIcon()?>  <?=$callItem->getStatusName()?>
                </td>
                <td class="text-center" style="width: 70px">
                    <?php if ($callItem->c_call_duration):?>
                        <span class="badge badge-warning" title="Duration: <?=Yii::$app->formatter->asDuration($callItem->c_call_duration)?>"><?=gmdate('i:s', $callItem->c_call_duration)?></span>
                    <?php endif;?>
                </td>
                <td>
                    <?php if ($callItem->recordingUrl):?>
                        <?=Html::button(gmdate('i:s', $callItem->recordingUrl) . ' <i class="fa fa-play-circle-o"></i>',
                            ['class' => 'btn btn-' . ($callItem->recordingUrl < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $callItem->recordingUrl /*yii\helpers\Url::to(['call/record', 'sid' =>  $callItem->c_call_sid ])*/ ]) ?>
                    <?php endif;?>
                </td>
                <td class="text-center">
                    <small><?=Yii::$app->formatter->asRelativeTime(strtotime($callItem->c_created_dt))?></small>
                </td>

                <td class="text-center" style="width: 90px">
                    <small><i class="fa fa-clock-o"></i> <?=Yii::$app->formatter->asDatetime(strtotime($callItem->c_created_dt), 'php:H:i:s')?></small>
                </td>

                <td class="text-left" style="width:150px">
                    <?php if($callItem->isIn()):?>
                        <div>
                            <?php if($callItem->c_created_user_id):?>
                                <i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->cCreatedUser->username)?>
                            <?php else: ?>
                                <i class="fa fa-phone fa-border"></i> <?=Html::encode($callItem->c_to)?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div>
                            <?php if($callItem->c_created_user_id):?>
                                <i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->cCreatedUser->username)?>
                            <?php else: ?>
                                <i class="fa fa-male fa-border"></i> <?=Html::encode($callItem->c_to)?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </td>
            </tr>
            <?php if ($callItem->calls):?>
                <tr>
                    <td colspan="7">
                        <?php \sales\helpers\communication\CommunicationHelper::renderChildCallsRecursive($callItem->calls)?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach;?>
    </table>
<?php endif; ?>