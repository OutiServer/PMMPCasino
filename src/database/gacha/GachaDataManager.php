<?php

declare(strict_types=1);

namespace outiserver\casino\database\gacha;

use outiserver\casino\database\gachaitem\GachaItemDataManager;
use outiserver\casino\database\slot\SlotData;
use outiserver\economycore\Database\Base\BaseAutoincrement;
use outiserver\economycore\Database\Base\BaseDataManager;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;

class GachaDataManager extends BaseDataManager
{
    use SingletonTrait;
    use BaseAutoincrement;

    public function __construct(DataConnector $dataConnector)
    {
        parent::__construct($dataConnector);
        self::setInstance($this);

        $this->dataConnector->executeSelect(
            "economy.casino.gachas.seq",
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
        $this->dataConnector->executeSelect("economy.casino.gachas.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new GachaData($this->dataConnector, $data["id"], $data["name"], $data["price"]);
                }
            });
    }

    public function get(int $id): ?GachaData
    {
        if (!isset($this->data[$id])) return null;
        return $this->data[$id];
    }

    public function getName(string $name): ?GachaData
    {
        $result = array_filter($this->data, function (GachaData $gachaData) use ($name) {
            return $gachaData->getName() === $name;
        });

        if (count($result) < 1) return null;
        return current($result);
    }

    public function create(string $name, int $price): GachaData
    {
        $this->dataConnector->executeInsert("economy.casino.gachas.create",
            [
                "name" => $name,
                "price" => $price
            ]);

        return ($this->data[++$this->seq] = new GachaData($this->dataConnector, $this->seq, $name, $price));
    }

    public function delete(int $id): void
    {
        if (!$this->get($id)) return;

       foreach (GachaItemDataManager::getInstance()->getGachaId($id) as $gachaItemData) {
           GachaItemDataManager::getInstance()->delete($gachaItemData->getId());
       }

        $this->dataConnector->executeGeneric(
            "economy.casino.gachas.delete",
            [
                "id" => $id
            ]);
        unset($this->data[$id]);
    }
}