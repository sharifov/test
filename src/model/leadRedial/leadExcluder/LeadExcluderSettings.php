<?php

namespace src\model\leadRedial\leadExcluder;

/**
 * Class LeadExcluderSettings
 *
 * @property array $projects
 * @property array $departments
 * @property array $cabins
 * @property bool $noFlightDetails
 * @property bool $isTest
 * @property bool $valid
 */
class LeadExcluderSettings
{
    private array $projects;
    private array $departments;
    private array $cabins;
    private bool $noFlightDetails;
    private bool $isTest;
    private bool $valid;

    private function __construct(
        array $projects,
        array $departments,
        array $cabins,
        bool $noFlightDetails,
        bool $isTest,
        bool $valid
    ) {
        $this->projects = $projects;
        $this->departments = $departments;
        $this->cabins = $cabins;
        $this->noFlightDetails = $noFlightDetails;
        $this->isTest = $isTest;
        $this->valid = $valid;
    }

    private static function createInvalidSettings(): self
    {
        return new self([], [], [], false, false, false);
    }

    public static function fromArray(array $settings): self
    {
        if (
            !array_key_exists('projects', $settings)
            || !array_key_exists('departments', $settings)
            || !array_key_exists('cabins', $settings)
            || !array_key_exists('noFlightDetails', $settings)
            || !array_key_exists('isTest', $settings)
        ) {
            return self::createInvalidSettings();
        }

        return new self(
            $settings['projects'],
            $settings['departments'],
            $settings['cabins'],
            $settings['noFlightDetails'],
            $settings['isTest'],
            true
        );
    }

    public function inProjects(string $project): bool
    {
        return in_array($project, $this->projects, true);
    }

    public function inDepartments(string $department): bool
    {
        return in_array($department, $this->departments, true);
    }

    public function inCabins(string $cabin): bool
    {
        return in_array($cabin, $this->cabins, true);
    }

    public function isNoFlightDetailsEnable(): bool
    {
        return $this->noFlightDetails;
    }

    public function inTest(bool $isTest): bool
    {
        return $this->isTest === $isTest;
    }

    public function isInvalid(): bool
    {
        return $this->valid === false;
    }
}
