<?php

namespace frontend\controllers;

use src\helpers\app\AppHelper;
use src\rbac\form\RbacRoleManagementForm;
use src\rbac\services\RbacQueryService;
use src\rbac\services\RbacRoleManagementService;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\rbac\Item;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii2mod\rbac\models\search\AuthItemSearch;

class RbacRoleManagementController extends FController
{
    /**
     * @var string search class name for auth items search
     */
    public $searchClass = [
        'class' => AuthItemSearch::class,
    ];


    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs'  => [
                    'class'   => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow'   => true,
                            'actions' => ['index', 'clone', 'merge', 'exclude'],
                            'roles'   => ['admin' , 'superadmin'],
                        ],
                    ],
                ]
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel       = \Yii::createObject($this->searchClass);
        $searchModel->type = Item::TYPE_ROLE;
        $dataProvider      = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    public function actionClone()
    {
        $roleName = (string)\Yii::$app->request->get('name');
        $item     = RbacQueryService::getRoleByName($roleName);
        if (empty($item)) {
            throw new NotFoundHttpException('Role not found');
        }
        $model       = new RbacRoleManagementForm();
        $model->name = $roleName;
        $roleList    = RbacQueryService::getRolesList();
        $roleList    = array_combine($roleList, $roleList);
        if (\Yii::$app->request->isPost) {
            try {
                $model->load(\Yii::$app->request->post());
                if (!$model->validate()) {
                    throw new \RuntimeException(implode(', ', $model->getErrorSummary(true)));
                }
                $result = RbacRoleManagementService::clonePermissions($model->donor_name, $model->name);
                if (!$result) {
                    throw new \RuntimeException('Saving Error');
                }
                \Yii::$app->session->setFlash('success', 'Form Saved');
            } catch (\RuntimeException | \DomainException $e) {
                \Yii::warning(AppHelper::throwableFormatter($e), 'RbacRoleManagementController::actionClone:exception');
                \Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e), 'RbacRoleManagementController:actionClone:Throwable');
                \Yii::$app->session->setFlash('error', 'Server Error');
            }
        }
        return $this->render('clone', [
            'model'    => $model,
            'roleList' => $roleList,
        ]);
    }

    public function actionMerge()
    {
        $roleName = (string)\Yii::$app->request->get('name');
        $item     = RbacQueryService::getRoleByName($roleName);
        if (empty($item)) {
            throw new NotFoundHttpException('Role not found');
        }
        $model       = new RbacRoleManagementForm();
        $model->name = $roleName;
        $roleList    = RbacQueryService::getRolesList();
        $roleList    = array_combine($roleList, $roleList);
        if (\Yii::$app->request->isPost) {
            try {
                $model->load(\Yii::$app->request->post());
                if (!$model->validate()) {
                    throw new \RuntimeException(implode(', ', $model->getErrorSummary(true)));
                }
                $result = RbacRoleManagementService::mergePermissions($model->donor_name, $model->name);
                if (!$result) {
                    throw new \RuntimeException('Saving Error');
                }
                \Yii::$app->session->setFlash('success', 'Form Saved');
            } catch (\RuntimeException | \DomainException $e) {
                \Yii::warning(AppHelper::throwableFormatter($e), 'RbacRoleManagementController::actionClone:exception');
                \Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e), 'RbacRoleManagementController:actionClone:Throwable');
                \Yii::$app->session->setFlash('error', 'Server Error');
            }
        }
        return $this->render('merge', [
            'model'    => $model,
            'roleList' => $roleList,
        ]);
    }

    public function actionExclude()
    {
        $roleName = (string)\Yii::$app->request->get('name');
        $item     = RbacQueryService::getRoleByName($roleName);
        if (empty($item)) {
            throw new NotFoundHttpException('Role not found');
        }
        $model       = new RbacRoleManagementForm();
        $model->name = $roleName;
        $roleList    = RbacQueryService::getRolesList();
        $roleList    = array_combine($roleList, $roleList);
        if (\Yii::$app->request->isPost) {
            try {
                $model->load(\Yii::$app->request->post());
                if (!$model->validate()) {
                    throw new \RuntimeException(implode(', ', $model->getErrorSummary(true)));
                }
                $result = RbacRoleManagementService::excludePermissions($model->donor_name, $model->name);
                if (!$result) {
                    throw new \RuntimeException('Saving Error');
                }
                \Yii::$app->session->setFlash('success', 'Form Saved');
            } catch (\RuntimeException | \DomainException $e) {
                \Yii::warning(AppHelper::throwableFormatter($e), 'RbacRoleManagementController::actionClone:exception');
                \Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e), 'RbacRoleManagementController:actionClone:Throwable');
                \Yii::$app->session->setFlash('error', 'Server Error');
            }
        }
        return $this->render('exclude', [
            'model'    => $model,
            'roleList' => $roleList,
        ]);
    }
}
