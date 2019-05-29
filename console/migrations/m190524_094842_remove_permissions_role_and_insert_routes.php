<?php

use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m190524_094842_remove_permissions_manageagents_manage_leads_trainer_agents
 */
class m190524_094842_remove_permissions_role_and_insert_routes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($trainerAgentsPermission = $auth->getPermission('trainerAgents')) {
            $auth->remove($trainerAgentsPermission);
        }

        if ($manageLeadsPermission = $auth->getPermission('manageLeads')) {
            $auth->remove($manageLeadsPermission);
        }

        if ($manageAgentsPermission = $auth->getPermission('manageAgents')) {
            $auth->remove($manageAgentsPermission);
        }

        if ($coachRole = $auth->getRole('coach')) {
            $auth->revoke($coachRole, 241);
            $auth->remove($coachRole);
        }

        foreach ($auth->getRoles() as $role) {
            $auth->removeChildren($role);
        }

        //*************************************************

        $roles = [];

        $this->createAssArrayWithRolesAndPermissions($roles);

        $this->assignParentChild($roles, 'supervision', 'agent');
        $this->assignParentChild($roles, 'admin', 'agent');
        $this->assignParentChild($roles, 'admin', 'supervision');

        $dataItem = $this->createDataItem($roles);
        $dataItemChild = $this->createDataItemChild($roles);

        $dataCreated = time();
        foreach ($dataItem as $key => $item) {
            $dataItem[$key][2] = $dataCreated;
            $dataItem[$key][3] = $dataCreated;
        }

        Yii::$app->db->createCommand()->batchInsert('{{%auth_item}}', ['name', 'type', 'created_at', 'updated_at'], $dataItem)->execute();
        Yii::$app->db->createCommand()->batchInsert('{{%auth_item_child}}', ['parent', 'child'], $dataItemChild)->execute();

        $this->createSuperAdmin();

        //  for user coach change role

        $roleAgent = $auth->getRole('agent');
        $auth->assign($roleAgent, 241);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    private function createDataItemChild($roles) : array
    {
        $data = [];
        foreach ($roles as $roleName => $controllers) {
            foreach ($controllers as $controllerName => $actions) {
                foreach ($actions as $actionName => $action) {
                    $data[] = [$roleName, $this->createRoute($controllerName, $actionName)];
                }
            }
        }
        return $data;
    }

    private function createDataItem($roles) : array
    {
        $data = [];
        foreach ($roles as $role => $controller) {
            foreach ($controller as $controllerName => $actions) {
                foreach ($actions as $actionName => $action) {
                    $data[$this->createRoute($controllerName, $actionName)] = 2;
                }
            }
        }
        $items = [];
        // remove duplicates
        foreach ($data as $key => $item) {
            $items[] = [$key, $data[$key]];
        }
        return $items;
    }

    private function createRoute($controller, $action) : string
    {
        return '/' . Inflector::camel2id(strstr($controller, 'Controller', true)) . '/' . $action;
    }

    private function assignParentChild(&$roles, $parent, $child) : void
    {
        foreach ($roles[$child] as $controller => $items) {
            foreach ($items as $key => $item) {
                $roles[$parent][$controller][$key] = 1;
            }
        }
    }

    private function createSuperAdmin() : void
    {
        $employee = new \common\models\Employee();
        $employee->username = 'superadmin';
        $employee->email = 'alex.connor2@techork.com';
        $employee->acl_rules_activated = false;
        $employee->setPassword('superadmin');
        $employee->generateAuthKey();
        $employee->save(false);

        $auth = \Yii::$app->authManager;

        $superAdmin = $auth->createRole('superadmin');
        $superAdmin->description = 'SuperAdmin';
        $auth->add($superAdmin);

        $fullPermission = $auth->createPermission('/*');
        $auth->add($fullPermission);
        $auth->addChild($superAdmin, $fullPermission);

        $roleAdmin = $auth->getRole('admin');
        $auth->addChild($superAdmin, $roleAdmin);

        $auth->assign($superAdmin, $employee->getId());

    }

    private function createAssArrayWithRolesAndPermissions(&$roles) : void
    {

        foreach ($this->listAccess() as $arrayItem) {
            foreach ($arrayItem['rules'] as $item) {
                if ($item['allow']) {
                    foreach ($item['roles'] as $role) {
                        if (isset($item['actions'])) {
                            foreach ($item['actions'] as $action) {
                                $roles[$role][$arrayItem['controller']][$action] = 1;
                            }
                        } else {
                            $roles[$role][$arrayItem['controller']]['*'] = 1;
                        }
                    }
                }
            }
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $this->createAssArrayWithRolesAndPermissions($roles);
        foreach ($this->createDataItem($roles) as $item) {
            $permission = $auth->getPermission($item[0]);
            $auth->remove($permission);
        }
        $permission = $auth->getPermission('/*');
        $auth->remove($permission);

        $trainerAgents = $auth->createPermission('trainerAgents');
        $trainerAgents->description = 'Trainer agents';
        $auth->add($trainerAgents);

        $manageLeads = $auth->createPermission('manageLeads');
        $manageLeads->description = 'Manage leads';
        $auth->add($manageLeads);

        $manageAgents = $auth->createPermission('manageAgents');
        $manageAgents->description = 'Manage agents';
        $auth->add($manageAgents);

        $coach = $auth->createRole('coach');
        $coach->description = 'Agents coach';
        $auth->add($coach);

        $auth->addChild($coach, $trainerAgents);

        $agent = $auth->getRole('agent');
        $auth->addChild($agent, $coach);
        $auth->addChild($agent, $manageLeads);

        $supervision = $auth->getRole('supervision');
        $auth->addChild($supervision, $agent);
        $auth->addChild($supervision, $manageAgents);

        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $supervision);

        $roleSuperadmin = $auth->getRole('superadmin');
        $auth->remove($roleSuperadmin);

        if (($model = \common\models\Employee::findOne(['username' => 'superadmin'])) !== null) {
            $model->delete();
        }

        if (($model = \common\models\Employee::findOne(241)) !== null) {
            $auth->revoke($agent, 241);
            $auth->assign($coach, 241);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    private function listAccess() : array
    {
        return [
            [
                'controller' => 'AgentReportController',
                'rules' => [
                    [
                        'actions' => ['index','calls','sms','email','cloned','created','sold','from-to-leads'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'ApiLogController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'create', 'delete', 'delete-all'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                ],
            ],
            [
                'controller' => 'ApiUserController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'create', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                ],
            ],
            [
                'controller' => 'CallController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'inbox', 'soft-delete', 'all-read'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin', 'qa'],
                    ],
                    [
                        'actions' => ['list'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['delete', 'create', 'user-map'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list', 'auto-redial'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'CleanController',
                'rules' => [
                    [
                        'actions' => ['index', 'cache', 'assets', 'runtime'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                ],
            ],
            [
                'controller' => 'ClientController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'ajax-get-info'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['ajax-get-info'],
                        'allow' => true,
                        'roles' => ['supervision', 'agent'],
                    ],
                ],
            ],
            [
                'controller' => 'ClientPhoneController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'EmailController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'inbox', 'soft-delete', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['inbox', 'view', 'soft-delete'],
                        'allow' => true,
                        'roles' => ['agent', 'supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'EmailTemplateTypeController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'synchronization'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'EmployeeController',
                'rules' => [
                    [
                        'actions' => ['switch'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['list', 'update', 'create', 'acl-rule'],
                        'allow' => true,
                        'roles' => ['supervision', 'userManager'],
                    ],
                    [
                        'actions' => ['seller-contact-info'],
                        'allow' => true,
                        'roles' => ['agent', 'supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'KpiController',
                'rules' => [
                    [
                        'actions' => ['index','details'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadCallExpertController',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadController',
                'rules' => [
                    [
                        'actions' => [
                            'create', 'add-comment', 'change-state', 'unassign', 'take', 'auto-take',
                            'set-rating', 'add-note', 'unprocessed', 'call-expert', 'send-email',
                            'check-updates', 'flow-transition', 'get-user-actions', 'add-pnr', 'update2','clone',
                            'get-badges', 'sold', 'split-profit', 'split-tips','processing', 'follow-up', 'booked',
                            'test', 'view'
                        ],
                        'allow' => true,
                        'roles' => ['agent', 'admin', 'supervision'],
                    ],
                    [
                        'actions' => ['trash'],
                        'allow' => true,
                        'roles' => ['admin', 'supervision'],
                    ],

                    [
                        'actions' => ['inbox'],
                        'allow' => true,
                        'roles' => ['agent', 'admin', 'supervision'],
                    ],

                    [
                        'actions' => [
                            'pending', 'duplicate'
                        ],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],


                    [
                        'actions' => [
                            'view', 'trash', 'sold', 'flow-transition'
                        ],
                        'allow' => true,
                        'roles' => ['qa'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadFlightSegmentController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadFlowController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'export', 'duplicate', 'view', 'ajax-activity-logs'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['ajax-activity-logs'],
                        'allow' => true,
                        'roles' => ['qa'],
                    ],
                    [
                        'actions' => ['index', 'ajax-reason-list'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'LeadTaskController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'LogController',
                'rules' => [
                    [
                        'actions' => ['index', 'clear', 'view', 'create', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'NotificationsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['agent', 'qa', 'supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'PhoneController',
                'rules' => [
                    [
                        'actions' => ['index', 'get-token', 'test', 'ajax-phone-dial', 'ajax-save-call', 'ajax-call-redirect'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['index', 'get-token', 'test', 'ajax-phone-dial', 'ajax-save-call', 'ajax-call-redirect'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'ProfitBonusController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'ProjectController',
                'rules' => [
                    [
                        //'actions' => ['index', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'QuoteController',
                'rules' => [
                    [
                        'actions' => [
                            'create', 'save', 'decline', 'calc-price', 'extra-price', 'clone',
                            'send-quotes', 'get-online-quotes','get-online-quotes-old','status-log','preview-send-quotes',
                            'create-quote-from-search','preview-send-quotes-new',
                        ],

                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'QuotePriceController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'QuotesController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'ajax-details'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['ajax-details'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'QuoteStatusLogController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'export', 'duplicate'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin']
                    ],
                    [
                        'actions' => ['view', 'index', 'ajax-reason-list'],
                        'allow' => true,
                        'roles' => ['agent']
                    ]
                ]
            ],
            [
                'controller' => 'ReportController',
                'rules' => [
                    [
                        'actions' => [
                            'sold', 'view-sold'
                        ],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                    [
                        'actions' => [
                            'agents'
                        ],
                        'allow' => true,
                        'roles' => ['admin', 'supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'SettingController',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'SettingsController',
                'rules' => [
                    [
                        'actions' => [
                            'projects', 'airlines', 'airports', 'logging', 'acl', 'email-template',
                            'sync', 'view-log', 'acl-rule', 'project-data', 'synchronization'
                        ],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
//            [
//                'controller' => 'SiteController',
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['index', 'logout', 'profile', 'get-airport', 'blank'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
            [
                'controller' => 'SmsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'inbox', 'soft-delete'], //'delete', 'create',
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],

                    [
                        'actions' => ['index', 'view', 'inbox'], //'delete', 'create',
                        'allow' => true,
                        'roles' => ['qa'],
                    ],

                    [
                        'actions' => ['delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            [
                'controller' => 'SmsTemplateTypeController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'synchronization'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'SourcesController',
                'rules' => [
                    [
                        //'actions' => ['index', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'StatsController',
                'rules' => [
                    [
                        'actions' => ['index', 'call-sms', 'calls-graph', 'sms-graph', 'emails-graph'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['call-sms', 'calls-graph', 'sms-graph', 'emails-graph'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ],
            [
                'controller' => 'TaskController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'ToolsController',
                'rules' => [
                    [
                        'actions' => ['clear-cache', 'supervisor'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ]
                ],
            ],
            [
                'controller' => 'UserCallStatusController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'update-status'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'UserConnectionController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'stats'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'UserGroupAssignController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['admin','userManager'], //'supervision',
                    ],
                ],
            ],
            [
                'controller' => 'UserGroupController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['admin','userManager'], //'supervision',
                    ],
                ],
            ],
            [
                'controller' => 'UserParamsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            [
                'controller' => 'UserProjectParamsController',
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'view', 'create-ajax', 'update-ajax'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['userManager'],
                    ],
                    [
                        'actions' => ['update', 'create', 'view', 'create-ajax', 'update-ajax'],
                        'allow' => true,
                        'roles' => ['supervision', 'userManager'],
                    ],
                ],
            ],

        ];
    }

}
