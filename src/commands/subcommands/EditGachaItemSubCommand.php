<?php

declare(strict_types=1);

namespace outiserver\casino\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;

class EditGachaItemSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("casino.editgachaitem.command");
        $this->registerArgument(0, new RawStringArgument("gachaName", true));
        $this->registerArgument(1, new IntegerArgument("itemId", true));
        $this->registerArgument(3, new IntegerArgument("rand", true));
        $this->registerArgument(4, new IntegerArgument("count", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (isset($args["gachaName"]) and isset($args["itemId"]) and isset($args["rand"]) and isset($args["count"])) {
            try{
                $item = StringToItemParser::getInstance()->parse($args["itemId"]);
            }
            catch(LegacyStringToItemParserException){
                $sender->sendMessage("[Casino] " . "不明なアイテムID $args[0]");
                return;
            }

            if ($args["count"] < 1) {
                $sender->sendMessage("");
            }
        }
    }
}