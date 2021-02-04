<?php

namespace common\components\schema;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserOnline;
use common\models\UserParams;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use sales\model\user\entity\userStatus\UserStatus;

/**
 * Class QueryType
 * @package common\components\schema
 */
class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'user' => [
                        'type' => Types::user(),
                        'args' => [
                            'id' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => static function ($root, $args) {
                            return Employee::find()->where(['id' => $args['id']])->one();
                        }
                    ],

                    'myUser' => [
                        'type' => Types::user(),
                        'resolve' => static function ($root, $args) {
                            $userId = \Yii::$app->user->id;
                            return Employee::find()->where(['id' => $userId])->one();
                        }
                    ],

                    'myUserParams' => [
                        'type' => Types::userParams(),
//                        'args' => [
//                            'id' => Type::nonNull(Type::int()),
//                        ],
                        'resolve' => static function ($root, $args) {
                            $userId = \Yii::$app->user->id;
                            return UserParams::find()->where(['up_user_id' => $userId])->one();
                        }
                    ],

                    'userList' => [
                        'type' => Types::listOf(Types::user()),
//                        'args' => [
//                            'id' => Type::nonNull(Type::int()),
//                        ],
                        'resolve' => static function ($root, $args) {
                            $query = Employee::find();
                            return $query->all();
                        }
                    ],

                    'call' => [
                        'type' => Types::call(),
                        'args' => [
                            'id' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => static function ($root, $args) {
                            return Call::find()->where(['c_id' => $args['id']])->one();
                        }
                    ],

                    'callStatusList' => [
                        'type' => Types::listOf(Types::array()),
                        'resolve' => static function ($root, $args) {
                            return Call::getStatusListApi();
                        }
                    ],

                    'callSourceList' => [
                        'type' => Types::listOf(Types::array()),
                        'resolve' => static function ($root, $args) {
                            return Call::getSourceListApi();
                        }
                    ],

                    'callTypeList' => [
                        'type' => Types::listOf(Types::array()),
                        'resolve' => static function ($root, $args) {
                            return Call::getTypeListApi();
                        }
                    ],

                    'callUserAccessStatusTypeList' => [
                        'type' => Types::listOf(Types::array()),
                        'resolve' => static function ($root, $args) {
                            return CallUserAccess::getStatusTypeListApi();
                        }
                    ],

                    'onlineUserList'  => [
                        'type' => Types::listOf(Types::userOnline()),
                        'resolve' => static function ($root, $args) {
                            $query = UserOnline::find();
                            return $query->all();
                        }
                    ],

                    'userStatusList'  => [
                        'type' => Types::listOf(Types::userStatus()),
                        'resolve' => static function ($root, $args) {
                            $query = UserStatus::find();
                            return $query->all();
                        }
                    ],

                    'departmentList'  => [
                        'type' => Types::listOf(Types::department()),
                        'resolve' => static function ($root, $args) {
                            $query = Department::find();
                            return $query->all();
                        }
                    ],

                    'department'  => [
                        'type' => Types::department(),
                        'args' => [
                            'id' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => static function ($root, $args) {
                            return Department::find()->where(['dep_id' => $args['id']])->one();
                        }
                    ],

                    'projectList'  => [
                        'type' => Types::listOf(Types::project()),
                        'args' => [
                            'closed' => Type::int(),
                        ],
                        'resolve' => static function ($root, $args) {
                            $query = Project::find();
                            if (isset($args['closed'])) {
                                $query->andWhere(['closed' => $args['closed']]);
                            }
                            return $query->all();
                        }
                    ],

                    'project'  => [
                        'type' => Types::project(),
                        'args' => [
                            'id' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => static function ($root, $args) {
                            return Project::find()->where(['id' => $args['id']])->one();
                        }
                    ],


                ];
            }
        ];

        parent::__construct($config);
    }
}
