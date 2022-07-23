<?php

declare(strict_types=1);

namespace outiserver\casino\database\slot;

use outiserver\economycore\database\Base\BaseData;
use poggit\libasynql\DataConnector;

class SlotData extends BaseData
{
    /**
     * 管理用ID
     *
     * @var int
     */
    private int $id;

    /**
     * スロットタイプ
     *
     * 0: 縦1x横3
     * 1: 縦3x横3
     * 2: 縦3x横5
     * @var int
     */
    private int $type;

    /**
     * SlotConfigのID
     *
     * @var int
     */
    private int $parentId;

    /**
     * スロット名
     *
     * @var string
     */
    private string $name;

    /**
     * スロットのあるワールド名
     *
     * @var string
     */
    private string $worldName;

    /**
     * X座標
     *
     * @var int
     */
    private int $x;

    /**
     * Y座標
     *
     * @var int
     */
    private int $y;

    /**
     * Z座標
     *
     * @var int
     */
    private int $z;

    /**
     * 1ラインのベット数(クレジット)
     *
     * @var int
     */
    private int $bet;

    public function __construct(DataConnector $dataConnector, int $id, int $type, int $parentId, string $name, string $worldName, int $x, int $y, int $z, int $bet)
    {
        parent::__construct($dataConnector);

        $this->id = $id;
        $this->type = $type;
        $this->parentId = $parentId;
        $this->name = $name;
        $this->worldName = $worldName;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->bet = $bet;
    }

    protected function update(): void
    {
        $this->dataConnector->executeChange("economy.casino.slots.update",
            [
                "type" => $this->type,
                "parent_id" => $this->parentId,
                "name" => $this->name,
                "bet" => $this->bet,
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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
        $this->update();
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
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ(): int
    {
        return $this->z;
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
}