<?php

declare(strict_types=1);

namespace outiserver\casino\games;

use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentInput;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use outiserver\casino\CasinoMain;
use outiserver\casino\database\gacha\GachaData;
use outiserver\casino\database\gachaitem\GachaItemData;
use outiserver\casino\database\gachaitem\GachaItemDataManager;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class GachaGame extends BaseGame
{
    private GachaData $gachaData;

    /**
     * @var GachaItemData[]
     */
    private array $resultItem;

    public function __construct(Player $player, CasinoMain $plugin, GachaData $gachaData)
    {
        parent::__construct($player, $plugin);

        $this->gachaData = $gachaData;
        $this->resultItem = [];
    }

    public function run(): void
    {
        parent::run();

        (new CustomForm($this->plugin,
        $this->player,
        "ガチャ {$this->gachaData->getName()}",
        [
            new ContentInput("ガチャを回す回数", "roll_count", inputType: ContentInput::TYPE_INT)
        ],
        function (Player $player, array $data) {
            $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($data): void {
                $this->roll((int)$data[0]);
            }), 7 * 20);

            $this->player->sendMessage("[Casino] [ガチャ] " . TextFormat::GREEN . "ガチャを回しています...");
            $this->plugin->playSoundPlayer($this->player, "example.sample");
        }));
    }

    /**
     * ガチャを回します
     * @param int $count
     * @return void
     */
    private function roll(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $gachaItems = GachaItemDataManager::getInstance()->getGachaId($this->gachaData->getId(), true);
            $all = 0;
            $total = 0;

            foreach ($gachaItems as $gachaItem) {
                $all += $gachaItem->getRand();
            }

            $result = rand(0, $all);
            foreach ($gachaItems as $gachaItem) {
                $total += $gachaItem->getRand();
                if ($result <= $total) {
                    $this->resultItem[] = $gachaItem;
                    $gachaItem->removeCount(1);
                    break;
                }
            }
        }

        $this->complete();
    }

    public function complete(): void
    {
        parent::complete();
        
        foreach ($this->resultItem as $item) {
            $giveItem = ItemFactory::getInstance()->get($item->getItemId(), $item->getItemMeta());
            if ($this->player->getInventory()->canAddItem($giveItem)) {
                $this->player->getInventory()->addItem($giveItem);
                $this->player->sendMessage("[Casino] [ガチャ] " . TextFormat::GREEN . "{$giveItem->getName()}を付与しました");
            }
        }
        $this->plugin->playSoundPlayer($this->player, "random.levelup");
    }
}