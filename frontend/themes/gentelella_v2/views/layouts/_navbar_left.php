<?php
/* @var $this \yii\web\View */
/* @var $host string */
/* @var $grav_url string */

use yii\helpers\Html;

?>

<div class="navbar nav_title" style="border: 0;">
    <?=Html::a('<span title="' . $host . '"><i class="fa fa-slideshare"></i> Sales CRM</span>', ['/site/index'], ['class' => 'site_title nav-sm-hidden'])?>
	<?=Html::a('<i class="fa fa-slideshare"></i>', ['/site/index'], ['class' => 'site_title nav-md-hidden'])?>
</div>
<div class="clearfix"></div>

<div class="profile">
    <div class="profile_pic">
        <?=Html::img($grav_url, ['alt' => 'avatar', 'class' => 'img-circle profile_img'])?>
    </div>
    <div class="profile_info">
        <span><?=implode(', ', Yii::$app->user->identity->getRoles())?></span>
        <h2><i class="fa fa-user"></i> <?=Html::encode(Yii::$app->user->identity->username)?></h2>
    </div>
</div>
<!--<br />-->
