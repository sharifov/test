<?php
/* @var $user \common\models\Employee */
/* @var $userGroups array */
/* @var $departments array */
?>
<li>
    <a href="javascript:;" title="User Info">
        <span style="font-size: 18px; padding-right: 20px"><?= implode(', ', $user->getRoles()) ?></span>

        <?php if ($departments): ?>
            <span style="padding-right: 20px">
            Departments:
            <?php
            foreach ($departments as $department) {
                echo \yii\helpers\Html::tag('span',
                        '<i class="fa fa-cube"></i> ' . \yii\helpers\Html::encode($department),
                        ['class' => 'label label-default']) . ' ';
            }
            ?>
            </span>
        <?php endif; ?>

        <?php if ($userGroups): ?>
            <span style="padding-right: 20px">
            Groups:
            <?php
            foreach ($userGroups as $group) {
                echo \yii\helpers\Html::tag('span', '<i class="fa fa-users"></i> ' . \yii\helpers\Html::encode($group),
                        ['class' => 'label label-default']) . ' ';
            }
            ?>
        </span>
        <?php endif; ?>

<!--        <span>-->
<!--            Updated: <i class="fa fa-clock-o"></i> --><?//= Yii::$app->formatter->asTime(time(), 'php:H:i:s') ?>
<!--        </span>-->

    </a>
</li>