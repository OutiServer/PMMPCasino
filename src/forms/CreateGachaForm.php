<?php

declare(strict_types=1);

namespace outiserver\casino\forms;

use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentInput;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use outiserver\casino\CasinoMain;
use outiserver\casino\database\gacha\GachaDataManager;
use outiserver\economycore\Forms\Base\BaseForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateGachaForm implements BaseForm
{
    public function execute(Player $player): void
    {
        (new CustomForm(CasinoMain::getInstance(),
        $player,
        "ガチャを作成",
        [
            new ContentInput("ガチャ名", "gacha_name"),
            new ContentInput("ガチャ1回分の値段", "gacha_price", inputType: ContentInput::TYPE_INT)
        ],
        function (Player $player, array $data) {
            GachaDataManager::getInstance()->create($data[0], (int)$data[1]);
            $player->sendMessage("[Casino]" . TextFormat::GREEN . "ガチャ {$data[0]}を作成しました");
        }));
    }
}