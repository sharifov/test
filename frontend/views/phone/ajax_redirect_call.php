<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $users \common\models\Employee[] */
/* @var $call \common\models\Call */
/* @var $error string */
?>
<div class="ajax-redirect-call">
    <?php if($error):?>
        <pre><?=$error?></pre>
    <?php else: ?>
        <?php if ($users):?>
        <table class="table" style="margin: 0" id="redirect-agent-table">
            <tr>
                <th>Username</th>
                <th>Roles</th>
                <th>Action</th>
            </tr>
            <?php foreach ($users as $userModel): ?>
                <?php
                    $roles = $userModel->roles;
                ?>
                <tr>
                    <td><?=Html::encode($userModel->username)?></td>
                    <td><?=( is_array($roles) ? implode(', ', $roles) : '-')?></td>
                    <td><?=Html::button('Redirect', [
                                'class' => 'btn btn-sm btn-primary redirect-agent-data2',
                                'data-agentid' => $userModel->id,
                                'data-projectid' => $call->c_project_id,
                                'data-dep-id' => $call->c_dep_id])?>
                    </td>
                </tr>
            <?php endforeach;?>
            </table>
        <?php endif;?>
    <?php endif;?>
</div>