<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\take\QaTaskTakeService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?php if (QaTaskTakeService::can($model, Auth::id())): ?>
    <?= Html::a(
        'Take',
        Url::to(['/qa-task/qa-task-action/take', 'gid' => $model->t_gid]),
        [
            'class' => 'btn btn-' . QaTaskStatus::getCssClass(QaTaskStatus::PROCESSING),
            'title' => 'Take',
        ]
    ) ?>
<?php endif; ?>

<?php
