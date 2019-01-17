<?php
/* @var $this \yii\web\View */
/* @var $host string */
/* @var $grav_url string */

use yii\helpers\Html;

?>

<div class="navbar nav_title" style="border: 0;">
    <a href="/" class="site_title"><span title="<?=$host?>"><i class="fa fa-dollar"></i> Sales</span></a>
</div>
<div class="clearfix"></div>

<div class="profile">
    <div class="profile_pic">
        <?=Html::img($grav_url, ['alt' => 'avatar', 'class' => 'img-circle profile_img'])?>
    </div>
    <div class="profile_info">
        <span><?=Html::encode(Yii::$app->user->identity->role)?></span>
        <h2><i class="fa fa-user"></i> <?=Html::encode(Yii::$app->user->identity->username)?></h2>
    </div>
</div>
<br />
