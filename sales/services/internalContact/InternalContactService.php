<?php

namespace sales\services\internalContact;

use common\models\DepartmentEmailProject;
use common\models\Project;
use common\models\DepartmentPhoneProject;
use common\models\UserProjectParams;

class InternalContactService
{
    /**
     * @param string $email
     * @param int|null $incomingProject
     * @return InternalContact
     */
    public function findByEmail(string $email, ?int $incomingProject): InternalContact
    {
        $contact = $this->loadContactByEmail($email, $incomingProject);
        $this->processProject($contact, $incomingProject);
        return $contact;
    }

    /**
     * @param string $phone
     * @param int|null $incomingProject
     * @return InternalContact
     */
    public function findByPhone(string $phone, ?int $incomingProject): InternalContact
    {
        $contact = $this->loadContactByPhone($phone, $incomingProject);
        $this->processProject($contact, $incomingProject);
        return $contact;
    }

    /**
     * @param InternalContact $contact
     * @param int|null $incomingProject
     */
    private function processProject(InternalContact $contact, ?int $incomingProject): void
    {
        if ($contact->projectId === null) {
            $contact->replaceProject($this->findIncomingProject($contact, $incomingProject));
        }
    }

    /**
     * @param InternalContact $contact
     * @param int|null $incomingProject
     * @return int|null
     */
    private function findIncomingProject(InternalContact $contact, ?int $incomingProject): ?int
    {
        if ($incomingProject === null) {
            return null;
        }

        if ($project = Project::findOne($incomingProject)) {
            return $project->id;
        }

        $contact->addLog('Incoming Project Id: ' . $incomingProject . ' not found');
        return null;
    }

    /**
     * @param string $phone
     * @param int|null $incomingProject
     * @return InternalContact
     */
    private function loadContactByPhone(string $phone, ?int $incomingProject): InternalContact
    {
        $log = new Log();

        if ($incomingProject === null) {
            $log->add('Incoming Project is empty');
        }

        if ($dpp = DepartmentPhoneProject::find()->findByPhone($phone)->one()) {
            if ($dpp->dpp_dep_id && $department = $dpp->dppDep) {
                if ($dpp->dpp_project_id === null) {
                    $log->add('Not found project for departmentPhoneProject Id: ' . $dpp->dpp_id);
                }
                if ($incomingProject && $dpp->dpp_project_id && $incomingProject !== $dpp->dpp_project_id) {
                    $log->add('Incoming Project not equal for ' . $phone . ' departmentPhoneProject Id: ' . $dpp->dpp_id . '. Incoming ProjectId: ' . $incomingProject . '. Found ProjectId: ' . $dpp->dpp_project_id);
                }
                return new InternalContact($department, $dpp->dpp_project_id, null, $log);
            }
            $log->add('Not found department for departmentPhoneProject Id: ' . $dpp->dpp_id);
        }

        if ($upp = UserProjectParams::find()->byPhone($phone)->one()) {
            if ($upp->upp_dep_id && $department = $upp->uppDep) {
                if ($upp->upp_project_id === null) {
                    $log->add('Not found project for userProjectParams tw_phone_number: ' . $upp->upp_tw_phone_number);
                }
                if ($incomingProject && $upp->upp_project_id && $incomingProject !== $upp->upp_project_id) {
                    $log->add('Incoming Project not equal for ' . $phone . ' userProjectParams. Incoming ProjectId: ' . $incomingProject . '. Found ProjectId: ' . $upp->upp_project_id);
                }
                return new InternalContact($department, $upp->upp_project_id, $upp->upp_user_id, $log);
            }
            $log->add('Not found department for userProjectParams tw_phone_number: ' . $upp->upp_tw_phone_number);
            if ($upp->uppUser) {
                if ($upp->uppUser->userDepartments && isset($upp->uppUser->userDepartments[0]) && $upp->uppUser->userDepartments[0]->udDep) {
                    if ($upp->upp_project_id === null) {
                        $log->add('Not found project for userProjectParams tw_phone_number: ' . $upp->upp_tw_phone_number);
                    }
                    if ($incomingProject && $upp->upp_project_id && $incomingProject !== $upp->upp_project_id) {
                        $log->add('Incoming Project not equal for ' . $phone . ' userProjectParams. Incoming ProjectId: ' . $incomingProject . '. Found ProjectId: ' . $upp->upp_project_id);
                    }
                    return new InternalContact($upp->uppUser->userDepartments[0]->udDep, $upp->upp_project_id, $upp->upp_user_id, $log);
                }
                $log->add('Not found department for user Id: ' . $upp->upp_user_id);
            }
        }

        $log->add('Not found department for phone: ' . $phone);
        return new InternalContact(null, null, null, $log);
    }

    /**
     * @param string $email
     * @param int|null $incomingProject
     * @return InternalContact
     */
    private function loadContactByEmail(string $email, ?int $incomingProject): InternalContact
    {
        $log = new Log();

        if ($incomingProject === null) {
            $log->add('Incoming Project is empty');
        }

        if ($dep = DepartmentEmailProject::find()->byEmail($email)->one()) {
            if ($dep->dep_dep_id && $department = $dep->depDep) {
                if ($incomingProject && $incomingProject !== $dep->dep_project_id) {
                    $log->add('Incoming Project not equal for ' . $email . ' DepartmentEmailProject Id: ' . $dep->dep_id . '. Incoming ProjectId: ' . $incomingProject . '. Found ProjectId: ' . $dep->dep_project_id);
                }
                return new InternalContact($department, $dep->dep_project_id, null, $log);
            }
            $log->add('Not found department for departmentEmailProject Id: ' . $dep->dep_id);
        }

        if ($upp = UserProjectParams::find()->byEmail($email)->one()) {
            if ($upp->upp_dep_id && $department = $upp->uppDep) {
                if ($incomingProject && $upp->upp_project_id && $incomingProject !== $upp->upp_project_id) {
                    $log->add('Incoming Project not equal for ' . $email . ' userProjectParams. Incoming ProjectId: ' . $incomingProject . '. Found ProjectId: ' . $upp->upp_project_id);
                }
                return new InternalContact($department, $upp->upp_project_id, $upp->upp_user_id, $log);
            }
//            $log->add('Not found department for userProjectParams email: ' . $upp->upp_email);
            $log->add('Not found department for userProjectParams email: ' . $upp->getEmail());
            if ($upp->uppUser) {
                if ($upp->uppUser->userDepartments && isset($upp->uppUser->userDepartments[0]) && $upp->uppUser->userDepartments[0]->udDep) {
                    if ($incomingProject && $upp->upp_project_id && $incomingProject !== $upp->upp_project_id) {
                        $log->add('Incoming Project not equal for ' . $email . ' userProjectParams. Incoming ProjectId: ' . $incomingProject . '. Found ProjectId: ' . $upp->upp_project_id);
                    }
                    return new InternalContact($upp->uppUser->userDepartments[0]->udDep, $upp->upp_project_id, $upp->upp_user_id, $log);
                }
                $log->add('Not found department for user Id: ' . $upp->upp_user_id);
            }
        }

        $log->add('Not found department for email: ' . $email);
        return new InternalContact(null, null, null, $log);
    }
}
