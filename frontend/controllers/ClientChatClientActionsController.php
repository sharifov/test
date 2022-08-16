<?php

namespace frontend\controllers;

use frontend\widgets\clientChat\ClientChatClientInfoWidget;
use frontend\widgets\clientChat\ClientChatFormBookingIdWidget;
use src\forms\clientChat\BookingIdCreateForm;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\model\clientChat\entity\abac\ClientChatAbacObject;
use src\model\clientChatForm\entity\ClientChatForm;
use src\model\clientChatRequest\useCase\api\create\ClientChatFormResponseService;
use src\services\client\ClientManageService;
use Yii;
use src\auth\Auth;
use src\model\clientChat\entity\ClientChat;
use src\services\client\ClientCreateForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class ClientChatClientActionsController
 *
 * @property ClientManageService $clientManageService
 */
class ClientChatClientActionsController extends FController
{
    private ClientManageService $clientManageService;

    private ClientChatFormResponseService $clientChatFormResponseService;

    public function __construct($id, $module, ClientManageService $clientManageService, ClientChatFormResponseService $clientChatFormResponseService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->clientManageService = $clientManageService;
        $this->clientChatFormResponseService = $clientChatFormResponseService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'ajax-edit-client-name-validation' => ['POST'],
                    'ajax-edit-client-name' => ['POST'],
                    'ajax-add-client-email' => ['POST'],
                    'ajax-add-client-email-validation' => ['POST'],
                    'ajax-add-client-phone' => ['POST'],
                    'ajax-add-client-phone-validation' => ['POST'],
                    'ajax-add-booking-id-validation' => ['POST'],
                    'ajax-add-booking-id' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'ajax-edit-client-name-modal-content',
                    'ajax-edit-client-name-validation',
                    'ajax-edit-client-name',
                    'ajax-add-client-email-modal-content',
                    'ajax-add-client-email-validation',
                    'ajax-add-client-email',
                    'ajax-add-client-phone-modal-content',
                    'ajax-add-client-phone-validation',
                    'ajax-add-client-phone',
                    'ajax-add-booking-id-modal-content',
                    'ajax-add-booking-id',
                    'ajax-add-booking-id-validation',
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    private function getChatFromRequest(): ClientChat
    {
        if (!$id = (int) Yii::$app->request->get('id')) {
            throw new BadRequestHttpException('Invalid parameter');
        }
        if (!$chat = ClientChat::findOne($id)) {
            throw new NotFoundHttpException('Chat is not found');
        }
        if (!Auth::can('client-chat/manage', ['chat' => $chat])) {
            throw new ForbiddenHttpException('You don\'t have access to this action.');
        }
        return $chat;
    }

    public function actionAjaxEditClientNameModalContent(): string
    {
        $chat = $this->getChatFromRequest();
        $client = $chat->cchClient;
        $form = new ClientCreateForm();
        $form->id = $client->id;
        $form->firstName = $client->first_name;
        $form->lastName = $client->last_name;
        $form->middleName = $client->middle_name;
        $form->locale = $client->cl_locale;
        $form->marketingCountry = $client->cl_marketing_country;

        return $this->renderAjax('_client_edit_name_modal_content', [
            'editName' => $form,
            'chatId' => $chat->cch_id,
        ]);
    }

    public function actionAjaxEditClientNameValidation(): array
    {
        $chat = $this->getChatFromRequest();
        $client = $chat->cchClient;
        $form = new ClientCreateForm();
        $form->id = $client->id;
        $form->firstName = $client->first_name;
        $form->lastName = $client->last_name;
        $form->middleName = $client->middle_name;

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        throw new BadRequestHttpException();
    }

    public function actionAjaxEditClientName()
    {
        $chat = $this->getChatFromRequest();
        $client = $chat->cchClient;
        $form = new ClientCreateForm();
        $form->id = $client->id;
        $form->firstName = $client->first_name;
        $form->lastName = $client->last_name;
        $form->middleName = $client->middle_name;

        $response[] = [
            'error' => false,
            'message' => '',
            'html' => '',
            'client' => [],
        ];

        try {
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $this->clientManageService->updateClient($chat->cchClient, $form);
                $chat->refresh();
                $response['error'] = false;
                $response['message'] = 'Client information has been updated successfully';
                $response['html'] = ClientChatClientInfoWidget::widget(['chat' => $chat]);
                $clientFullName = trim($chat->cchClient->first_name . ($chat->cchClient->last_name ? ' ' . $chat->cchClient->last_name : ''));
                $response['client'] = [
                    'id' => $chat->cch_client_id,
                    'name' => $clientFullName ?: 'Client-' . $chat->cch_client_id,
                ];
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }
        } catch (\Throwable $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }

        return $this->asJson($response);
    }

    public function actionAjaxAddClientEmailModalContent(): string
    {
        $chat = $this->getChatFromRequest();

        $form = new EmailCreateForm();

        return $this->renderAjax('_client_add_email_modal_content', [
            'addEmail' => $form,
            'chatId' => $chat->cch_id,
        ]);
    }

    public function actionAjaxAddClientEmail()
    {
        $chat = $this->getChatFromRequest();

        $form = new EmailCreateForm();
        $form->required = true;

        $response[] = [
            'error' => false,
            'message' => '',
            'html' => '',
        ];

        try {
            $isLoaded = $form->load(Yii::$app->request->post());
            $form->client_id = $chat->cch_client_id;
            if ($isLoaded && $form->validate()) {
                $this->clientManageService->addEmails($chat->cchClient, [$form]);
                $chat->refresh();

                $response['error'] = false;
                $response['message'] = 'New email was successfully added: ' . $form->email;
                $response['html'] = ClientChatClientInfoWidget::widget(['chat' => $chat]);
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }
        } catch (\Throwable $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }

        return $this->asJson($response);
    }

    public function actionAjaxAddClientEmailValidation(): array
    {
        $chat = $this->getChatFromRequest();

        $form = new EmailCreateForm();
        $form->required = true;

        $isLoaded = $form->load(Yii::$app->request->post());
        $form->client_id = $chat->cch_client_id;

        if (Yii::$app->request->isAjax && $isLoaded) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        throw new BadRequestHttpException();
    }

    public function actionAjaxAddClientPhoneModalContent(): string
    {
        $chat = $this->getChatFromRequest();

        $form = new PhoneCreateForm();

        return $this->renderAjax('_client_add_phone_modal_content', [
            'addPhone' => $form,
            'chatId' => $chat->cch_id,
        ]);
    }

    public function actionAjaxAddClientPhone()
    {
        $chat = $this->getChatFromRequest();

        $form = new PhoneCreateForm();
        $form->required = true;

        $response[] = [
            'error' => false,
            'message' => '',
            'html' => '',
        ];

        try {
            $isLoaded = $form->load(Yii::$app->request->post());
            $form->client_id = $chat->cch_client_id;

            if ($isLoaded && $form->validate()) {
                $this->clientManageService->addPhone($chat->cchClient, $form);
                $chat->refresh();

                $response['error'] = false;
                $response['message'] = 'New phone was successfully added: ' . $form->phone;
                $response['html'] = ClientChatClientInfoWidget::widget(['chat' => $chat]);
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }
        } catch (\Throwable $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }

        return $this->asJson($response);
    }

    public function actionAjaxAddClientPhoneValidation(): array
    {
        $chat = $this->getChatFromRequest();

        $form = new PhoneCreateForm();
        $form->required = true;

        $isLoaded = $form->load(Yii::$app->request->post());
        $form->client_id = $chat->cch_client_id;

        if (Yii::$app->request->isAjax && $isLoaded) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        throw new BadRequestHttpException();
    }

    public function actionAjaxAddBookingIdModalContent(): string
    {
        /** @abac ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE, Access To create Client Chat Form Response */
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $chat = $this->getChatFromRequest();

        $form = new BookingIdCreateForm();

        return $this->renderAjax('_client_add_booking_id_modal_content', [
            'addBookingId' => $form,
            'chatId' => $chat->cch_id,
        ]);
    }

    public function actionAjaxAddBookingIdValidation(): array
    {
        /** @abac ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE, Access To create Client Chat Form Response */
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $chat = $this->getChatFromRequest();

        $form = new BookingIdCreateForm();

        $isLoaded = $form->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax && $isLoaded) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        throw new BadRequestHttpException();
    }

    public function actionAjaxAddBookingId()
    {
        /** @abac ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE, Access To create Client Chat Form Response */
        if (!Yii::$app->abac->can(null, ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $chat = $this->getChatFromRequest();

        $form = new BookingIdCreateForm();

        $response[] = [
            'error' => false,
            'message' => '',
            'html' => '',
        ];

        try {
            $isLoaded = $form->load(Yii::$app->request->post());

            if ($isLoaded && $form->validate()) {
                $createdAt = date('Y-m-d H:i:s');
                $this->clientChatFormResponseService->createFormResponse($chat->cch_rid, ClientChatForm::KEY_BOOKING_ID, $form->bookingId, $createdAt);

                $response['error'] = false;
                $response['message'] = 'New booking id was successfully added: ' . $form->bookingId;
                $response['html'] = ClientChatFormBookingIdWidget::widget(['bookingId' => $form->bookingId]);
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }
        } catch (\Throwable $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }

        return $this->asJson($response);
    }
}
