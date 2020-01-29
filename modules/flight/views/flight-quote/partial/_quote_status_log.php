<?php

/**
 * @var $flightQuote FlightQuote[]
 * @var $this \yii\web\View
 */

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteStatusLog;


$quoteStatusLog = $flightQuote->flightQuoteStatusLogs;
?>


<div class="sl-events-log">
	<table class="table table-neutral">
		<?php if (!empty($quoteStatusLog)) : ?>
			<thead>
			<tr>
				<th>Status</th>
				<th>Agent</th>
				<th>Created</th>
			</tr>
			</thead>
		<?php endif; ?>
		<tbody>
		<?php if (!empty($quoteStatusLog)) :
			foreach ($quoteStatusLog as $status) : ?>
				<tr>
					<th><?= FlightQuoteStatusLog::getStatusText($status->qsl_status_id) ?></th>
					<th><?= empty($status->qsl_created_user_id) ? 'System' : $status->qslCreatedUser->username ?></th>
					<th><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($status->qsl_created_dt)) ?></th>
				</tr>
			<?php endforeach;
		else : ?>
			<tr>
				<th class="text-bold text-center">Not found info for this request!</th>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
</div>
