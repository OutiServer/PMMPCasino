<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Database\SlotConfig;

use Ken_Cir\EconomyCore\Database\Base\BaseData;
use poggit\libasynql\DataConnector;

class SlotConfigData extends BaseData
{
    private int $id;

    private int $jp;

    private string $latestJPPlayerXuid;

    private int $latestJP;

    public function __construct(DataConnector $dataConnector, int $id, int $jp, string $latestJPPlayerXuid, int $latestJP)
    {
        parent::__construct($dataConnector);

        $this->id = $id;
        $this->jp = $jp;
        $this->latestJPPlayerXuid = $latestJPPlayerXuid;
        $this->latestJP = $latestJP;
    }

    protected function update(): void
    {
        $this->dataConnector->executeChange("");
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getJp(): int
    {
        return $this->jp;
    }

    /**
     * @param int $jp
     */
    public function setJp(int $jp): void
    {
        $this->jp = $jp;
    }

    /**
     * @return string
     */
    public function getLatestJPPlayerXuid(): string
    {
        return $this->latestJPPlayerXuid;
    }

    /**
     * @param string $latestJPPlayerXuid
     */
    public function setLatestJPPlayerXuid(string $latestJPPlayerXuid): void
    {
        $this->latestJPPlayerXuid = $latestJPPlayerXuid;
    }

    /**
     * @return int
     */
    public function getLatestJP(): int
    {
        return $this->latestJP;
    }

    /**
     * @param int $latestJP
     */
    public function setLatestJP(int $latestJP): void
    {
        $this->latestJP = $latestJP;
    }
}