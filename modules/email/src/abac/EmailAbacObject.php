<?php

namespace modules\email\src\abac;

use common\models\Department;
use common\models\EmailTemplateType;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class EmailAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'email/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_ALL     = self::NS . 'act/*';
    public const ACT_VIEW  = self::NS . 'act/view';

    /** UI PERMISSION */
    public const OBJ_PREVIEW_EMAIL = self::NS . 'obj/preview-email';
    public const OBJ_REVIEW_EMAIL = self::NS . 'obj/review-email';
    public const OBJ_EMAIL_TEMPLATE_TYPE = self::NS . 'obj/email-template-type';

    public const OBJECT_LIST = [
        self::ACT_VIEW => self::ACT_VIEW,
        self::OBJ_PREVIEW_EMAIL => self::OBJ_PREVIEW_EMAIL,
        self::OBJ_REVIEW_EMAIL => self::OBJ_REVIEW_EMAIL,
        self::OBJ_EMAIL_TEMPLATE_TYPE => self::OBJ_EMAIL_TEMPLATE_TYPE,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_SEND = 'send';
    public const ACTION_SEND_WITHOUT_REVIEW = 'sendWithoutReview';
    public const ACTION_EDIT_MESSAGE = 'editMessage';
    public const ACTION_EDIT_SUBJECT = 'editSubject';
    public const ACTION_EDIT_FROM = 'editFrom';
    public const ACTION_EDIT_TO = 'editTo';
    public const ACTION_EDIT_EMAIL_FROM_NAME = 'editEmailFromName';
    public const ACTION_EDIT_EMAIL_TO_NAME = 'editEmailToName';
    public const ACTION_ATTACH_FILES = 'attachFiles';
    public const ACTION_SHOW_EMAIL_DATA = 'showEmailData';

    public const ACTION_MANAGE_REVIEW_FORM = 'manageReviewForm';
    public const ACTION_VIEW_REVIEW_DATA = 'viewReviewData';
    public const ACTION_VIEW_REVIEW_EMAIL_DATA = 'viewReviewEmailData';
    public const ACTION_VIEW_REVIEW_EMAIL_ATTACHED_FILES = 'viewReviewEmailAttachedFiles';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_VIEW  => [self::ACTION_ACCESS],
        self::OBJ_PREVIEW_EMAIL => [
            self::ACTION_SEND,
            self::ACTION_SEND_WITHOUT_REVIEW,
            self::ACTION_EDIT_MESSAGE,
            self::ACTION_EDIT_SUBJECT,
            self::ACTION_EDIT_FROM,
            self::ACTION_EDIT_TO,
            self::ACTION_EDIT_EMAIL_FROM_NAME,
            self::ACTION_EDIT_EMAIL_TO_NAME,
            self::ACTION_ATTACH_FILES,
            self::ACTION_SHOW_EMAIL_DATA,
        ],
        self::OBJ_REVIEW_EMAIL => [
            self::ACTION_MANAGE_REVIEW_FORM,
            self::ACTION_VIEW_REVIEW_DATA,
            self::ACTION_VIEW_REVIEW_EMAIL_DATA,
            self::ACTION_VIEW_REVIEW_EMAIL_ATTACHED_FILES
        ],
        self::OBJ_EMAIL_TEMPLATE_TYPE  => [self::ACTION_ACCESS],
    ];

    protected const ATTR_IS_EMAIL_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_email_owner',
        'field' => 'is_email_owner',
        'label' => 'Is Email Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_HAS_CREATOR = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'has_creator',
        'field' => 'has_creator',
        'label' => 'Has Creator',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_CASE_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_case_owner',
        'field' => 'is_case_owner',
        'label' => 'Is Case Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_LEAD_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_lead_owner',
        'field' => 'is_lead_owner',
        'label' => 'Is Lead Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_ADDRESS_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_address_owner',
        'field' => 'is_address_owner',
        'label' => 'Is Address Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_COMMON_GROUP = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_common_group',
        'field' => 'is_common_group',
        'label' => 'Is Common Group',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_TEMPLATE_KEY = [
        'optgroup' => 'EMAIL PREVIEW',
        'id' => self::NS . 'template_id',
        'field' => 'template_id',
        'label' => 'Template',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_TEMPLATE_TYPE_KEY = [
        'optgroup' => 'EMAIL TEMPLATE',
        'id' => self::NS . 'template_key',
        'field' => 'template_key',
        'label' => 'Template',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_IS_MESSAGE_EDITED = [
        'optgroup' => 'EMAIL PREVIEW',
        'id' => self::NS . 'is_message_edited',
        'field' => 'is_message_edited',
        'label' => 'Is Message Edited',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_SUBJECT_EDITED = [
        'optgroup' => 'EMAIL PREVIEW',
        'id' => self::NS . 'is_subject_edited',
        'field' => 'is_subject_edited',
        'label' => 'Is Subject Edited',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_ATTACH_COUNT = [
        'optgroup' => 'EMAIL PREVIEW',
        'id' => self::NS . 'attachCount',
        'field' => 'attachCount',
        'label' => 'Attached files count',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'validation' => ['min' => 0, 'max' => 999, 'step' => 1],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<'],
    ];

    protected const ATTR_PROJECT_ID = [
        'optgroup' => 'EMAIL PREVIEW',
        'id' => self::NS . 'project_id',
        'field' => 'project_id',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_DEPARTMENT_ID = [
        'optgroup' => 'EMAIL PREVIEW',
        'id' => self::NS . 'department_id',
        'field' => 'department_id',
        'label' => 'Department',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_VIEW => [
            self::ATTR_IS_EMAIL_OWNER,
            self::ATTR_HAS_CREATOR,
            self::ATTR_IS_CASE_OWNER,
            self::ATTR_IS_LEAD_OWNER,
            self::ATTR_IS_ADDRESS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::OBJ_PREVIEW_EMAIL => [
            self::ATTR_ATTACH_COUNT,
            self::ATTR_IS_MESSAGE_EDITED,
            self::ATTR_IS_SUBJECT_EDITED
        ],
        self::OBJ_REVIEW_EMAIL => [
            self::ATTR_IS_EMAIL_OWNER,
            self::ATTR_HAS_CREATOR,
            self::ATTR_IS_CASE_OWNER,
            self::ATTR_IS_LEAD_OWNER,
            self::ATTR_IS_ADDRESS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ]
    ];


    /**
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    /**
     * @return string[]
     */
    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    /**
     * @return \array[][]
     */
    public static function getObjectAttributeList(): array
    {
        $templateKey = self::ATTR_TEMPLATE_KEY;
        $templateKey['values'] = EmailTemplateType::getList(false, null);

        $templateTypeKey = self::ATTR_TEMPLATE_TYPE_KEY;
        $templateTypeKey['values'] = EmailTemplateType::getKeyList(false, null);

        $project = self::ATTR_PROJECT_ID;
        $project['values'] = Project::getList();

        $department = self::ATTR_DEPARTMENT_ID;
        $department['values'] = Department::getList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::OBJ_PREVIEW_EMAIL][] = $templateKey;
        $attributeList[self::OBJ_EMAIL_TEMPLATE_TYPE][] = $templateTypeKey;
        $attributeList[self::OBJ_PREVIEW_EMAIL][] = $project;
        $attributeList[self::OBJ_PREVIEW_EMAIL][] = $department;
        return $attributeList;
    }
}
