<?php

namespace modules\qaTask\controllers;

use common\models\Lead;
use frontend\controllers\FController;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTask\QaTask;
use sales\entities\cases\Cases;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class QaTaskController extends FController
{
    /**
     * @param $gid
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($gid): string
    {
        return $this->render('view', [
            'model' => $this->findModel($gid),
        ]);
    }

    public function actionViewObject($typeId, $id): Response
    {
        $typeId = (int)$typeId;
        $id = (int)$id;

        if ($typeId === QaObjectType::LEAD) {
            if ($object = Lead::findOne($id)) {
                return $this->redirect(['/lead/view', 'gid' => $object->gid]);
            }
            throw new BadRequestHttpException('Not found Lead: ' . $id);
        }

        if ($typeId === QaObjectType::CASE) {
            if ($object = Cases::findOne($id)) {
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
