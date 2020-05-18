<?php

use common\components\purifier\Purifier;
use common\components\purifier\PurifierFilter;
use common\models\Email;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var int $id */
/** @var string $title */
/** @var string $createdDt */
/** @var string $message */

$time = strtotime($createdDt);

?>

<li data-id="<?= $id ?>">
    <a href="javascript:;" onclick="notificationShow(this);" id="notification-menu-element-show" data-title="<?= Html::encode($title) ?>" data-id="<?= $id ?>">
        <span class="glyphicon glyphicon-info-sign"> </span>
        <span>
            <span><?= Html::encode($title) ?></span>
            <span class="time" data-time="<?= $time ?>"><?= Yii::$app->formatter->asRelativeTime($time) ?></span>
        </span>
        <span class="message"><?= StringHelper::truncate(Email::strip_html_tags(Purifier::purify($message, PurifierFilter::shortCodeToId())), 80, '...') ?><br></span>
    </a>
</li>
