<?php

use common\models\Email;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/** @var int $id */
/** @var string $title */
/** @var string $createdDt */
/** @var string $message */

$time = strtotime($createdDt);

?>

<li data-id="<?= $id ?>">
    <a href="<?= Url::to(['/notifications/view2', 'id' => $id]) ?>" data-pjax="0">
        <span class="glyphicon glyphicon-info-sign"> <?php //remove-sign, ok-sign, question-sign ?></span>
        <span>
            <span><?= Html::encode($title) ?></span>
            <span class="time" data-time="<?= $time ?>"><?= Yii::$app->formatter->asRelativeTime($time) ?></span>
        </span>
        <span class="message"><?= StringHelper::truncate(Email::strip_html_tags($message), 80, '...') ?><br></span>
    </a>
</li>
