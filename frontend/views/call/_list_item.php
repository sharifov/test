<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\models\Email;
use \common\models\Sms;
use \common\models\Call;

/* @var $this yii\web\View */
/* @var $model Call */

?>

    <div class="col-md-12">


            <table class="table <?=($model->c_call_status === Call::CALL_STATUS_CANCELED || $model->c_call_status === Call::CALL_STATUS_NO_ANSWER || $model->c_call_status === Call::CALL_STATUS_BUSY) ? '' : 'table-striped'?>">
                <tr <?=($model->c_call_status === Call::CALL_STATUS_CANCELED || $model->c_call_status === Call::CALL_STATUS_NO_ANSWER || $model->c_call_status === Call::CALL_STATUS_BUSY) ? 'class="danger"' : ''?>>
                    <td width="50">
                        <u><?=$model->c_id?></u><br>
                        <?php if($model->c_call_type_id === Call::CALL_TYPE_IN):?>
                            <?=Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right fa-lg text-success'])?>
                        <?php else: ?>
                            <?=Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-left fa-lg text-info'])?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center" width="100">

                        <?php if($model->c_call_type_id === Call::CALL_TYPE_IN):?>
                            <div><i class="fa fa-male text-info fa-2x fa-border"></i></div>
                            <?=$model->c_from?>
                        <?php else: ?>
                            <?php if($model->c_created_user_id):?>
                                <i class="fa fa-user fa-2x fa-border"></i><br>
                                <?=Html::encode($model->cCreatedUser->username)?>
                            <?php else: ?>
                                <i class="fa fa-phone fa-2x fa-border"></i><br>
                                <?=$model->c_from?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center" width="130">
                        <?php if($model->c_call_type_id === Call::CALL_TYPE_IN):?>
                            Incoming
                        <?php else:?>
                            Outgoing
                        <?php endif;?>
                        <br>
                        <span class="badge badge-info"><?=$model->cProject->name?></span>
                    </td>

                    <?php //if($model->c_call_status === Call::CALL_STATUS_RINGING || $model->c_call_status === Call::CALL_STATUS_IN_PROGRESS): ?>

                        <td class="text-left">
                            <?php if($model->c_lead_id && $model->cLead):?>
                                <i><?=Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['data-pjax' => 0, 'target' => '_blank'])?></i><br>
                                <?php
                                    $segments = $model->cLead->leadFlightSegments;
                                    $segmentData = [];
                                    if ($segments) {
                                        foreach ($segments as $sk => $segment) {
                                            $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                                        }
                                    }

                                    $segmentStr = implode('<br>', $segmentData);
                                    echo $segmentStr;
                                ?>


                                <?//=$model->c_lead_id?>
                            <?php endif; ?>
                        </td>
                    <?php //endif; ?>

                    <td class="text-center" width="160">
                        <?php
                            if($model->c_call_status === Call::CALL_STATUS_RINGING) {
                                $icon = 'fa fa-refresh fa-pulse fa-fw text-danger';
                            } elseif($model->c_call_status === Call::CALL_STATUS_IN_PROGRESS) {
                                $icon = 'fa fa-spinner fa-pulse fa-fw';
                            } elseif($model->c_call_status === Call::CALL_STATUS_QUEUE) {
                                $icon = 'fa fa-pause';
                            } elseif($model->c_call_status === Call::CALL_STATUS_COMPLETED) {
                                $icon = 'fa fa-trophy text-success';
                            } elseif($model->c_call_status === Call::CALL_STATUS_CANCELED || $model->c_call_status === Call::CALL_STATUS_NO_ANSWER || $model->c_call_status === Call::CALL_STATUS_BUSY) {
                                $icon = 'fa fa-times-circle text-danger';
                            } else {
                                $icon = '';
                            }
                        ?>
                        <i class="<?=$icon?>"></i> <?=$model->getStatusName()?><br>
                        <?php
                            $sec = 0;
                            if($model->c_updated_dt) {

                                if(in_array($model->c_call_status, [Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS, Call::CALL_STATUS_QUEUE])) {
                                    $sec = time() - strtotime($model->c_updated_dt);
                                } else {
                                    $sec = $model->c_call_duration ?: strtotime($model->c_updated_dt) - strtotime($model->c_created_dt);
                                }
                            }

                            //echo $sec;

                        ?>

                        <?php if(in_array($model->c_call_status, [Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS, Call::CALL_STATUS_QUEUE])):?>
                            <span class="badge badge-warning timer" data-sec="<?=$sec?>" data-control="start" data-format="%M:%S" title="<?=Yii::$app->formatter->asDuration($sec)?>">
                                00:00
                            </span>
                        <?php else: ?>
                            <span class="badge badge-primary timer" data-sec="<?=$sec?>" data-control="pause" data-format="%M:%S" title="<?=Yii::$app->formatter->asDuration($sec)?>">
                                00:00
                            </span>
                            &nbsp;&nbsp;&nbsp;<?=Yii::$app->formatter->asRelativeTime(strtotime($model->c_created_dt))?>
                        <?php endif;?>
                    </td>



                    <td class="text-center" width="110">
                        <?php if($model->c_call_type_id === Call::CALL_TYPE_IN):?>
                            <div>
                                <?php if($model->c_created_user_id):?>
                                    <i class="fa fa-user fa-2x fa-border"></i><br>
                                    <?=Html::encode($model->cCreatedUser->username)?>
                                <?php else: ?>
                                    <i class="fa fa-phone fa-2x fa-border"></i><br>
                                    <?=$model->c_to?>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div>
                                <i class="fa fa-male text-info fa-2x fa-border"></i>
                            </div>
                            <?=$model->c_to?>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

    </div>


