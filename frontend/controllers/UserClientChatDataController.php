<?php

namespace frontend\controllers;

use common\models\Employee;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\model\userClientChatData\entity\UserClientChatDataRepository;
use src\model\userClientChatData\service\UserClientChatDataService;
use src\repositories\clientChatUserChannel\ClientChatUserChannelRepository;
use src\services\clientChat\ClientChatRequesterService;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;
use Yii;
use src\model\userClientChatData\entity\UserClientChatData;
use src\model\userClientChatData\entity\UserClientChatDataSearch;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class UserClientChatDataController
 *
 * @property ClientChatUserAccessService $clientChatUserAccessService
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatUserChannelRepository $clientChatUserChannelRepository
 */
class UserClientChatDataController extends FController
{
    private ClientChatUserAccessService $clientChatUserAccessService;
    private ClientChatMessageService $clientChatMessageService;
    private ClientChatUserChannelRepository $clientChatUserChannelRepository;

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @param $id
     * @param $module
     * @param ClientChatUserAccessService $clientChatUserAccessService
     * @param ClientChatMessageService $clientChatMessageService
     * @param ClientChatUserChannelRepository $clientChatUserChannelRepository
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ClientChatUserAccessService $clientChatUserAccessService,
        ClientChatMessageService $clientChatMessageService,
        ClientChatUserChannelRepository $clientChatUserChannelRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->clientChatUserAccessService = $clientChatUserAccessService;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->clientChatUserChannelRepository = $clientChatUserChannelRepository;
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
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new UserClientChatDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserClientChatData();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $user = ClientChatRequesterService::checkRegisterEmployee($model->getEmployeeId());

                $result = ClientChatRequesterService::register(
                    $model->uccd_username,
                    $model->uccd_name,
                    $user->email,
                    $model->uccd_password
                );

                $model->uccd_rc_user_id = $result['rcUserId'];
                $model->uccd_auth_token = $result['authToken'];
                $model->uccd_token_expired = \Yii::$app->rchat::generateTokenExpired();

                (new UserClientChatDataRepository())->save($model);

                $userChannels = $this->clientChatUserChannelRepository->getChannelsByUserId($user->id);
                $userChannels = ArrayHelper::getColumn(ArrayHelper::toArray($userChannels), 'ccuc_channel_id');
                if ($userChannels) {
                    $this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($userChannels, $user->id);
                }

                return $this->redirect(['view', 'id' => $model->uccd_id]);
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'UserClientChatDataController:actionCreate'
                );
                $error = $throwable->getMessage();
            }
        }

        $model->uccd_password = Yii::$app->rchat::generatePassword();

        return $this->render('create', [
            'model' => $model,
            'error' => $error ?? '',
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $password = $model->uccd_password;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                if (!$rcUserId = $model->getRcUserId()) {
                    throw new \RuntimeException('Error. RcUserId is empty');
                }

                $updateRC = [];
                $updateFields = [
                    'uccd_name' => 'name',
                    'uccd_username' => 'username',
                ];
                if (!empty($model->uccd_password)) {
                    $updateFields['uccd_password'] = 'password';
                }

                if (empty($model->uccd_password)) {
                    $model->uccd_password = $password;
                }

                foreach ($updateFields as $column => $rcField) {
                    if ($model->isAttributeChanged($column) && $model->validate([$column])) {
                        $updateRC[$rcField] = $model->{$column};
                    }
                }

                if (!empty($updateRC)) {
                    $rocketChat = \Yii::$app->rchat;
                    $rocketChat->updateSystemAuth(false);
                    $result = $rocketChat->updateUser($rcUserId, $updateRC);

                    if (isset($result['error']) && !$result['error']) {
                        $model->save(false);
                    } else {
                        $errorMessage = $rocketChat::getErrorMessageFromResult($result);
                        throw new \RuntimeException('Error from RocketChat. ' . $errorMessage);
                    }

                    $authToken = ClientChatRequesterService::refreshToken($model->uccd_username, $model->uccd_password);
                    $model->uccd_auth_token = $authToken;
                    $model->uccd_token_expired = \Yii::$app->rchat::generateTokenExpired();

                    if (!$model->save(false)) {
                        throw new \RuntimeException($model->getErrorSummary(false)[0]);
                    }
                }

                return $this->redirect(['view', 'id' => $model->uccd_id]);
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'UserClientChatDataController:actionCreate'
                );
                $error = $throwable->getMessage();
            }
        } else {
            $model->uccd_password = '';
        }

        return $this->render('update', [
            'model' => $model,
            'error' => $error ?? '',
        ]);
    }

    /**
     * @param int $id
     * @return UserClientChatData
     * @throws NotFoundHttpException
     */
    protected function findModel($id): UserClientChatData
    {
        if (($model = UserClientChatData::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param int $id
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \JsonException
     * @throws \Throwable
     * @throws \yii\httpclient\Exception
     */
    public function actionDelete(int $id): Response
    {
        $userChatData = $this->findModel($id);

        if (!$rcUserId = $userChatData->getRcUserId()) {
            throw new NotFoundHttpException('Error rc_user_id is empty');
        }

        $rocketChat = \Yii::$app->rchat;
        $rocketChat->updateSystemAuth(false);

        $result = $rocketChat->deleteUser($rcUserId, $userChatData->uccd_username);

        if (isset($result['error']) && !$result['error']) {
            $this->clientChatUserAccessService->disableUserAccessToAllChats($userChatData->getEmployeeId());
            $employeeId = $userChatData->getEmployeeId();
            $rcUserId = $userChatData->getRcUserId();
            $userChatData->delete();
            \Yii::info(
                VarDumper::dumpAsString([
                    'employeeId' => $employeeId,
                    'rcUserId' => $rcUserId,
                    'deleted by' => Auth::id(),
                ], 10),
                'info\UserClientChatDataController:deleted'
            );
            return $this->redirect(['index']);
        }
        $errorMessage = $rocketChat::getErrorMessageFromResult($result);
        throw new BadRequestHttpException('Error from RocketChat. ' . $errorMessage);
    }

    /**
     * @return array
     * @throws BadRequestHttpException|NotFoundHttpException
     */
    public function actionActivate(): array
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }
        $userId = (int) Yii::$app->request->post('id');
        if (!$user = (Employee::findOne($userId))) {
            throw new NotFoundHttpException('User not found. UserId:' . $userId);
        }
        if (!$userClientChatData = $user->userClientChatData) {
            throw new NotFoundHttpException('UserClientChatData not found from UserId:' . $userId);
        }

        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['status' => 0, 'message' => ''];

            try {
                if (!$rcUserId = $userClientChatData->getRcUserId()) {
                    throw new \RuntimeException('Error. RcUserId is empty');
                }
                if (!$rocketUsername = $userClientChatData->uccd_username) {
                    throw new \RuntimeException('Not found Username for this user(' . $userId . ')');
                }
                if (!$rocketPassword = $userClientChatData->uccd_password) {
                    throw new \RuntimeException('Not found Rocket Chat Auth Password for this user(' . $userId . ')');
                }

                $rocketChat = \Yii::$app->rchat;
                $result = $rocketChat->updateUser($rcUserId, ['active' => true]);

                if (isset($result['error']) && !$result['error']) {
                    $userClientChatData->uccd_active = true;
                } else {
                    $errorMessage = $rocketChat::getErrorMessageFromResult($result);
                    throw new \RuntimeException('Error from RocketChat. ' . $errorMessage);
                }

                $authToken = ClientChatRequesterService::refreshToken($rocketUsername, $rocketPassword);
                $userClientChatData->uccd_auth_token = $authToken;
                $userClientChatData->uccd_token_expired = \Yii::$app->rchat::generateTokenExpired();

                if (!$userClientChatData->save(false)) {
                    throw new \RuntimeException($userClientChatData->getErrorSummary(false)[0]);
                }

                $out['status'] = 1;
                $out['message'] = 'Request Activate To Rocket Chat was successful';
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableFormatter($throwable),
                    'UserClientChatDataController:actionActivateToRocketChat:Throwable'
                );
                $out['message'] = $throwable->getMessage();
            }
            return $out;
        }
        throw new BadRequestHttpException('Request must by isAjax');
    }

    /**
     * @return array
     * @throws BadRequestHttpException|NotFoundHttpException
     */
    public function actionDeactivate(): array
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }
        $userId = (int) Yii::$app->request->post('id');
        if (!$user = (Employee::findOne($userId))) {
            throw new NotFoundHttpException('User not found. UserId:' . $userId);
        }
        if (!$userClientChatData = $user->userClientChatData) {
            throw new NotFoundHttpException('UserClientChatData not found from UserId:' . $userId);
        }

        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['status' => 0, 'message' => ''];

            try {
                if (!$rcUserId = $userClientChatData->getRcUserId()) {
                    throw new \RuntimeException('Error. RcUserId is empty');
                }

                $rocketChat = \Yii::$app->rchat;
                $result = $rocketChat->updateUser($rcUserId, ['active' => false]);

                if (isset($result['error']) && !$result['error']) {
                    $userClientChatData->uccd_active = false;

                    if (!$userClientChatData->save(false)) {
                        throw new \RuntimeException($userClientChatData->getErrorSummary(false)[0]);
                    }
                } else {
                    $errorMessage = $rocketChat::getErrorMessageFromResult($result);
                    throw new \RuntimeException('Error from RocketChat. ' . $errorMessage);
                }
                $out['status'] = 1;
                $out['message'] = 'Request Deactivate From Rocket Chat was successful';
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableFormatter($throwable),
                    'UserClientChatDataController:actionDeactivateFromRocketChat:Throwable'
                );
                $out['message'] = $throwable->getMessage();
            }
            return $out;
        }
        throw new BadRequestHttpException('Request must by isAjax');
    }

    public function actionValidateRocketChatCredential()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        try {
            $userId = (int)Yii::$app->request->post('id');
            if (!$userId) {
                throw new \RuntimeException('Not found user Id');
            }
            $user = Employee::findOne($userId);
            if (!$user) {
                throw new \RuntimeException('Not found User with Id ' . $userId);
            }
            if (!$rocketUserId = $user->userClientChatData->getRcUserId()) {
                throw new \RuntimeException('Not found Rocket Chat User Id for this user(' . $userId . ')');
            }
            if (!$rocketToken = $user->userClientChatData->getAuthToken()) {
                throw new \RuntimeException('Not found Rocket Chat Auth Token for this user(' . $userId . ')');
            }

            $result = \Yii::$app->rchat->me($rocketUserId, $rocketToken);

            if ($result['error'] !== false) {
                if ($result['error'] === 'You must be logged in to do this.') {
                    throw new \Exception('Invalid credential');
                }
                throw new \Exception((string)$result['error']);
            }
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
        return $this->asJson([
            'error' => false,
            'message' => 'OK',
        ]);
    }

    public function actionRefreshRocketChatUserToken()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        try {
            $userId = (int)Yii::$app->request->post('id');
            if (!$userId) {
                throw new \RuntimeException('Not found user Id');
            }
            $user = Employee::findOne($userId);
            if (!$user) {
                throw new \RuntimeException('Not found User with Id ' . $userId);
            }
            if (!$userClientChatData = $user->userClientChatData) {
                throw new \RuntimeException('Not found UserClientChatData for this user(' . $userId . ')');
            }

            $userClientChatDataService = Yii::createObject(UserClientChatDataService::class);
            $userClientChatDataService->refreshRocketChatUserToken($userClientChatData);
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
        return $this->asJson([
            'error' => false,
            'message' => 'OK',
        ]);
    }

    public function actionUserInfo(): array
    {
        if (!Yii::$app->request->isPost || !Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }
        $userId = (int) Yii::$app->request->post('id');
        if (!$user = (Employee::findOne($userId))) {
            throw new NotFoundHttpException('User not found. UserId:' . $userId);
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'username' => UserClientChatData::generateUsername($user->id),
            'name' => $user->nickname ?? $user->username,
        ];
    }
}
