<?php

declare(strict_types=1);

namespace outiserver\casino\games;

use InvalidArgumentException;
use outiserver\economycore\Database\Economy\EconomyData;
use outiserver\economycore\EconomyCore;
use outiserver\casino\CasinoMain;
use outiserver\casino\database\slot\SlotData;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentToggle;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class SlotGame extends BaseGame
{
    private SlotData $slotData;

    private int $runCount;

    private array $result;

    private int $slotX;

    private int $slotY;

    private array $payLines;

    public function __construct(Player $player, CasinoMain $plugin, SlotData $slotData)
    {
        parent::__construct($player, $plugin);

        $this->slotData = $slotData;
        $this->runCount = 0;
        $this->result = [];
        $this->slotX = 0;
        $this->slotY = 0;
        $this->payLines = [];
    }

    public function run(): void
    {
        parent::run();

        switch ($this->slotData->getType()) {
            case 0:
                if ($this->economyData->getMoney() < $this->slotData->getBet()) {
                    $this->player->sendMessage("[Casino] " . TextFormat::RED . " スロットを回すお金があと" . $this->slotData->getBet() - $this->economyData->getMoney() . "円足りません");
                    return;
                }

                $this->payLines = [0];
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
                    foreach ($data as $key => $_) {
                        if ($_) $this->payLines[] = $key;
                    }

                    if ($this->economyData->getMoney() < ($this->slotData->getBet() * count($this->payLines))) {
                        $this->player->sendMessage("[Casino] " . TextFormat::RED . " スロットを回すお金があと" .  ($this->slotData->getBet() * count($this->payLines)) - $this->economyData->getMoney() . "円足りません");
                        return;
                    }

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
                        foreach ($data as $key => $_) {
                            if ($_) $this->payLines[] = $key;
                        }

                        if ($this->economyData->getMoney() < ($this->slotData->getBet() * count($this->payLines))) {
                            $this->player->sendMessage("[Casino] " . TextFormat::RED . " スロットを回すお金があと" .  ($this->slotData->getBet() * count($this->payLines)) - $this->economyData->getMoney() . "円足りません");
                            return;
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

        switch ($this->slotData->getType()) {
            case 0:
                $this->slotX = 1;
                $this->slotY = 3;
                break;
            case 1:
                $this->slotX = 3;
                $this->slotY = 3;
                break;
            case 2:
                $this->slotX = 3;
                $this->slotY = 5;
                break;
            default:
                throw new InvalidArgumentException("Unknown slot type {$this->slotData->getType()}");
        }

        // 初回
        if ($this->runCount <= 1) {
            for ($i = 0; $i < $this->slotX; $i++) {
                if ($i < 1) $slotTitle .= "§f";
                else $slotTitle .= "\n§f";

                for ($y = 0; $y < $this->slotY; $y++) {
                    if ($y < 1) $slotTitle .= "[§e?§f]";
                    else $slotTitle .= "-[§e?§f]";
                }
            }
        }
        else {
            for ($i = 0; $i < $this->slotX; $i++) {
                if (!isset($this->result[$i])) $this->result[$i] = [];
                if ($i < 1) $slotTitle .= "§f";
                else $slotTitle .= "\n§f";

                $rand = rand(0, 9);
                for ($y = 0; $y < $this->slotY; $y++) {
                    if (!isset($this->result[$i][$y]) and $y < ($this->runCount - 1)) $this->result[$i][$y] = $rand;

                    if ($y < 1) $slotTitle .= "[§e{$this->result[$i][$y]}§f]";
                    elseif ($y < ($this->runCount - 1)) $slotTitle .= "-[§e{$this->result[$i][$y]}§f]";
                    else $slotTitle .= "-[§e?§f]";
                }
            }
        }

        $this->player->sendTitle($slotTitle);
        $this->plugin->playSoundPlayer($this->player, "random.click");
        if ($this->runCount <= $this->slotY) {
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