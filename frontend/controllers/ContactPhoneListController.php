<?php

namespace frontend\controllers;

use common\models\Call;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\model\call\abac\CallAbacObject;
use sales\model\contactPhoneData\form\ToggleDataForm;
use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneData\service\ContactPhoneDataHelper;
use sales\model\contactPhoneData\service\ContactPhoneDataService;
use sales\model\contactPhoneList\service\ContactPhoneListService;
use Yii;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\model\contactPhoneList\entity\ContactPhoneListSearch;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class ContactPhoneListController
 */
class ContactPhoneListController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'toggle-data',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ContactPhoneListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionToggleData(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['message' => '', 'status' => 0, 'result' => '', 'title' => ''];

            try {
                $toggleDataForm = new ToggleDataForm();

                if (!$toggleDataForm->load(Yii::$app->request->post())) {
                    throw new \RuntimeException('ToggleDataForm not loaded');
                }
                if (!$toggleDataForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($toggleDataForm));
                }
                if (!$this->checkAccess($toggleDataForm->key)) {
                    throw new \RuntimeException('Access denied');
                }
                if (!$contactPhoneList = ContactPhoneList::findOne($toggleDataForm->modelId)) {
                    throw new \RuntimeException('ContactPhoneList not found');
                }

                if (ContactPhoneListService::isExistByDataKeys($contactPhoneList->cpl_phone_number, [$toggleDataForm->key])) {
                    ContactPhoneDataService::removeByCplIdAndKey($contactPhoneList->cpl_id, $toggleDataForm->key);
                    $result['result'] = 'removed';
                    $result['message'] = 'Removed Phone number from ' . ContactPhoneDataHelper::getName($toggleDataForm->key);
                    $result['title'] = 'Add Phone number to ' . ContactPhoneDataHelper::getName($toggleDataForm->key);
                } else {
                    ContactPhoneDataService::getOrCreate(
                        $contactPhoneList->cpl_id,
                        $toggleDataForm->key,
                        ContactPhoneDataDictionary::DEFAULT_TRUE_VALUE
                    );
                    $result['result'] = 'added';
                    $result['message'] = 'Added Phone number to ' . ContactPhoneDataHelper::getName($toggleDataForm->key);
                    $result['title'] = 'Remove Phone number from ' . ContactPhoneDataHelper::getName($toggleDataForm->key);
                }
                $result['status'] = 1;
            } catch (\RuntimeException | \DomainException $exception) {
                Yii::warning(AppHelper::throwableLog($exception), 'ContactPhoneListController:actionToggleData::exception');
                $result['message'] = VarDumper::dumpAsString($exception->getMessage());
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'ContactPhoneListController:actionToggleData:throwable');
                $result['message'] = 'Internal Server Error';
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param integer $id
     * @return ContactPhoneList
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ContactPhoneList
    {
        if (($model = ContactPhoneList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ContactPhoneList not found');
    }

    private function checkAccess(string $key): ?bool
    {
        switch ($key) {
            case ContactPhoneDataDictionary::KEY_ALLOW_LIST:
                /** @abac CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key allow_list */
                return (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA));
                break;
            case ContactPhoneDataDictionary::KEY_IS_TRUSTED:
                /** @abac CallAbacObject::ACT_DATA_IS_TRUSTED, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key is_trusted */
                return (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_IS_TRUSTED, CallAbacObject::ACTION_TOGGLE_DATA));
                break;
            case ContactPhoneDataDictionary::KEY_AUTO_CREATE_CASE_OFF:
                /** @abac CallAbacObject::ACT_DATA_AUTO_CREATE_CASE_OFF, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key auto_create_case_off */
                return (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_AUTO_CREATE_CASE_OFF, CallAbacObject::ACTION_TOGGLE_DATA));
                break;
            case ContactPhoneDataDictionary::KEY_AUTO_CREATE_LEAD_OFF:
                /** @abac CallAbacObject::ACT_DATA_AUTO_CREATE_LEAD_OFF, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key auto_create_lead_off */
                return (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_AUTO_CREATE_LEAD_OFF, CallAbacObject::ACTION_TOGGLE_DATA));
                break;
            case ContactPhoneDataDictionary::KEY_INVALID:
                /** @abac CallAbacObject::ACT_DATA_INVALID, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key invalid */
                return (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_INVALID, CallAbacObject::ACTION_TOGGLE_DATA));
            default:
                return false;
        }
    }
}
