<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Database\Slot;

use Ken_Cir\EconomyCore\Database\Base\BaseAutoincrement;
use Ken_Cir\EconomyCore\Database\Base\BaseDataManager;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;

class SlotDataManager extends BaseDataManager
{
    use SingletonTrait;
    use BaseAutoincrement;

    public function __construct(DataConnector $dataConnector)
    {
        parent::__construct($dataConnector);
        self::setInstance($this);

        $this->dataConnector->executeSelect(
            "economy.casino.slots.seq",
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
        $this->dataConnector->executeSelect("economy.casino.slots.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new SlotData($this->dataConnector, $data["id"], $data["type"], $data["parent_id"], $data["name"], $data["world_name"], $data["x"], $data["y"], $data["z"], $data["bet"]);
                }
            });
    }

    public function get(int $id): ?SlotData
    {
        if (!isset($this->data[$id])) return null;

        return $this->data[$id];
    }

    public function getPos(string $worldName, int $x, int $y, int $z): ?SlotData
    {
        $data = array_filter($this->data, function (SlotData $slotData) use ($worldName, $x, $y, $z) {
            return $slotData->getWorldName() === $worldName and $slotData->getX() === $x and $slotData->getY() === $y and $slotData->getZ() === $z;
        });

        if (count($data) < 1) return null;

        return current($data);
    }

    public function create(int $type, int $parentId, string $name, string $worldName, int $x, int $y, int $z, int $bet): SlotData
    {
        $this->dataConnector->executeInsert("economy.casino.slots.create",
            [
                "type" => $type,
                "parent_id" => $parentId,
                "name" => $name,
                "world_name" => $worldName,
                "x" => $x,
                "y" => $y,
                "z" => $z,
                "bet" => $bet
            ]);

        return ($this->data[++$this->seq] = new SlotData($this->dataConnector, $this->seq, $type, $parentId, $name, $worldName, $x, $y ,$z, $bet));
    }

    public function delete(int $id): void
    {
        if (!$this->get($id)) return;

        $this->dataConnector->executeGeneric(
            "economy.casino.slots.delete",
            [
                "id" => $id
            ]);
        unset($this->data[$id]);
    }
}