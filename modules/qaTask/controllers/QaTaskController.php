<?php

namespace modules\qaTask\controllers;

use modules\qaTask\src\entities\qaTask\search\object\QaTaskObjectSearch;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\guard\QaTaskGuard;
use sales\access\QueryAccessService;
use Yii;
use common\models\Lead;
use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\search\CreateDto;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class QaTaskController
 *
 * @property QueryAccessService $queryAccessService
 */
class QaTaskController extends FController
{
    private $queryAccessService;

    public function __construct($id, $module, QueryAccessService $queryAccessService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->queryAccessService = $queryAccessService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'view-tasks',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @param $gid
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($gid): string
    {
        $model = $this->findModel($gid);

        QaTaskGuard::guard($model->t_project_id, Auth::id());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $typeId
     * @param $id
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionViewTasks($typeId, $id): string
    {
        $id = (int)$id;
        $typeId = (int)$typeId;

        if (QaTaskObjectType::isLead($typeId)) {
            if (!$lead = Lead::findOne($id)) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            if (!Auth::can('lead/view_QA_Tasks')) {
                throw new ForbiddenHttpException('Access denied.');
            }
            QaTaskGuard::guard($lead->project_id, Auth::id());
        } else {
            throw new BadRequestHttpException('Undefined Object Type.');
        }

        $searchModel = QaTaskObjectSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => [],
            'userList' => (new ListsAccess(Auth::id()))->getEmployees(),
            'categoryList' =>  QaTaskCategoryQuery::getEnabledListByType($typeId),
            'queryAccessService' => $this->queryAccessService,
        ]));

        $dataProvider = $searchModel->search($typeId, $id, Yii::$app->request->queryParams);

        return $this->renderAjax('view-tasks', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $typeId
     * @param $id
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionViewObject($typeId, $id): Response
    {
        $typeId = (int)$typeId;
        $id = (int)$id;

        if (QaTaskObjectType::isLead($typeId)) {
            if ($object = Lead::findOne($id)) {
                QaTaskGuard::guard($object->project_id, Auth::id());
                return $this->redirect(['/lead/view', 'gid' => $object->gid]);
            }
            throw new BadRequestHttpException('Not found Lead: ' . $id);
        }

        if (QaTaskObjectType::isCase($typeId)) {
            if ($object = Cases::findOne($id)) {
                QaTaskGuard::guard($object->cs_project_id, Auth::id());
                return $this->redirect(['/cases/view', 'gid' => $object->cs_gid]);
            }
            throw new BadRequestHttpException('Not found Case: ' . $id);
        }

        throw new BadRequestHttpException('Undefined Object type');
    }

    /**
     * @param $gid
     * @return QaTask
     * @throws NotFoundHttpException
     */
    protected function findModel($gid): QaTask
    {
        if (($model = QaTask::find()->andWhere(['t_gid' => $gid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
