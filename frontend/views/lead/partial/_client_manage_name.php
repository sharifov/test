<?php

/**
 * @var $client Client
 */

use common\models\Client;
use yii\widgets\Pjax;

?>

<?php Pjax::begin(['id' => 'pjax-client-manage-name', 'enablePushState' => false, 'enableReplaceState' => false]) ?>

<table class="table table-bordered table-condensed" style="margin-bottom: 0;">
	<tr>
		<td style="width: 40%; background-color: #eef3f9"><?= $client->getAttributeLabel('firstName') ?></td>
		<td><?= \yii\helpers\Html::encode($client->first_name) ?></td>
	</tr>
	<tr>
		<td style="background-color: #eef3f9"><?= $client->getAttributeLabel('last_name') ?></td>
		<td><?= \yii\helpers\Html::encode($client->last_name) ?></td>
	</tr>
	<?php if(!empty($client->middle_name)): ?>
		<tr>
			<td style="background-color: #eef3f9"><?= $client->getAttributeLabel('middle_name') ?></td>
			<td><?= \yii\helpers\Html::encode($client->middle_name) ?></td>
		</tr>
	<?php endif; ?>
</table>

<?php Pjax::end() ?>