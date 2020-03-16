<?php
/**
 * @var ImportForm $model
 * @var array $rbacDifferences
 * @var string $cacheKey
 * @var string $fileSize
 * @var string $fileName
 * @var int $cacheDuration
 */

use modules\rbacImportExport\src\forms\ImportForm;
use yii\helpers\Html;
use yii\helpers\Url;
use \yii\widgets\ActiveForm;
$this->title = 'RBAC Import';
$this->params['breadcrumbs'][] = ['label' => 'RBAC Import Export', 'url' => ['/rbac-import-export/log/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-3">
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-bars"></i> Import Form <small>Upload File</small></h2>
                <ul class="nav navbar-right panel_toolbox" style="display: flex;justify-content: flex-end;">
                    <li></li>
                    <li></li>
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

                <?= $form->errorSummary($model) ?>

                <?= $form->field($model, 'zipFile')->fileInput([
                    'accept' => '.zip'
                ]) ?>

                <?= Html::submitButton('Submit', ['class' => 'btn btn-success get-export']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 40px;">
    <?php if(isset($rbacDifferences)): ?>
        <div class="col-md-8 col-sm-8  ">
            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-bars"></i> Import <small>Data result</small></h2>
                    <ul class="nav navbar-right panel_toolbox" style="display: flex;justify-content: flex-end;">
                        <li></li>
                        <li style="display: flex; align-items: center;"><div id="timer"></div></li>
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" id="rbac-imported-data-result">

                    <?php if(!empty($rbacDifferences)): ?>
                    <ul class="nav nav-tabs bar_tabs" id="myTab" role="tablist">
                        <?php $i = 0; foreach($rbacDifferences as $key => $difference): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= !$i ? 'active' : '' ?>" id="<?= $key ?>-tab" data-toggle="tab" href="#<?= $key ?>" role="tab" aria-controls="home" aria-selected="true" style="text-transform: capitalize;"><?= $key ?></a>
                            </li>
                        <?php $i++; endforeach; ?>
                    </ul>
                    <div class="tab-content" id="myTabContent">
						<?php $i = 0; foreach($rbacDifferences as $key => $difference): ?>
                            <div class="tab-pane fade <?= !$i ? ' show active' : '' ?>" id="<?= $key ?>" role="tabpanel" aria-labelledby="<?= $key ?>-tab">
                                <?php if ($key === 'roles'): ?>
                                    <div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
                                        <?php $j = 0; foreach ($difference as $roleName => $role): ?>
                                        <?php if(is_array($role)): ?>
                                        <div class="panel">
                                            <a class="panel-heading" role="tab" id="heading-<?= $roleName ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?= $roleName ?>" aria-expanded="true" aria-controls="collapse-<?= $roleName ?>">
                                                <h5 class="panel-title" style="text-transform: capitalize"><?= $roleName ?></h5>
                                            </a>
                                            <div id="collapse-<?= $roleName ?>" class="panel-collapse collapse <?= !$j ? 'in' : '' ?>" role="tabpanel" aria-labelledby="heading-<?= $roleName ?>">
                                                <div class="panel-body" style="padding: 10px;">

                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="nav nav-tabs flex-column  bar_tabs" id="v-pills-tab-<?= $roleName ?>" role="tablist" aria-orientation="vertical">
                                                                <?php $k = 0; foreach($role as $tabName => $tab): ?>
                                                                <a class="nav-link <?= !$k ? 'active' : '' ?>" id="v-pills-<?= $roleName ?>-<?= $tabName ?>-tab" data-toggle="pill" href="#v-pills-<?= $roleName ?>-<?= $tabName ?>" role="tab" aria-controls="v-pills-<?= $roleName ?>-<?= $tabName ?>" aria-selected="true"><?= $tabName ?></a>
                                                                <?php $k++; endforeach; ?>
                                                            </div>

                                                        </div>

                                                        <div class="col-xs-9 col-md-9">
                                                                <div class="tab-content" id="v-pills-<?= $roleName ?>-tabContent" style="padding: 10px">
															    <?php $k = 0; foreach($role as $tabName => $tab): ?>
                                                                    <div class="tab-pane fade show <?= !$k ? 'active' : '' ?>" id="v-pills-<?= $roleName ?>-<?= $tabName ?>" role="tabpanel" aria-labelledby="v-pills-<?= $roleName ?>-<?= $tabName ?>-tab">
                                                                        <?php if ($tabName === 'roleInfo'): ?>
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>#</th>
                                                                                    <th>Type</th>
                                                                                    <th>Name</th>
                                                                                    <th>Rule Name</th>
                                                                                    <th>Description</th>
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
																					<?php if ($tabName !== 'action'): ?>
                                                                                        <tr>
                                                                                            <td><?= $tab['type'] ?? '' ?></td>
                                                                                            <td><?= $tab['name'] ?? '' ?></td>
                                                                                            <td><?= $tab['ruleName'] ?? '' ?></td>
                                                                                            <td><?= $tab['description'] ?? '' ?></td>
                                                                                            <td><?= $tab['action'] ?? 'insert' ?></td>
                                                                                        </tr>
																					<?php endif; ?>
                                                                                </tbody>
                                                                            </table>
                                                                        <?php elseif ($tabName === 'userIdsByRole'): ?>
                                                                        <p style="margin: 0;">Total rows: <?= count($tab) ?></p>
                                                                        <table class="table table-bordered">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>User Id</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php $u = 1; foreach ($tab as $userKey => $user): ?>
                                                                                <?php if ($userKey !== 'action'): ?>
                                                                                <tr>
                                                                                    <td><?= $u ?></td>
                                                                                    <td><?= $user ?></td>
                                                                                    <td><?= $tab['action'] ?? 'insert' ?></td>
                                                                                </tr>
                                                                                <?php $u++; endif; ?>
                                                                            <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                        <?php elseif ($tabName === 'permissionsByRole'): ?>
                                                                            <p style="margin: 0;">Total rows: <?= count($tab) ?></p>
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>#</th>
                                                                                    <th>Type</th>
                                                                                    <th>Name</th>
                                                                                    <th>Rule Name</th>
                                                                                    <th>Description</th>
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
																				<?php $p = 1; foreach ($tab as $permissionKey => $permission): ?>
                                                                                    <tr>
                                                                                        <td><?= $p ?></td>
                                                                                        <td><?= $permission['type'] ?? '' ?></td>
                                                                                        <td><?= $permission['name'] ?? $permissionKey ?></td>
                                                                                        <td><?= $permission['ruleName'] ?? '' ?></td>
                                                                                        <td><?= $permission['description'] ?? '' ?></td>
                                                                                        <td><?= $permission['action'] ?? 'insert' ?></td>
                                                                                    </tr>
																				<?php $p++; endforeach; ?>
                                                                                </tbody>
                                                                            </table>
                                                                        <?php elseif ($tabName === 'childByRole'): ?>
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>Type</th>
                                                                                    <th>Name</th>
                                                                                    <th>Rule Name</th>
                                                                                    <th>Description</th>
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
																				<?php foreach ($tab as $childByRoleKey => $childByRole): ?>
                                                                                    <tr>
                                                                                        <td><?= $childByRole['type'] ?? '' ?></td>
                                                                                        <td><?= $childByRole['name'] ?? $childByRoleKey ?></td>
                                                                                        <td><?= $childByRole['ruleName'] ?? '' ?></td>
                                                                                        <td><?= $childByRole['description'] ?? '' ?></td>
                                                                                        <td><?= $childByRole['action'] ?? 'insert' ?></td>
                                                                                    </tr>
																				<?php endforeach; ?>
                                                                                </tbody>
                                                                            </table>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php $k++; endforeach; ?>
                                                            </div>

                                                        </div>

                                                        <div class="clearfix"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php $j++; endforeach; ?>
                                    </div>
								<?php elseif ($key === 'rules'): ?>
                                    <div class="col-md-12">
                                        <p style="margin: 0;">Total rows: <?= count($difference) ?></p>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
											<?php $l = 1; foreach($difference as $rulesKey => $rule): ?>
                                                <tr>
                                                    <td><?= $l ?></td>
                                                    <td><?= $rule['name'] ?? $rulesKey ?></td>
                                                    <td><?= $rule['action'] ?? 'insert' ?></td>
                                                </tr>
											<?php $l++; endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
								<?php elseif ($key === 'permissions'): ?>
                                    <div class="col-md-12 table-search-wrap">
                                            <p style="margin: 0;">Total rows: <?= count($difference) ?></p>
<!--                                        <div style="display: flex; justify-content: space-between;align-content: center;align-items: center;">-->
<!--                                            <div class="form-group pull-right top_search">-->
<!--                                                <div class="input-group" style="margin: 0;">-->
<!--                                                    <input type="text" class="form-control search-input" data-search-by=".name" placeholder="Search for permission name..." style="height: 32px;">-->
<!--                                                    <span class="input-group-btn">-->
<!--                                                      <button class="btn btn-default" type="button" style="color: #fff;">Go!</button>-->
<!--                                                    </span>-->
<!--													--><?php //$js = <<<JS
//$(document).ready( function () {
//    $('.search-input').on('keydown', function (){
//        var val = $(this).val();
//        var searchBy = $(this).data('search-by');
//
//        if (val !== '') {
//            $(this).closest('.table-search-wrap').find('.table').each( function (i, elem) {
//                var textSearch = $(elem).find('td'+searchBy).text();
//
//                console.log(textSearch);
//            });
//        }
//    });
//})
//JS;
//													$this->registerJs($js);
//													?>
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th>Rule Name</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php $l = 1; foreach($difference as $permissionKey => $permissions): ?>
                                                <tr>
                                                    <td><?= $l ?></td>
                                                    <td><?= $permissions['type'] ?? '' ?></td>
                                                    <td class=".name"><?= $permissions['name'] ?? $permissionKey ?></td>
                                                    <td><?= $permissions['ruleName'] ?? '' ?></td>
                                                    <td><?= $permissions['description'] ?? '' ?></td>
                                                    <td><?= $permissions['action'] ?? 'insert' ?></td>
                                                </tr>
                                                <?php $l++; endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
								<?php endif; ?>
                            </div>

						<?php $i++; endforeach; ?>
                    </div>
				    <?= Html::submitButton('<i class="fas fa-file-export"></i> Import', ['class' => 'btn btn-success do-import']) ?>
						<?php
						$url = Url::toRoute(['/rbac-import-export/import/import-data']);
						/**
						 * @var string $cacheKey
						 * @var string $fileSize
						 * @var string $fileName
						 */
						$js = <<<JS
            $('.do-import').on('click', function () {
                var btnHtml = $(this).html();
                var btn = $(this);
                $.ajax({
                    type: 'post',
                    url: '$url',
                    dataType: 'json',
                    data: {cacheKey: '$cacheKey', fileSize: '$fileSize', fileName: '$fileName'},
                    beforeSend: function () {
                        btn.html('<i class="fa fa-spin fa-spinner"></i> Importing...').attr('disabled', true).addClass('disabled');
                    },
                    success: function(data) {
                        if (data.error) {
                            new PNotify({title: "Warning", type: "error", text: data.message , hide: true});
                        } else {
                            new PNotify({title: "Success", type: "success", text: data.message , hide: true});
                        }
                    },
                    complete: function () {
                        btn.html(btnHtml).removeAttr('disabled').removeClass('disabled');
                    }
                })
            });
            
            function startTimer(duration, display) {
                var timer = duration, minutes, seconds;
                var interval = setInterval(function () {
                    minutes = parseInt(timer / 60, 10)
                    seconds = parseInt(timer % 60, 10);
            
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
            
                    display.innerHTML = 'How long will the imported data be stored in the cache: <label class="control-label" style="margin: 0;">' + minutes + ":" + seconds + '</label>';
            
                    if (--timer < 0) {
                        // timer = duration;
                        display.textContent = 'Imported data has been removed from the cache; Download the data again.';
                        $('#rbac-imported-data-result').hide();
                        
                        clearInterval(interval);
                    }
                }, 1000);
            }
            
            var timerDiv = document.getElementById('timer');
            startTimer('$cacheDuration', timerDiv);
JS;
						$this->registerJs($js);
						?>
                    <?php else: ?>
                        <div class="text-center">
                            <p>Differences not found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
