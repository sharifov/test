<?php

/**
 * @var $client Client
 */

use common\models\Client;
use yii\widgets\Pjax;

?>

<? Pjax::begin(['id' => 'pjax-client-manage-name', 'enablePushState' => false, 'enableReplaceState' => false]) ?>

<table class="table table-bordered table-condensed" style="margin-bottom: 0;">
	<tr>
		<td><?= $client->getAttributeLabel('firstName') ?>:</td>
		<td><?= $client->first_name ?></td>
	</tr>
	<tr>
		<td><?= $client->getAttributeLabel('last_name') ?>:</td>
		<td><?= $client->last_name ?></td>
	</tr>
	<? if(!empty($client->middle_name)): ?>
		<tr>
			<td><?= $client->getAttributeLabel('middle_name') ?></td>
			<td><?= $client->middle_name ?></td>
		</tr>
	<? endif; ?>
</table>

<? Pjax::end() ?>