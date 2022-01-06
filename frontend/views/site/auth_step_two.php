<?php

/**
 * @var \yii\web\View $this
 * @var \sales\model\userAuthClient\entity\UserAuthClient[] $authClients
 */

use yii\helpers\StringHelper;
use yii\helpers\Url;

?>

<div class="login_wrapper">
    <div class="animate form login_form">
        <section class="login_content">

            <div style="padding: 0; position: relative; margin: 20px 0;">
                <h1>Choose profile</h1>
                <ul class="list-unstyled">
                    <?php foreach ($authClients as $authClient) : ?>
                        <?php $userRoles = implode(', ', $authClient->user->getRoles(true)); ?>
                        <li>
                            <a class="profile_link" href="<?= Url::to(['/site/auth-step-two', 'user-id' => $authClient->uac_user_id])?>">
                                <img src="<?= $authClient->user->getGravatarUrl() ?>" alt="avatar" class="user_avatar" style="margin-right: 15px;">
                                <div class="text-left">
                                    <span>
                                        <h6><b><?= $authClient->user->nickname ?></b> <span title="<?= $userRoles ?>">(<?= StringHelper::truncate($userRoles, 20, '...') ?>)</span></h6>
                                    </span>
                                    <span>
                                        <?= $authClient->user->email ?>
                                    </span>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="clearfix"></div>

                <div class="separator">
                    <div class="clearfix"></div>

                    <div class="text-center">
                        <?= \yii\helpers\Html::a(' <i class="fa fa-arrow-left"></i> Go Back', ['/site/login'], ['class' => 'btn btn-default']) ?>
                    </div>

                    <br />
                    <div>
                        <h1>CRM - Sales!</h1>
                        <p>©2017-<?=date('Y')?> All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
