<?php

use common\models\CallUserAccess;
use yii\helpers\Html;
use \common\models\Call;

/* @var $this yii\web\View */
/* @var $model Call */

?>

<div class="col-md-12">
    <?php
        $trClass = '';
        if ($model->isIn() && ($model->isNoAnswer() || $model->isCanceled() || $model->isBusy())) {
            $trClass = 'danger';
        }

        if ($model->c_parent_id) {
            $trClass = 'warning';
        }
    ?>

    <table class="table <?=($model->isIn() && ($model->isNoAnswer() || $model->isCanceled() || $model->isBusy())) ? '' : 'table-striped'?>">
        <tr class="<?=$trClass?>">
            <td style="width:50px">
                <u><?=Html::a($model->c_id, ['call/view', 'id' => $model->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u><br>
                <?= $model->c_parent_id ? 'p:' . Html::a($model->c_parent_id, ['call/view', 'id' => $model->c_parent_id], ['target' => '_blank', 'data-pjax' => 0]) . '<br>' : ''?>
                <?php if ($model->c_call_type_id === Call::CALL_TYPE_IN):?>
                    <?=Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right fa-lg text-success'])?>
                <?php else: ?>
                    <?=Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-left fa-lg text-info'])?>
                <?php endif; ?>
            </td>
            <td class="text-center" style="width:100px">

                <?php if($model->isIn()):?>
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
            <td class="text-center" style="width:130px">
                <?php if ($model->isIn()):?>
                    In
                <?php else:?>
                    Out
                <?php endif;?>
                <br>
                <span class="badge badge-info"><?=$model->cProject ? $model->cProject->name : '-'?></span><br>
                <?php if($model->cDep):?>
                    <span class="label label-warning"><?=$model->cDep ? Html::encode($model->cDep->dep_name) : '-'?></span>
                <?php endif; ?>
                <?php if ($model->c_source_type_id):?>
                    <span class="label label-info"><?=$model->getShortSourceName()?></span>
                <?php endif; ?>
            </td>

            <?php  ?>

                <td class="text-left">
                    <?php if($model->c_lead_id && $model->cLead):?>
                        <i>l:<?=Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['data-pjax' => 0, 'target' => '_blank'])?>

                            <?//=$model->cLead->l_init_price ? ' - ' . number_format($model->cLead->l_init_price, 0) : ''?>
                        </i><br>
                        <?/*php
                            $segments = $model->cLead->leadFlightSegments;
                            $segmentData = [];
                            if ($segments) {
                                foreach ($segments as $sk => $segment) {
                                    $segmentData[] =  '<small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                                }
                            }

                            $segmentStr = implode('<br>', $segmentData);
                            echo $segmentStr;*/
                        ?>


                        <?//=$model->c_lead_id?>
                    <?php endif; ?>

                    <?php if($model->c_case_id && $model->cCase):?>
                        <i>c:<?=Html::a($model->c_case_id, ['cases/view', 'gid' => $model->cCase->cs_gid], ['data-pjax' => 0, 'target' => '_blank'])?></i><br>
                    <?php endif; ?>

                    <?php if($model->isIn() && $model->cugUgs):?>
                        <?php $userGroupList = [];
                            foreach ($model->cugUgs as $userGroup) {
                                $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                            }
                            echo $userGroupList ? implode('<br>', $userGroupList) : '-';
                        ?>
                    <?php endif; ?>

                </td>
            <?php //endif; ?>

            <td class="text-left">
                <?php if($model->cuaUsers):?>
                    <?php foreach ($model->callUserAccesses as $cua):

                        switch ((int) $cua->cua_status_id) {
                            case CallUserAccess::STATUS_TYPE_PENDING:
                                $label = 'warning';
                                break;
                            case CallUserAccess::STATUS_TYPE_ACCEPT:
                                $label = 'success';
                                break;
                            case CallUserAccess::STATUS_TYPE_BUSY:
                                $label = 'danger';
                                break;
                            default:
                                $label = 'default';
                        }

                        ?>
                        <span class="label label-<?=$label?>"><i class="fa fa-user"></i> <?=Html::encode($cua->cuaUser->username)?></span>&nbsp;
                    <?php endforeach;?>
                <?php endif; ?>
            </td>

            <td class="text-center" style="width:160px">
                <?php
                    if($model->isRinging()) {
                        $icon = 'fa fa-refresh fa-pulse fa-fw text-danger';
                    } elseif($model->isInProgress()) {
                        $icon = 'fa fa-spinner fa-pulse fa-fw';
                    } elseif($model->isQueue()) {
                        $icon = 'fa fa-pause';
                    } elseif($model->isCompleted()) {
                        $icon = 'fa fa-trophy text-success';
                    } elseif($model->isCanceled() || $model->isNoAnswer() || $model->isBusy()) {
                        $icon = 'fa fa-times-circle text-danger';
                    } else {
                        $icon = '';
                    }
                ?>
                <i class="<?=$icon?>"></i>
                <?=$model->getStatusName()?><br>
                <?php
                    $sec = 0;
                    if($model->c_updated_dt) {

                        if($model->isIvr() || $model->isRinging() || $model->isInProgress() || $model->isQueue()) {
                            $sec = time() - strtotime($model->c_updated_dt);
                        } else {
                            $sec = $model->c_call_duration ?: strtotime($model->c_updated_dt) - strtotime($model->c_created_dt);
                        }
                    }
             ?>

                <?php if ($model->isIvr() || $model->isRinging() || $model->isInProgress() || $model->isQueue()):?>
                    <span class="badge badge-warning timer" data-sec="<?=$sec?>" data-control="start" data-format="%M:%S" title="<?=Yii::$app->formatter->asDuration($sec)?>">
                        00:00
                    </span>
                <?php else: ?>
                    <span class="badge badge-primary timer" data-sec="<?=$sec?>" data-control="pause" data-format="%M:%S" title="<?=Yii::$app->formatter->asDuration($sec)?>">
                        00:00
                    </span>
                    &nbsp;&nbsp;&nbsp;<?=Yii::$app->formatter->asRelativeTime(strtotime($model->c_created_dt))?>
                <?php endif;?>

                <br><?=$model->getStatusName2()?><br>
            </td>



            <td class="text-center" style="width:110px">
                <?php if($model->isIn()):?>
                    <div>
                        <?php if((int) $model->c_source_type_id === Call::SOURCE_GENERAL_LINE):?>
                            <i class="fa fa-fax fa-2x fa-border"></i>
                        <?php endif;?>

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


