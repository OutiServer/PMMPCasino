<?php

declare(strict_types=1);

namespace outiserver\casino\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use outiserver\casino\forms\CreateSlotConfigForm;
use outiserver\casino\forms\EditGachaItemForm;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditGachaItemSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("casino.editgachaitem.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            (new EditGachaItemForm())->execute($sender);
        }
    }
}