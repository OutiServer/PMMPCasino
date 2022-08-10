<?php

declare(strict_types=1);

namespace outiserver\casino\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use outiserver\casino\forms\RunGachaForm;
use outiserver\casino\games\GachaGame;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RunGachaSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("casino.editgachaitem.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            (new RunGachaForm())->execute($sender);
        }

    }
}