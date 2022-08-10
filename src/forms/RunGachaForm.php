<?php

declare(strict_types=1);

namespace outiserver\casino\forms;

use Ken_Cir\LibFormAPI\FormContents\SimpleForm\SimpleFormButton;
use Ken_Cir\LibFormAPI\Forms\SimpleForm;
use outiserver\casino\CasinoMain;
use outiserver\casino\database\gacha\GachaDataManager;
use outiserver\casino\games\GachaGame;
use outiserver\economycore\Forms\Base\BaseForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RunGachaForm implements BaseForm
{
    public function execute(Player $player): void
    {
        $gachaDatas = GachaDataManager::getInstance()->getAll(true);
        if (count($gachaDatas) < 1) {
            $player->sendMessage(TextFormat::RED . "[Casino] 現在プレイ可能なガチャはありません");
            return;
        }

        $contents = [];
        foreach ($gachaDatas as $gachaData) {
            $contents[] = new SimpleFormButton("{$gachaData->getName()} {$gachaData->getPrice()}円/回");
        }

        (new SimpleForm(CasinoMain::getInstance(),
        $player,
        "[Casino] ガチャをプレイ",
        "プレイするガチャを選択してください",
        $contents,
        function (Player $player, int $data) use ($gachaDatas) {
            (new GachaGame($player, CasinoMain::getInstance(), $gachaDatas[$data]))->run();
        }));
    }
}