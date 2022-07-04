<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Commands\SubCommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use Ken_Cir\Casino\CasinoMain;
use Ken_Cir\Casino\Forms\CreateSlotConfigForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateSlotManagerSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("casino.createslotmanager.command");
        $this->registerArgument(0, new RawStringArgument("name", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $plugin = CasinoMain::getInstance();

        if (isset($args["name"])) {
            if ($plugin->getSlotConfigDataManager()->getName($args["name"])) {
                $sender->sendMessage("[Casino] " . TextFormat::RED . "その名前は既に使用されています");
                return;
            }

            $plugin->getSlotConfigDataManager()->create($args["name"], $plugin->getConfig()->get("default_slot_jp", 10000));
            $sender->sendMessage("[Casino]" . TextFormat::GREEN . "スロットマネージャー {$args["name"]}を作成しました");
        }
        elseif ($sender instanceof Player) {
            (new CreateSlotConfigForm())->execute($sender);
        }
        else $this->sendUsage();
    }
}