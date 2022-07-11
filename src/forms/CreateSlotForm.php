<?php

declare(strict_types=1);

namespace outiserver\casino\forms;

use outiserver\casino\CasinoMain;
use outiserver\casino\database\slot\SlotDataManager;
use outiserver\casino\database\slotconfig\SlotConfigData;
use outiserver\casino\database\slotconfig\SlotConfigDataManager;
use Ken_Cir\EconomyCore\Forms\Base\BaseForm;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentDropdown;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentInput;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use Ken_Cir\LibFormAPI\Utils\FormUtil;
use pocketmine\block\BaseSign;
use pocketmine\block\tile\Tile;
use pocketmine\block\utils\SignText;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class CreateSlotForm implements BaseForm
{
    private string $worldName;

    private int $x;

    private int $y;

    private int $z;

    private BaseSign $sign;

    public function __construct(string $worldName, int $x, int $y, int $z, BaseSign $sign)
    {
        $this->worldName = $worldName;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->sign = $sign;
    }

    public function execute(Player $player): void
    {
        $slotConfigs = SlotConfigDataManager::getInstance()->getAll(true);
        if (count($slotConfigs) < 1) {
            $player->sendMessage(TextFormat::RED . "スロットマネージャーが1つも作成されていません");
            return;
        }

        new CustomForm(
            $player,
        "[Casino] スロットの作成",
        [
            new ContentDropdown("スロットマネージャー", array_map(function (SlotConfigData $slotConfig) {
                return $slotConfig->getName();
            }, $slotConfigs)),
            new ContentInput("スロット名", "slot_name"),
            new ContentInput("1ラインのベット額", "bet")
        ],
        function (Player $player, array $data) use ($slotConfigs): void {
            $plugin = CasinoMain::getInstance();

            if (!$data[1] or !$data[2]) {
                $player->sendMessage("[Casino] " . TextFormat::RED . "スロット名とベット額は入力必須項目です");
                $player->sendMessage("3秒後前のフォームに戻ります");
                FormUtil::backForm($plugin, [$this, "execute"], [$player]);
            }

            SlotDataManager::getInstance()->create(1, $slotConfigs[$data[0]]->getId(), $data[1], $this->worldName, $this->x, $this->y, $this->z, (int)$data[2]);
            $this->sign->setText(new SignText(["[3x3 SLOT] $data[1]", "Bet数: $data[2]"]));

            $player->sendMessage("スロットを作成しました");
        });
    }
}