<?php

declare(strict_types=1);

namespace outiserver\casino\games;

use InvalidArgumentException;
use outiserver\casino\CasinoMain;
use outiserver\casino\database\slot\SlotData;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentToggle;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class SlotGame extends BaseGame
{
    private SlotData $slotData;

    private int $runCount;

    public function __construct(Player $player, CasinoMain $plugin, SlotData $slotData)
    {
        parent::__construct($player, $plugin);

        $this->slotData = $slotData;
        $this->runCount = 0;
    }

    public function run(): void
    {
        parent::run();

        switch ($this->slotData->getType()) {
            case 0:
                $this->runSlot();
                break;
            case 1:
                $contents = [];
                for ($i = 0; $i < 8; $i++) {
                    $contents[] = new ContentToggle("ライン" . $i + 1);
                }

                new CustomForm(CasinoMain::getInstance(),
                    $this->player,
                "[Casino] スロット3x3 ペイライン選択",
                $contents,
                function (Player $player, array $data): void {
                    $this->runSlot();
                });
                break;
            case 2:
                $contents = [];
                for ($i = 0; $i < 48; $i++) {
                    $contents[] = new ContentToggle("ライン" . $i + 1);
                }
                new CustomForm(CasinoMain::getInstance(),
                    $this->player,
                    "[Casino] スロット3x5 ペイライン選択",
                    $contents,
                    function (Player $player, array $data): void {
                        $this->runSlot();
                    });
                break;
            default:
                throw new InvalidArgumentException("Unknown slot type {$this->slotData->getType()}");
        }
    }

    private function runSlot(): void
    {
        $this->runCount++;

        $slotTitle = "";
        $vertical = 0;
        $beside = 0;

        switch ($this->slotData->getType()) {
            case 0:
                $vertical = 1;
                $beside = 3;
                break;
            case 1:
                $vertical = 3;
                $beside = 3;
                break;
            case 2:
                $vertical = 3;
                $beside = 5;
                break;
            default:
                throw new InvalidArgumentException("Unknown slot type {$this->slotData->getType()}");
        }

        // 初回
        if ($this->runCount <= 1) {
            for ($i = 0; $i < $vertical; $i++) {
                if ($i < 1) $slotTitle .= "§f";
                else $slotTitle .= "\n§f";

                for ($y = 0; $y < $beside; $y++) {
                    if ($y < 1) $slotTitle .= "[§e?§f]";
                    else $slotTitle .= "-[§e?§f]";
                }
            }
        }
        else {
            for ($i = 0; $i < $vertical; $i++) {
                if ($i < 1) $slotTitle .= "§f";
                else $slotTitle .= "\n§f";

                for ($y = 0; $y < $beside; $y++) {
                    $rand = rand(0, 9);
                    if ($y < 1) $slotTitle .= "[§e{$rand}§f]";
                    else $slotTitle .= "-[§e{$rand}§f]";
                }
            }
        }

        $this->player->sendTitle($slotTitle);
        $this->plugin->playSoundPlayer($this->player, "random.click");
        if ($this->runCount <= 3) {
            $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void {
                call_user_func_array([$this, "runSlot"], []);
            }), 30);
        }
        else {
            $this->complete();
        }
    }

    public function complete(): void
    {
        parent::complete();

        $this->plugin->playSoundPlayer($this->player, "random.levelup");
        $this->player->sendMessage("処理終了");
    }
}