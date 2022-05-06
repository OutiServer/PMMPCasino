<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Database\SlotConfig;

use Ken_Cir\EconomyCore\Database\Base\BaseDataManager;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;

class SlotConfigDataManager extends BaseDataManager
{
    use SingletonTrait;

    private int $seq;

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
                    $this->data[$data["id"]] = new SlotConfigData($this->dataConnector, $data["id"], $data["jp"], $data["latest_jp_player_xuid"], $data["latest_jp"]);
                }
            });
    }

    public function get(int $id): ?SlotConfigData
    {
        if (!isset($this->data[$id])) return null;
        return $this->data[$id];
    }

    public function create(int $defaultJP): SlotConfigData
    {
        $this->dataConnector->executeInsert("economy.casino.slots.create",
            [
                "jp" => $defaultJP
            ]);

        return ($this->data[++$this->seq] = new SlotConfigData($this->dataConnector, $this->seq, $defaultJP, "なし", 0));
    }

    public function delete(int $id): void
    {
        if ($this->get($id) === null) return;

        $this->dataConnector->executeGeneric(
            "economy.casino.slots..delete",
            [
                "id" => $id
            ]);
        unset($this->data[$id]);
    }
}