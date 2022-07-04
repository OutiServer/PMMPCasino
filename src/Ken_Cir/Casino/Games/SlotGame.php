<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Games;

use InvalidArgumentException;
use Ken_Cir\Casino\CasinoMain;
use Ken_Cir\Casino\Database\Slot\SlotData;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentToggle;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class SlotGame extends BaseGame
{
    private SlotData $slotData;

    private array $payLines;

    private int $runCount;

    public function __construct(Player $player, CasinoMain $plugin, SlotData $slotData)
    {
        parent::__construct($player, $plugin);

        $this->slotData = $slotData;
        $this->payLines = [];
        $this->runCount = 0;
    }

    public function run(): void
    {
        switch ($this->slotData->getType()) {
            case 0:
                $this->runSlot();
                break;
            case 1:
                $contents = [];
                for ($i = 0; $i < 8; $i++) {
                    $contents[] = new ContentToggle("ライン" . $i + 1);
                }

                new CustomForm($this->player,
                "[Casino] スロット3x3 ペイライン選択",
                $contents,
                function (Player $player, array $data): void {
                    foreach ($data as $key => $line) {
                        if ($line) {
                            $this->payLines[] = $key;
                        }
                    }

                    $this->runSlot();
                });
                break;
            case 2:
                $contents = [];
                for ($i = 0; $i < 48; $i++) {
                    $contents[] = new ContentToggle("ライン" . $i + 1);
                }
                new CustomForm($this->player,
                    "[Casino] スロット3x5 ペイライン選択",
                    $contents,
                    function (Player $player, array $data): void {
                        foreach ($data as $key => $line) {
                            if ($line) {
                                $this->payLines[] = $key;
                            }
                        }

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
                $beside = 1;
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
        if ($this->runCount <= 2) {
            $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void {
                call_user_func_array([$this, ["runSlot"]], []);
            }), 30);
        }
        else {
            $this->complete();
        }
    }

    public function complete(): void
    {
        $this->player->sendMessage("処理終了");
    }
}