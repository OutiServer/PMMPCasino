<?php

declare(strict_types=1);

namespace outiserver\casino\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use outiserver\casino\CasinoMain;
use outiserver\casino\forms\CreateSlotConfigForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateGachaSubCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("casino.creategacha.command");
        $this->registerArgument(0, new RawStringArgument("name", true));
        $this->registerArgument(1, new IntegerArgument("price", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (isset($args["name"]) and isset($args["price"])) {
            if (CasinoMain::getInstance()->getGachaDataManager()->getName($args["name"])) {
                $sender->sendMessage("[Casino] " . TextFormat::RED . "その名前は既に使用されています");
                return;
            }

            CasinoMain::getInstance()->getGachaDataManager()->create($args["name"], $args["price"]);
            $sender->sendMessage("[Casino]" . TextFormat::GREEN . "ガチャ {$args["name"]}を作成しました");
        }
        elseif ($sender instanceof Player) {
            (new CreateSlotConfigForm())->execute($sender);
        }
        else $this->sendUsage();
    }
}