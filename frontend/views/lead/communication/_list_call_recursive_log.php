<?php

use common\models\Employee;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\CallLogStatus;
use yii\helpers\Html;

/* @var $callList CallLog[] */
/* @var $child bool */

/** @var Employee $user */
$user = Yii::$app->user->identity;
$child = $child ?? false;
?>
<?php if ($callList):?>
        <?php if ($child): ?>
	    <table class="table table-condensed" style="background-color: rgba(255, 255,255, .7); margin-bottom: 0;">
        <?php endif; ?>
		<?php foreach ($callList as $callItem):?>
			<tr>
				<td style="width:80px">
					<?php if ($user->isAdmin()):?>
						<u title="SID: <?=Html::encode($callItem->cl_call_sid)?>"><?=Html::a($callItem->cl_id, ['call/view', 'id' => $callItem->cl_id], ['target' => '_blank', 'data-pjax' => 0])?></u>
					<?php endif; ?>

					<?php if ($callItem->department):?>
						<br>
						<?= Html::encode($callItem->department->dep_name)?>
					<?php endif; ?>
				</td>
				<td class="text-left">
					<?=$callItem->getStatusIcon()?>  <?= CallLogStatus::getName($callItem->cl_status_id) ?>
				</td>
				<td class="text-center" style="width: 70px">
					<?php if ($callItem->cl_duration):?>
						<span class="badge badge-warning" title="Duration: <?=Yii::$app->formatter->asDuration($callItem->cl_duration)?>"><?=gmdate('i:s', $callItem->cl_duration)?></span>
					<?php endif;?>
				</td>
				<td>
					<?php  if ($callItem->record && $callItem->record->clr_record_sid):?>
						<?=  Html::button(gmdate('i:s', $callItem->record->clr_duration) . ' <i class="fa fa-play-circle-o"></i>',
							['class' => 'btn btn-' . ($callItem->record->clr_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $callItem->record->recordingUrl /*yii\helpers\Url::to(['call/record', 'sid' =>  $callItem->c_call_sid ])*/ ]) ?>
					<?php  endif;?>
				</td>
				<td class="text-center">
					<small><?=Yii::$app->formatter->asRelativeTime(strtotime($callItem->cl_call_created_dt))?></small>
				</td>

				<td class="text-center" style="width: 90px">
					<small><i class="fa fa-clock-o"></i> <?=Yii::$app->formatter->asDatetime(strtotime($callItem->cl_call_created_dt), 'php:H:i:s')?></small>
				</td>

				<td class="text-left" style="width:150px">
					<?php if($callItem->isIn()):?>
						<div>
							<?php if($callItem->user):?>
								<i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->user->username)?>
							<?php else: ?>
								<i class="fa fa-phone fa-border"></i> <?=Html::encode($callItem->cl_phone_to)?>
							<?php endif; ?>
						</div>
					<?php else: ?>
						<div>
							<?php if($callItem->user):?>
								<i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->user->username)?>
							<?php else: ?>
								<i class="fa fa-male fa-border"></i> <?=Html::encode($callItem->cl_phone_to)?>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</td>
			</tr>
            <?php if ($callItem->childCalls):?>
                <?= $this->render('_list_call_recursive_log', [
                    'callList' => $callItem->childCalls,
                    'child' => true
                ]) ?>
            <?php endif; ?>
		<?php endforeach;?>
		<?php if ($child): ?>
        </table>
        <?php endif; ?>
<?php endif; ?>