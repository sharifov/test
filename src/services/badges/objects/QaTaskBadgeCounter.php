<?php

namespace src\services\badges\objects;

use modules\qaTask\src\entities\qaTask\search\CreateDto;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchClosedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchEscalatedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchPendingSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchProcessingSearch;
use src\access\ListsAccess;
use src\access\ProjectAccessService;
use src\access\QueryAccessService;
use src\auth\Auth;
use src\model\voiceMailRecord\entity\VoiceMailRecord;
use src\services\badges\BadgeCounterInterface;

/**
 * Class QaTaskBadgeCounter
 *
 * @property array|null $availableProjects
 * @property array|null $availableUsers
 * @property ProjectAccessService $projectAccessService
 * @property QueryAccessService $queryAccessService
 */
class QaTaskBadgeCounter implements BadgeCounterInterface
{
    private $availableProjects;
    private $availableUsers;
    private $projectAccessService;
    private $queryAccessService;

    public function __construct(
        ProjectAccessService $projectAccessService,
        QueryAccessService $queryAccessService
    ) {
        $this->projectAccessService = $projectAccessService;
        $this->queryAccessService = $queryAccessService;
    }

    public function countTypes(array $types): array
    {
        $result = [];
        foreach ($types as $type) {
            switch ($type) {
                case 'pending':
                    if ($count = $this->countPending()) {
                        $result['pending'] = $count;
                    }
                    break;
                case 'processing':
                    if ($count = $this->countProcessing()) {
                        $result['processing'] = $count;
                    }
                    break;
                case 'escalated':
                    if ($count = $this->countEscalated()) {
                        $result['escalated'] = $count;
                    }
                    break;
                case 'closed':
                    if ($count = $this->countClosed()) {
                        $result['closed'] = $count;
                    }
                    break;
            }
        }
        return $result;
    }

    private function getAvailableProjects(): array
    {
        if ($this->availableProjects !== null) {
            return $this->availableProjects;
        }
        $this->availableProjects = $this->projectAccessService->getProjects(Auth::user()->getAccess());
        return $this->availableProjects;
    }

    private function getAvailableUsers(): array
    {
        if ($this->availableUsers !== null) {
            return $this->availableUsers;
        }
        $this->availableUsers = (new ListsAccess(Auth::id()))->getEmployees();
        return $this->availableUsers;
    }

    private function countPending(): ?int
    {
        if (!Auth::can('/qa-task/qa-task-queue/pending')) {
            return null;
        }
        return (QaTaskSearchPendingSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }

    private function countProcessing(): ?int
    {
        if (!Auth::can('/qa-task/qa-task-queue/processing')) {
            return null;
        }
        return (QaTaskSearchProcessingSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }

    private function countEscalated(): ?int
    {
        if (!Auth::can('/qa-task/qa-task-queue/escalated')) {
            return null;
        }
        return (QaTaskSearchEscalatedSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }

    private function countClosed(): ?int
    {
        if (!Auth::can('/qa-task/qa-task-queue/closed')) {
            return null;
        }
        return (QaTaskSearchClosedSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }
}
