<?php

declare(strict_types=1);

namespace outiserver\casino\database\slotconfig;

use outiserver\economycore\database\Base\BaseData;
use poggit\libasynql\DataConnector;

class SlotConfigData extends BaseData
{
    /**
     * 管理ID
     *
     * @var int
     */
    private int $id;

    /**
     * 名前
     *
     * @var string
     */
    private string $name;

    /**
     * 現在のJP
     *
     * @var int
     */
    private int $jp;

    /**
     * 最新のJP獲得プレイヤーXUID
     *
     * @var string
     */
    private string $latestJPPlayerXuid;

    /**
     * 最新のJP獲得額
     *
     * @var int
     */
    private int $latestJP;

    public function __construct(DataConnector $dataConnector, int $id, string $name, int $jp, string $latestJPPlayerXuid, int $latestJP)
    {
        parent::__construct($dataConnector);

        $this->id = $id;
        $this->name = $name;
        $this->jp = $jp;
        $this->latestJPPlayerXuid = $latestJPPlayerXuid;
        $this->latestJP = $latestJP;
    }

    protected function update(): void
    {
        $this->dataConnector->executeChange("economy.casino.slot_configs.update",
            [
                "name" => $this->name,
                "jp" => $this->jp,
                "latest_jp_player_xuid" => $this->latestJPPlayerXuid,
                "latest_jp" => $this->latestJP,
                "id" => $this->id
            ]);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->update();
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
        $this->update();
    }

    public function addJp(int $jp): void
    {
        $this->jp += $jp;
        $this->update();
    }

    public function removeJp(int $jp): void
    {
        $this->jp -= $jp;
        $this->update();
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
        $this->update();
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
        $this->update();
    }
}