<?php

declare(strict_types=1);

namespace outiserver\casino\games;

use InvalidArgumentException;
use outiserver\casino\database\slotconfig\SlotConfigData;
use outiserver\casino\particles\DragonBreathParticle;
use outiserver\casino\particles\ExplosionEmitterParticle;
use outiserver\casino\CasinoMain;
use outiserver\casino\database\slot\SlotData;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentToggle;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use pocketmine\color\Color;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\PotionSplashParticle;

class SlotGame extends BaseGame
{
    private SlotData $slotData;

    private SlotConfigData $slotConfigData;

    private int $runCount;

    private array $result;

    private int $slotX;

    private int $slotY;

    private array $payLines;

    public function __construct(Player $player, CasinoMain $plugin, SlotData $slotData)
    {
        parent::__construct($player, $plugin);

        $this->slotData = $slotData;
        $this->slotConfigData = $this->plugin->getSlotConfigDataManager()->get($this->slotData->getParentId());
        $this->runCount = 0;
        $this->result = [];
        $this->slotX = 0;
        $this->slotY = 0;
        $this->payLines = [];

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

                $this->economyData->removeMoney($this->slotData->getBet());
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

                    $this->economyData->removeMoney(($this->slotData->getBet() * count($this->payLines)));
                    $this->runSlot();
                },
                function () {
                    $this->plugin->getCasinoCacheManager()->get($this->player->getXuid())->setCasinoRunning(false);
                });
                break;
            case 2:
                $this->player->sendMessage("[Casino] " . TextFormat::YELLOW . "申し訳ありません、3x8スロットは現在準備中です");
                $this->plugin->getCasinoCacheManager()->get($this->player->getXuid())->setCasinoRunning(false);
                return;

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
                    },
                    function () {
                        $this->plugin->getCasinoCacheManager()->get($this->player->getXuid())->setCasinoRunning(false);
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

        $winLines = 0;
        $jp = false;
        // 1x3
        if ($this->slotX === 1 and $this->slotY === 3) {
            if ($this->result[0][0] === $this->result[0][1] and $this->result[0][0] === $this->result[0][2]) {
                if ($this->result[0][0] === 7) $jp = true;
                else $winLines++;
            }
        }
        // 3x3
        elseif ($this->slotX === 3 and $this->slotY === 3) {
            // 横あわせ確認
            for ($i = 0; $i < 3; $i++) {
                if ($this->result[$i][0] === $this->result[$i][1] and $this->result[$i][0] === $this->result[$i][2] and in_array($i, $this->payLines, true)) {
                    if ($this->result[$i][0] === 7 and !$jp) $jp = true;
                    else $winLines++;
                }
            }

            // 縦あわせ確認
            for ($i = 0; $i < 3; $i++) {
                if ($this->result[0][$i] === $this->result[1][$i] and $this->result[0][$i] === $this->result[2][$i] and in_array(($i + 3), $this->payLines, true)) {
                    if ($this->result[0][$i] === 7 and !$jp) $jp = true;
                    else $winLines++;
                }
            }

            // 斜め
            if ($this->result[0][0] === $this->result[1][1] and $this->result[0][0] === $this->result[2][2] and in_array(6, $this->payLines, true)) {
                if ($this->result[0][0] === 7 and !$jp) $jp = true;
                else $winLines++;
            }
            if ($this->result[0][2] === $this->result[1][1] and $this->result[0][2] === $this->result[2][0] and in_array(7, $this->payLines, true)) {
                if ($this->result[0][0] === 7 and !$jp) $jp = true;
                else $winLines++;
            }
        }

        // JP以外の当たりのみ
        if ($winLines > 0 and !$jp) {
            $this->player->sendMessage("[Casino] [{$this->slotConfigData->getName()}]" . TextFormat::GOLD . "{$winLines}ライン当たり！ " . ($winLines * ($this->slotData->getBet() * $this->plugin->getConfig()->get("reduction_rate", 10))) . "円獲得！");
        }
        // JPのみのあたり
        elseif ($winLines < 1 and $jp) {
            $this->player->sendMessage("[Casino] [{$this->slotConfigData->getName()}]" . TextFormat::GOLD . "ジャックポット！ " . $this->slotConfigData->getJp() . "円獲得！");
        }
        // JPとJP以外両方
        elseif ($winLines > 0 and $jp) {
            $this->player->sendMessage("[Casino] [{$this->slotConfigData->getName()}]" . TextFormat::GOLD . "ジャックポットと{$winLines}ライン当たり！ " . ($this->slotConfigData->getJp() + (($this->slotData->getBet() * $this->plugin->getConfig()->get("reduction_rate", 10)) * $winLines)) . "円獲得！");

        }

        // 当たり共通
        if ($jp or $winLines > 0) {
            $this->plugin->playSoundPlayer($this->player, "random.levelup");
        }

        // JP共通処理
        if ($jp) {
            $this->plugin->getServer()->broadcastMessage("[Casino] [{$this->slotConfigData->getName()}]" . TextFormat::GREEN . "{$this->player->getName()}さんがJPを当て、" . ($this->slotConfigData->getJp()) . "円獲得しました、おめでとうございます！");
            $this->plugin->playSoundPlayer($this->player, "raid.horn");
            $this->economyData->addMoney($this->slotConfigData->getJp());
            $pos = $this->player->getPosition();
            $random = new Random((int) (microtime(true) * 1000) + mt_rand());
            for ($y = 0; $y < 5; $y++) {
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);

                for($i = 0; $i < 10; ++$i){
                    $pos = $pos->add(
                        $random->nextSignedFloat(),
                        $random->nextSignedFloat(),
                        $random->nextSignedFloat()
                    );

                    $this->player->getWorld()->addParticle($pos, new PotionSplashParticle(new Color($r, $g, $b)));
                }

            }

            $this->slotConfigData->setLatestJP($this->slotConfigData->getJp());
            $this->slotConfigData->setLatestJPPlayerXuid($this->player->getXuid());
            $this->slotConfigData->setJp($this->plugin->getConfig()->get("default_slot_jp", 10000));
            $this->plugin->getServer()->broadcastMessage("[Casino] [{$this->slotConfigData->getName()}] JPが{$this->plugin->getConfig()->get("default_slot_jp", 10000)}円にリセットされました");
        }

        // 普通のあたり
        if ($winLines > 0) {
            $getMoney = $winLines * ($this->slotData->getBet() * $this->plugin->getConfig()->get("reduction_rate", 10));
            $this->economyData->addMoney($getMoney);
        }

        // You Lose
        if (!$jp) {
            $this->slotConfigData->addJp($this->slotData->getBet() * (count($this->payLines) - $winLines));

            if ($winLines < 1) {
                $this->player->sendMessage("[Casino] [{$this->slotConfigData->getName()}]" . TextFormat::YELLOW . " ハズレ");
            }
        }
    }
}