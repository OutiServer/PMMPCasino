<?php

declare(strict_types=1);

namespace outiserver\casino\database\gachaitem;

use outiserver\casino\database\gacha\GachaData;
use outiserver\economycore\Database\Base\BaseAutoincrement;
use outiserver\economycore\Database\Base\BaseDataManager;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;

class GachaItemDataManager extends BaseDataManager
{
    use SingletonTrait;
    use BaseAutoincrement;

    public function __construct(DataConnector $dataConnector)
    {
        parent::__construct($dataConnector);
        self::setInstance($this);

        $this->dataConnector->executeSelect(
            "economy.casino.gacha_items.seq",
            [],
            function (array $row) {
                if (count($row) < 1) {
                    $this->seq = 0;
                    return;
                }
                foreach ($row as $data) {
                    $this->seq = $data["seq"];
                }
            });
        $this->dataConnector->executeSelect("economy.casino.gacha_items.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new GachaItemData($this->dataConnector, $data["id"], $data["gacha_id"], $data["item_id"], $data["item_meta"], $data["rand"], $data["count"]);
                }
            });
    }

    public function get(int $id): ?GachaItemData
    {
        if (!isset($this->data[$id])) return null;
        return $this->data[$id];
    }

    public function getItemId(int $id, int $meta): ?GachaItemData
    {
        $result = array_filter($this->data, function (GachaItemData $gachaItemData) use ($id, $meta) {
            return $gachaItemData->getItemId() === $id and $gachaItemData->getItemMeta() === $meta;
        });

        if (count($result) < 1) return null;
        return current($result);
    }

    /**
     * @param int $id
     * @param bool $keyValue
     * @return GachaItemData
     */
    public function getGachaId(int $id, bool $keyValue): array
    {
        $data = array_filter($this->data, function (GachaItemData $gachaItemData) use ($id) {
            return $id === $gachaItemData->getGachaId();
        });

        if ($keyValue) return array_values($data);
        return $data;
    }

    public function create(int $gachaId, int $itemId, int $itemMeta, int $rand, int $count): GachaItemData
    {
        $this->dataConnector->executeInsert("economy.casino.gacha_items.create",
            [
                "gacha_id" => $gachaId,
                "item_id" => $itemId,
                "item_meta" => $itemMeta,
                "rand" => $rand,
                "count" => $count
            ]);

        return ($this->data[++$this->seq] = new GachaItemData($this->dataConnector, $this->seq, $gachaId, $itemId, $itemMeta, $rand, $count));
    }

    public function delete(int $id): void
    {
        if (!$this->get($id)) return;

        $this->dataConnector->executeGeneric(
            "economy.casino.gacha_items.delete",
            [
                "id" => $id
            ]);
        unset($this->data[$id]);
    }
}