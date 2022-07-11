<?php

declare(strict_types=1);

namespace outiserver\casino\games;

use outiserver\casino\CasinoMain;
use pocketmine\player\Player;

abstract class BaseGame
{
    protected Player $player;

    protected CasinoMain $plugin;

    public function __construct(Player $player, CasinoMain $plugin)
    {
        $this->player = $player;
        $this->plugin = $plugin;
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