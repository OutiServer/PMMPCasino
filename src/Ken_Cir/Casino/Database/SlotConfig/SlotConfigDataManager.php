<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Database\SlotConfig;

use Ken_Cir\Casino\Database\Slot\SlotDataManager;
use Ken_Cir\EconomyCore\Database\Base\BaseAutoincrement;
use Ken_Cir\EconomyCore\Database\Base\BaseDataManager;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;

class SlotConfigDataManager extends BaseDataManager
{
    use SingletonTrait;
    use BaseAutoincrement;

    public function __construct(DataConnector $dataConnector)
    {
        parent::__construct($dataConnector);
        self::setInstance($this);

        $this->dataConnector->executeSelect(
            "economy.casino.slot_configs.seq",
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
        $this->dataConnector->executeSelect("economy.casino.slot_configs.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new SlotConfigData($this->dataConnector, $data["id"], $data["name"], $data["jp"], $data["latest_jp_player_xuid"], $data["latest_jp"]);
                }
            });
    }

    public function get(int $id): ?SlotConfigData
    {
        if (!isset($this->data[$id])) return null;
        return $this->data[$id];
    }

    public function getName(string $name): ?SlotConfigData
    {
        $result = array_filter($this->data, function (SlotConfigData $slotConfigData) use ($name) {
            return $slotConfigData->getName() === $name;
        });

        if (count($result) < 1) return null;
        return current($result);
    }

    public function create(string $name, int $defaultJP): SlotConfigData
    {
        $this->dataConnector->executeInsert("economy.casino.slot_configs.create",
            [
                "jp" => $defaultJP,
                "name" => $name
            ]);

        return ($this->data[++$this->seq] = new SlotConfigData($this->dataConnector, $this->seq, $name, $defaultJP, "なし", 0));
    }

    public function delete(int $id): void
    {
        if ($this->get($id) === null) return;

        $slotDataManager = SlotDataManager::getInstance();
        foreach ($slotDataManager->getAll(false) as $slotData) {
            if ($slotData->getParentId() === $id) {
                $slotDataManager->delete($slotData->getId());
            }
        }

        $this->dataConnector->executeGeneric(
            "economy.casino.slot_configs.delete",
            [
                "id" => $id
            ]);
        unset($this->data[$id]);
    }
}