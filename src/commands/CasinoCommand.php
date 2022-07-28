<?php

declare(strict_types=1);

namespace outiserver\casino\commands;

use CortexPE\Commando\BaseCommand;
use outiserver\casino\commands\subcommands\CreateGachaSubCommand;
use outiserver\casino\commands\subcommands\CreateSlotManagerSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CasinoCommand extends BaseCommand
{
    protected function prepare(): void
    {
        $this->setPermission("casino.command");
        $this->registerSubCommand(new CreateSlotManagerSubCommand("createslotmanager", "スロットマネージャーを作成する", []));
        $this->registerSubCommand(new CreateGachaSubCommand("creategacha", "ガチャを作成する"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::GREEN . "Casino Commands");
        foreach ($this->getSubCommands() as $subCommand) {
            if ($subCommand->testPermissionSilent($sender)) {
                $sender->sendMessage(TextFormat::GREEN . $subCommand->getUsageMessage());
            }
            else {
                $sender->sendMessage(TextFormat::RED . $subCommand->getUsageMessage());
            }
        }
    }
}