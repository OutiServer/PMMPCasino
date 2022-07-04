<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Forms;

use Ken_Cir\Casino\CasinoMain;
use Ken_Cir\EconomyCore\Forms\Base\BaseForm;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentInput;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use Ken_Cir\LibFormAPI\Utils\FormUtil;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateSlotForm implements BaseForm
{
    private string $worldName;

    private int $x;

    private int $y;

    private int $z;

    public function __construct(string $worldName, int $x, int $y, int $z)
    {
        $this->worldName = $worldName;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function execute(Player $player): void
    {
        new CustomForm($player,
        "[Casino] スロットの作成",
        [
            new ContentInput("スロット名", "slot_name"),
            new ContentInput("1ラインのベット額", "bet")
        ],
        function (Player $player, array $data): void {
            $plugin = CasinoMain::getInstance();

            if (!$data[0] or !$data[1]) {
                $player->sendMessage("[Casino] " . TextFormat::RED . "スロット名とベット額は入力必須項目です");
                $player->sendMessage("3秒後前のフォームに戻ります");
                FormUtil::backForm($plugin, [$this, "execute"], [$player]);
            }
        });
    }
}