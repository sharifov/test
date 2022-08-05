<?php

use src\helpers\text\StringHelper;

/* @var $model \common\models\Notifications[] */
/* @var $newCount integer */
?>
<?php yii\widgets\Pjax::begin(['id' => 'notify-pjax', 'timeout' => false, 'enablePushState' => false, 'enableReplaceState' => false, 'options' => [
        'tag' => 'li',
        'class' => 'dropdown open',
        'role' => 'presentation',
]])?>
    <a href="javascript:;" class="dropdown-toggle info-number" title="Notifications" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-comment-o"></i>
        <?php if ($newCount) : ?>
            <span class="badge bg-green"><?=$newCount?></span>
        <?php endif;?>
    </a>

    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu" x-placement="bottom-end">
        <?php

        $soundPlay = false;

        if ($model) :
            foreach ($model as $n => $item) : ?>
            <li>
                <a href="<?=\yii\helpers\Url::to(['/notifications/view2', 'id' => $item->n_id])?>" data-pjax="0">
                    <span class="glyphicon glyphicon-info-sign"> <?php //remove-sign, ok-sign, question-sign ?>
                    </span>
                    <span>
                        <span><?=\yii\helpers\Html::encode($item->n_title)?></span>
                        <span class="time"><?=Yii::$app->formatter->asRelativeTime(strtotime($item->n_created_dt))?></span>
                    </span>
                    <span class="message">
                        <?= StringHelper::truncate(StringHelper::stripHtmlTags($item->n_message), 80, '...');?><br>
                    </span>
                </a>
                <?php
                if ($item->n_popup && !$item->n_popup_show) :
                    $soundPlay = true;

                    $desktopMessage = strip_tags($item->n_message);
                    $desktopMessage = str_replace('"', '\"', $desktopMessage);

                    $message = str_replace("\r\n", '', $item->n_message);
                    $message = str_replace("\n", '', $message);
                    $message = str_replace('"', '\"', $message);

                    $type = \common\models\Notifications::getNotifyType($item->n_type_id);

                    if ($n === 0) {
                        $js2 = '
                            createDesktopNotify(
                                "notif",
                                "' . \yii\helpers\Html::encode($item->n_title) . '",
                                ' . $message . '",
                                "' . $type . '",
                            );';

//                            .get().click(function(e) {
//                            });

                        /*nonblock: {
                                   nonblock: true
                               },*/

//                            createNotifyByObject({
//                                title: "'.\yii\helpers\Html::encode($item->n_title).'",
//                                type: "'.$type.'",
//                                text: "'.$message.'",
//                                hide: true
//                            });
//                            ';

                        $this->registerJs($js2, \yii\web\View::POS_READY);
//                        break;
                    }
                    ?>
                <?php endif;?>

                <?php
//                    if($n >= 10) {
//                        break;
//                    }
                ?>

            </li>
            <?php endforeach; ?>
        <?php endif;?>
        <li>
            <div class="text-center">
                <?=\yii\helpers\Html::a('<i class="fa fa-search"></i> <strong>See all Notifications</strong>', ['/notifications/list'], ['data-pjax' => 0])?>
            </div>

            <?php
            if ($newCount) {
                $jsDiv = '<span class="label-success label pull-right">' . $newCount . '</span>';
                $this->registerJs('favicon.badge(' . $newCount . ');', \yii\web\View::POS_READY);
            } else {
                $jsDiv = '';
                //$this->registerJs('favicon.badge(10);', \yii\web\View::POS_READY);
                $this->registerJs('favicon.reset();', \yii\web\View::POS_READY);
            }

                $this->registerJs("$('#div-cnt-notification').html('" . $jsDiv . "'); ", \yii\web\View::POS_READY);
            ?>
        </li>

    </ul>
    <?php
    if ($soundPlay) {
        $this->registerJs('$(function() {soundNotification();});', \yii\web\View::POS_READY);
    }
    ?>

<?php yii\widgets\Pjax::end() ?>

<?php
$notifyUrl = \yii\helpers\Url::to(['/notifications/pjax-notify']);

$js = <<<JS

    const notifyUrl = '$notifyUrl';
    function updatePjaxNotify() {
        $.pjax.reload({url: notifyUrl, container : '#notify-pjax', push: false, replace: false, timeout: 10000, scrollTo: false, async: false});
    }

    $("#notify-pjax").on("pjax:beforeSend", function() {
        $('#notify-pjax .info-number i').removeClass('fa-comment-o').addClass('fa-spin fa-spinner');
    });

    $("#notify-pjax").on("pjax:complete", function() {
        $('#notify-pjax .info-number i').removeClass('fa-spin fa-spinner').addClass('fa-comment-o');
    });

    $("#notify-pjax").on('pjax:timeout', function(event) {
        $('#notify-pjax .info-number i').removeClass('fa-spin fa-spinner').addClass('fa-comment-o');
        event.preventDefault()
    });

    var timerId2 = setInterval(updatePjaxNotify, 10 * 60000);
JS;

$this->registerJs($js, \yii\web\View::POS_READY);


