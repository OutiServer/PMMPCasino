<?php

declare(strict_types=1);

namespace outiserver\casino\forms;

use outiserver\casino\CasinoMain;
use outiserver\economycore\Forms\Base\BaseForm;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentInput;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use Ken_Cir\LibFormAPI\Utils\FormUtil;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateSlotConfigForm implements BaseForm
{
    public function execute(Player $player): void
    {
        new CustomForm(CasinoMain::getInstance(),
            $player,
        "[Casino] スロットマネージャーの作成",
        [
            new ContentInput("マネージャー名", "name")
        ],
        function (Player $player, array $data): void {
            $plugin = CasinoMain::getInstance();

            if (!$data[0]) {
                $player->sendMessage("[Casino] " . TextFormat::RED . "マネージャー名は入力必須項目です");
                $player->sendMessage("3秒後前のフォームに戻ります");
                FormUtil::backForm($plugin, [$this, "execute"], [$player]);
                return;
            }

            if ($plugin->getSlotConfigDataManager()->getName($data[0])) {
                $player->sendMessage("[Casino] " . TextFormat::RED . "その名前は既に使用されています");
                $player->sendMessage("3秒後前のフォームに戻ります");
                FormUtil::backForm($plugin, [$this, "execute"], [$player]);
                return;
            }

            $plugin->getSlotConfigDataManager()->create($data[0], $plugin->getConfig()->get("default_slot_jp", 10000));
            $player->sendMessage("[Casino]" . TextFormat::GREEN . "スロットマネージャー $data[0]を作成しました");
        });
    }
}