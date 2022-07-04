<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Commands;

use CortexPE\Commando\BaseCommand;
use Ken_Cir\Casino\Commands\SubCommands\CreateSlotManagerSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CasinoCommand extends BaseCommand
{
    protected function prepare(): void
    {
        $this->setPermission("casino.command");
        $this->registerSubCommand(new CreateSlotManagerSubCommand("createslotmanager", "スロットマネージャーを作成する", []));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::GREEN . "Casino Commands");
        foreach ($this->getSubCommands() as $subCommand) {
            if ($subCommand->testPermissionSilent($sender)) {
                $sender->sendMessage(TextFormat::GREEN . $subCommand->getUsageMessage());
            }
        }
    }
}