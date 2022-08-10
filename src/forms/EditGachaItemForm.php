<?php

declare(strict_types=1);

namespace outiserver\casino\forms;

use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentInput;
use Ken_Cir\LibFormAPI\FormContents\SimpleForm\SimpleFormButton;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use Ken_Cir\LibFormAPI\Forms\SimpleForm;
use outiserver\casino\CasinoMain;
use outiserver\casino\database\gacha\GachaData;
use outiserver\casino\database\gacha\GachaDataManager;
use outiserver\casino\database\gachaitem\GachaItemData;
use outiserver\casino\database\gachaitem\GachaItemDataManager;
use outiserver\economycore\Forms\Base\BaseForm;
use pocketmine\item\ItemFactory;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditGachaItemForm implements BaseForm
{
    public function execute(Player $player): void
    {
        $gachaDatas = GachaDataManager::getInstance()->getAll(true);
        $contents = [];
        $contents[] = new SimpleFormButton("ガチャを追加");
        foreach ($gachaDatas as $gachaData) {
            $contents[] = new SimpleFormButton("{$gachaData->getName()}");
        }

        $form = new SimpleForm(CasinoMain::getInstance(),
        $player,
        "編集するガチャ選択",
        "編集するガチャを選択",
           $contents,
        function (Player $player, int $data) use ($gachaDatas) {
            if ($data === 0) (new CreateGachaForm())->execute($player);
            else $this->editItems($player, $gachaDatas[$data - 1]);
        },
        function (Player $player) {
            CasinoMain::getInstance()->getStackFormManager()->deleteStack($player->getXuid());
        });

        CasinoMain::getInstance()->getStackFormManager()->addStackForm($player->getXuid(), "edit_gacha_form", $form);
    }

    public function editItems(Player $player, GachaData $gachaData): void
    {
        $gachaItemDatas = GachaItemDataManager::getInstance()->getGachaId($gachaData->getId(), true);
        $contents = [];
        $contents[] = new SimpleFormButton("アイテムを追加");
        foreach ($gachaItemDatas as $gachaItemData) {
            $item = ItemFactory::getInstance()->get($gachaItemData->getId(), $gachaItemData->getItemMeta());
            $contents[] = new SimpleFormButton("{$item->getName()}");
        }

        (new SimpleForm(CasinoMain::getInstance(),
            $player,
            "ガチャ内のアイテムを編集",
            "",
            $contents,
        function (Player $player, int $data) use ($gachaItemDatas, $gachaData) {
            if ($data === 0) {
                $this->createItem($player, $gachaData);
            }
            else {
                $this->editItem($player, $gachaData, $gachaItemDatas[$data - 1]);
            }
        },
            function (Player $player) {
                CasinoMain::getInstance()->getStackFormManager()->deleteStack($player->getXuid());
            }));
    }

    public function createItem(Player $player, GachaData $gachaData): void
    {
        (new CustomForm(CasinoMain::getInstance(),
        $player,
        "ガチャアイテム追加",
        [
            new ContentInput("アイテムID", "item_id", inputType: ContentInput::TYPE_INT),
            new ContentInput("アイテムMeta", "item_meta", inputType: ContentInput::TYPE_INT),
            new ContentInput("確率", "rand", inputType: ContentInput::TYPE_INT),
            new ContentInput("封入個数", "count", inputType: ContentInput::TYPE_INT),
        ],
        function (Player $player, array $data) use ($gachaData) {
            $item = ItemFactory::getInstance()->get((int)$data[0], (int)$data[1]);
            // 存在しないアイテムでも処理されてしまうので注意
            GachaItemDataManager::getInstance()->create($gachaData->getId(), (int)$data[0], (int)$data[1], (int)$data[2], (int)$data[3]);
            $player->sendMessage(TextFormat::GREEN . "ガチャ{$gachaData->getName()}にアイテム{$item->getName()}を追加しました");
        },
            function (Player $player) {
                CasinoMain::getInstance()->getStackFormManager()->getStackFormEnd($player->getXuid())->reSend();
            }));
    }

    public function editItem(Player $player, GachaData $gachaData, GachaItemData $gachaItemData): void
    {
        (new CustomForm(CasinoMain::getInstance(),
        $player,
        "ガチャアイテム編集",
        [
            new ContentInput("確率", "rand", (string)$gachaItemData->getRand(), inputType: ContentInput::TYPE_INT),
            new ContentInput("封入個数", "count", (string)$gachaItemData->getCount(), inputType: ContentInput::TYPE_INT),
        ],
        function (Player $player, array $data) use ($gachaItemData) {
            $gachaItemData->setRand((int)$data[0]);
            $gachaItemData->setCount((int)$data[1]);

            $player->sendMessage(TextFormat::GREEN . "更新しました");
            CasinoMain::getInstance()->getStackFormManager()->getStackFormEnd($player->getXuid())->reSend();
        },
            function (Player $player) {
                CasinoMain::getInstance()->getStackFormManager()->getStackFormEnd($player->getXuid())->reSend();
            }));
    }
}