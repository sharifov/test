<?php

/**
 * @var $client Client
 */

use common\models\Client;
use yii\widgets\Pjax;

?>

<?php Pjax::begin(['id' => 'pjax-client-manage-name', 'enablePushState' => false, 'enableReplaceState' => false]) ?>

<table class="table table-bordered table-condensed">
    <tr>
        <td style="width: 32px; background-color: #eef3f9"><i class="fa fa-user"></i></td>
        <td style="width: 100px; background-color: #eef3f9"><?= $client->getAttributeLabel('firstName') ?></td>
        <td><?= \sales\model\client\helpers\ClientFormatter::formatName($client) ?></td>
    </tr>
    <tr>
        <td style="background-color: #eef3f9"><i class="fa fa-user"></i></td>
        <td style="background-color: #eef3f9"><?= $client->getAttributeLabel('last_name') ?></td>
        <td><?= \yii\helpers\Html::encode($client->last_name) ?></td>
    </tr>
    <?php if (!empty($client->middle_name)) : ?>
        <tr>
            <td style="background-color: #eef3f9"><i class="fa fa-user"></i></td>
            <td style="background-color: #eef3f9"><?= $client->getAttributeLabel('middle_name') ?></td>
            <td><?= \yii\helpers\Html::encode($client->middle_name) ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td colspan="2" style="background-color: #eef3f9">Project</td>
        <td><?= $client->cl_project_id ? $client->project->name : '' ?></td>
    </tr>
</table>

<?php Pjax::end() ?>