<?php

use common\models\search\ClientSearch;
use common\models\search\DepartmentPhoneProjectSearch;
use common\models\search\DepartmentSearch;
use common\models\search\ProjectSearch;
use common\models\search\UserProfileSearch;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $systemSettingsDataProvider yii\data\ActiveDataProvider */
/* @var $userProfileSearchModel UserProfileSearch */
/* @var $userProfileDataProvider yii\data\ActiveDataProvider */
/* @var $clientSearchModel ClientSearch */
/* @var $clientDataProvider yii\data\ActiveDataProvider */
/* @var $projectDataProvider yii\data\ActiveDataProvider */
/* @var $projectSearchModel ProjectSearch */
/* @var $departmentDataProvider yii\data\ActiveDataProvider */
/* @var $departmentSearchModel DepartmentSearch */
/* @var $departmentPhoneProjectDataProvider yii\data\ActiveDataProvider */
/* @var $departmentPhoneProjectSearchModel DepartmentPhoneProjectSearch */

$this->title = 'Call recording disabled';
$this->params['breadcrumbs'][] = $this->title;

$tabs[] = [
    'id' => 'system',
    'name' => 'System setting',
    'content' => $this->render('system', [
        'systemSettingsDataProvider' => $systemSettingsDataProvider,
    ]),
];

$tabs[] = [
    'id' => 'user-profile',
    'name' => 'Users',
    'content' => $this->render('user-profile', [
        'userProfileSearchModel' => $userProfileSearchModel,
        'userProfileDataProvider' => $userProfileDataProvider,
    ]),
];

$tabs[] = [
    'id' => 'project',
    'name' => 'Projects',
    'content' => $this->render('project', [
        'projectSearchModel' => $projectSearchModel,
        'projectDataProvider' => $projectDataProvider,
    ]),
];

$tabs[] = [
    'id' => 'departments',
    'name' => 'Departments',
    'content' => $this->render('department', [
        'departmentSearchModel' => $departmentSearchModel,
        'departmentDataProvider' => $departmentDataProvider,
    ]),
];

$tabs[] = [
    'id' => 'departments-phone',
    'name' => 'Departments phone',
    'content' => $this->render('department-phone', [
        'departmentPhoneProjectSearchModel' => $departmentPhoneProjectSearchModel,
        'departmentPhoneProjectDataProvider' => $departmentPhoneProjectDataProvider,
    ]),
];

$tabs[] = [
    'id' => 'clients',
    'name' => 'Clients',
    'content' => $this->render('client', [
        'clientSearchModel' => $clientSearchModel,
        'clientDataProvider' => $clientDataProvider,
    ]),
];

?>

<div class="row">
    <div class="col-md-12">
        <h1><?= Html::encode($this->title) ?></h1>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <?php foreach ($tabs as $key => $tab) : ?>
                    <?php if ($key === 0) : ?>
                        <a class="nav-item nav-link active" id="nav-<?= $tab['id']?>-tab" data-toggle="tab" href="#nav-<?= $tab['id']?>" role="tab" aria-controls="nav-<?= $tab['id']?>" aria-selected="true"><?= $tab['name']?></a>
                    <?php else : ?>
                        <a class="nav-item nav-link" id="nav-<?= $tab['id']?>-tab" data-toggle="tab" href="#nav-<?= $tab['id']?>" role="tab" aria-controls="nav-<?= $tab['id']?>" aria-selected="false"><?= $tab['name']?></a>
                    <?php endif;?>
                <?php endforeach; ?>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <br>
            <?php foreach ($tabs as $key => $tab) : ?>
                <?php if ($key === 0) : ?>
                    <div class="tab-pane fade show active" id="nav-<?= $tab['id']?>" role="tabpanel" aria-labelledby="nav-<?= $tab['id']?>-tab"><?= $tab['content']?></div>
                <?php else : ?>
                    <div class="tab-pane fade" id="nav-<?= $tab['id']?>" role="tabpanel" aria-labelledby="nav-<?= $tab['id']?>-tab"><?= $tab['content']?></div>
                <?php endif;?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
