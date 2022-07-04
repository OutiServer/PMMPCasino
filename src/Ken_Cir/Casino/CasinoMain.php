<?php

declare(strict_types=1);

namespace Ken_Cir\Casino;

use CortexPE\Commando\PacketHooker;
use Ken_Cir\Casino\Caches\CasinoCache\CasinoCacheManager;
use Ken_Cir\Casino\Commands\CasinoCommand;
use Ken_Cir\Casino\Database\Slot\SlotDataManager;
use Ken_Cir\Casino\Database\SlotConfig\SlotConfigDataManager;
use Ken_Cir\EconomyCore\EconomyCore;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class CasinoMain extends PluginBase
{
    use SingletonTrait;

    const CORE_VERSION = "1.0.0";

    const VERSION = "1.0.0";

    const CONFIG_VERSION = "1.0.0";

    const DATABASE_VERSION = "1.0.0";

    private DataConnector $dataConnector;

    private SlotConfigDataManager $slotConfigDataManager;

    private SlotDataManager $slotDataManager;

    private CasinoCacheManager $casinoCacheManager;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        if (EconomyCore::VERSION !== self::CORE_VERSION) {
            $this->getLogger()->emergency("EconomyCoreのバージョンが一致しません、このプラグインが動作に必要なバージョンは" . self::CORE_VERSION . "です");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        if (@file_exists("{$this->getDataFolder()}config.yml")) {
            $config = new Config("{$this->getDataFolder()}config.yml", Config::YAML);
            // データベース設定のバージョンが違う場合は
            if ($config->get("version") !== self::CONFIG_VERSION) {
                rename("{$this->getDataFolder()}config.yml", "{$this->getDataFolder()}config.yml.{$config->get("version")}");
                $this->getLogger()->warning("config.yml バージョンが違うため、上書きしました");
                $this->getLogger()->warning("前バージョンのconfig.ymlは{$this->getDataFolder()}config.yml.{$config->get("version")}にあります");
            }
        }
        $this->saveResource("config.yml");

        if (@file_exists("{$this->getDataFolder()}database.yml")) {
            $config = new Config("{$this->getDataFolder()}database.yml", Config::YAML);
            // データベース設定のバージョンが違う場合は
            if ($config->get("version") !== self::DATABASE_VERSION) {
                rename("{$this->getDataFolder()}database.yml", "{$this->getDataFolder()}database.yml.{$config->get("version")}");
                $this->getLogger()->warning("database.yml バージョンが違うため、上書きしました");
                $this->getLogger()->warning("前バージョンのdatabase.ymlは{$this->getDataFolder()}database.yml.{$config->get("version")}にあります");
            }
        }
        $this->saveResource("database.yml");

        $this->dataConnector = libasynql::create($this, (new Config("{$this->getDataFolder()}database.yml", Config::YAML))->get("database"), [
            "sqlite" => "sqlite.sql"
        ]);
        $this->dataConnector->executeGeneric("economy.casino.slot_configs.init");
        $this->dataConnector->executeGeneric("economy.casino.slots.init");
        $this->dataConnector->waitAll();
        $this->slotConfigDataManager = new SlotConfigDataManager($this->dataConnector);
        $this->slotDataManager = new SlotDataManager($this->dataConnector);
        $this->casinoCacheManager = new CasinoCacheManager();

        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->register($this->getName(), new CasinoCommand($this, "casino", "カジノコマンド", []));
    }

    protected function onDisable(): void
    {
        if (isset($this->dataConnector)) {
            $this->dataConnector->waitAll();
            $this->dataConnector->close();
        }
    }

    /**
     * @return DataConnector
     */
    public function getDataConnector(): DataConnector
    {
        return $this->dataConnector;
    }

    /**
     * @return SlotConfigDataManager
     */
    public function getSlotConfigDataManager(): SlotConfigDataManager
    {
        return $this->slotConfigDataManager;
    }

    /**
     * @return SlotDataManager
     */
    public function getSlotDataManager(): SlotDataManager
    {
        return $this->slotDataManager;
    }

    /**
     * @return CasinoCacheManager
     */
    public function getCasinoCacheManager(): CasinoCacheManager
    {
        return $this->casinoCacheManager;
    }
}