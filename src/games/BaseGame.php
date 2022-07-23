<?php

declare(strict_types=1);

namespace outiserver\casino\games;

use outiserver\economycore\Database\Economy\EconomyData;
use outiserver\economycore\Database\Economy\EconomyDataManager;
use outiserver\casino\CasinoMain;
use pocketmine\player\Player;

abstract class BaseGame
{
    protected Player $player;

    protected CasinoMain $plugin;

    protected EconomyData $economyData;

    public function __construct(Player $player, CasinoMain $plugin)
    {
        $this->player = $player;
        $this->plugin = $plugin;
        $this->economyData = EconomyDataManager::getInstance()->get($player->getXuid());
    }

    /**
     * ゲーム実行
     *
     * @return void
     */
    public function run(): void
    {
        $this->plugin->getCasinoCacheManager()->get($this->player->getXuid())->setCasinoRunning(true);
    }

    /**
     * ゲーム完了
     *
     * @return void
     */
    public function complete(): void
    {
        $this->plugin->getCasinoCacheManager()->get($this->player->getXuid())->setCasinoRunning(false);
    }
}