<?php


declare(strict_types=1);

namespace outiserver\casino\database\gachaitem;

use outiserver\economycore\Database\Base\BaseData;
use poggit\libasynql\DataConnector;

class GachaItemData extends BaseData
{
    /**
     * 識別用ID
     * @var int
     */
    private int $id;

    /**
     * ガチャID
     * @var int
     */
    private int $gachaId;

    /**
     * アイテムID
     * @var int
     */
    private int $itemId;

    /**
     * アイテムMeta
     * @var int
     */
    private int $itemMeta;

    /**
     * 排出率
     * @var int
     */
    private int $rand;

    /**
     * 在庫数
     * @var int
     */
    private int $count;

    public function __construct(DataConnector $dataConnector, int $id, int $gachaId, int $itemId, int $itemMeta, int $rand, int $count)
    {
        parent::__construct($dataConnector);

        $this->id = $id;
        $this->gachaId = $gachaId;
        $this->itemId = $itemId;
        $this->itemMeta = $itemMeta;
        $this->rand = $rand;
        $this->count = $count;
    }

    protected function update(): void
    {
        $this->dataConnector->executeChange("economy.casino.gacha_items.update",
            [
                "rand" => $this->rand,
                "count" => $this->count,
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
    public function getGachaId(): int
    {
        return $this->gachaId;
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * @return int
     */
    public function getItemMeta(): int
    {
        return $this->itemMeta;
    }

    /**
     * @return int
     */
    public function getRand(): int
    {
        return $this->rand;
    }

    /**
     * @param int $rand
     */
    public function setRand(int $rand): void
    {
        $this->rand = $rand;
        $this->update();
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function addCount(int $count): void
    {
        $this->count += $count;
        $this->update();
    }

    public function removeCount(int $count): void
    {
        $this->count -= $count;
        $this->update();
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
        $this->update();
    }
}