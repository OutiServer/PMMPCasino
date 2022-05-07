<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Database\Slot;

use Ken_Cir\Casino\Database\SlotConfig\SlotConfigData;
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

        $this->dataConnector->executeSelect("economy.casino.slots.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new SlotConfigData($this->dataConnector, $data["id"], $data["jp"], $data["latest_jp_player_xuid"], $data["latest_jp"]);
                }
            });
        $this->dataConnector->executeSelect("economy.casino.slots.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new SlotConfigData($this->dataConnector, $data["id"], $data["jp"], $data["latest_jp_player_xuid"], $data["latest_jp"]);
                }
            });
    }
}