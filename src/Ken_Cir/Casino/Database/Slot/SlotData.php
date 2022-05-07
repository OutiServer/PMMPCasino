<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Database\Slot;

use Ken_Cir\EconomyCore\Database\Base\BaseData;
use poggit\libasynql\DataConnector;

class SlotData extends BaseData
{

    private int $id;

    private int $parentId;

    private string $name;

    private string $worldName;

    private int $x;

    private int $y;

    private int $z;

    private int $bet;

    private int $sideline;

    public function __construct(DataConnector $dataConnector, int $id, int $parentId, string $name, string $worldName, int $x, int $y, int $z, int $bet, int $sideline)
    {
        parent::__construct($dataConnector);

        $this->id = $id;
        $this->parentId = $parentId;
        $this->name = $name;
        $this->worldName = $worldName;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->bet = $bet;
        $this->sideline = $sideline;
    }

    protected function update(): void
    {
        $this->dataConnector->executeChange("economy.casino.slots.update",
            [
                "parentId" => $this->parentId,
                "name" => $this->name,
                "world_name" => $this->worldName,
                "x" => $this->x,
                "y" => $this->y,
                "z" => $this->z,
                "bet" => $this->bet,
                "sideline" => $this->sideline
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
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
        $this->update();
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
     * @return string
     */
    public function getWorldName(): string
    {
        return $this->worldName;
    }

    /**
     * @param string $worldName
     */
    public function setWorldName(string $worldName): void
    {
        $this->worldName = $worldName;
        $this->update();
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @param int $x
     */
    public function setX(int $x): void
    {
        $this->x = $x;
        $this->update();
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
        $this->update();
    }

    /**
     * @return int
     */
    public function getZ(): int
    {
        return $this->z;
    }

    /**
     * @param int $z
     */
    public function setZ(int $z): void
    {
        $this->z = $z;
        $this->update();
    }

    /**
     * @return int
     */
    public function getBet(): int
    {
        return $this->bet;
    }

    /**
     * @param int $bet
     */
    public function setBet(int $bet): void
    {
        $this->bet = $bet;
        $this->update();
    }

    /**
     * @return int
     */
    public function getSideline(): int
    {
        return $this->sideline;
    }

    /**
     * @param int $sideline
     */
    public function setSideline(int $sideline): void
    {
        $this->sideline = $sideline;
        $this->update();
    }
}