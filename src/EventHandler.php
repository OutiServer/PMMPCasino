<?php

declare(strict_types=1);

namespace outiserver\casino;

use outiserver\casino\forms\CreateSlotForm;
use outiserver\casino\games\SlotGame;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventHandler implements Listener
{
    private CasinoMain $plugin;

    public function __construct(CasinoMain $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        $this->plugin->getCasinoCacheManager()->create($player->getXuid());
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        $this->plugin->getCasinoCacheManager()->delete($player->getXuid());
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;

        $player = $event->getPlayer();
        $pos = $event->getBlock()->getPosition();

        if (($slotData = $this->plugin->getSlotDataManager()->getPos($pos->getWorld()->getFolderName(), $pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ())) !== null and !$this->plugin->getCasinoCacheManager()->get($player->getXuid())->getCasinoRunning()) {
            (new SlotGame($player, $this->plugin, $slotData))->run();
        }
    }

    public function onSignChange(SignChangeEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $signText = $event->getNewText();
        $sign = $event->getSign();

        if ($signText->getLine(0) === "slot") {
            (new CreateSlotForm($block->getPosition()->getWorld()->getFolderName(), $block->getPosition()->getFloorX(), $block->getPosition()->getFloorY(), $block->getPosition()->getFloorZ(), $sign))->execute($player);
        }
    }
}