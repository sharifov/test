<?php
use yii\helpers\Html;
use \common\models\Call;

/* @var $callList Call[] */
?>
<?php if ($callList):?>
    <table class="table table-condensed" style="background-color: rgba(255, 255,255, .7)">
        <?php foreach ($callList as $callItem):?>
            <tr>
                <?php if (Yii::$app->user->identity->isAdmin()):?>
                    <td style="width:50px">
                        <u title="SID: <?=Html::encode($callItem->c_call_sid)?>"><?=Html::a($callItem->c_id, ['call/view', 'id' => $callItem->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u><br>
                    </td>
                <?php endif; ?>
                <td class="text-left">
                    <?=$callItem->getStatusIcon()?>  <?=$callItem->getStatusName()?>
                </td>
                <td class="text-center" style="width: 70px">
                    <?php if ($callItem->c_call_duration):?>
                        <span class="badge badge-warning" title="Duration: <?=Yii::$app->formatter->asDuration($callItem->c_call_duration)?>"><?=gmdate('i:s', $callItem->c_call_duration)?></span>
                    <?php endif;?>
                </td>
                <td>
                    <?php if ($callItem->c_recording_url):?>
                        <?=Html::button(gmdate('i:s', $callItem->c_recording_duration) . ' <i class="fa fa-play-circle-o"></i>',
                            ['class' => 'btn btn-' . ($callItem->c_recording_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $callItem->c_recording_url]) ?>
                    <?php endif;?>
                </td>
                <td class="text-center">
                    <?=Yii::$app->formatter->asRelativeTime(strtotime($callItem->c_created_dt))?>
                </td>

                <td class="text-center" style="width: 90px">
                    <i class="fa fa-clock-o"></i> <?=Yii::$app->formatter->asDatetime(strtotime($callItem->c_created_dt), 'php:H:i:s')?>
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