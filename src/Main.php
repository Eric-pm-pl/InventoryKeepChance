<?php

/*
 * InventoryKeepChance plugin for PocketMine-MP
 * Copyright (C) 2022 Taylor-pm-pl <https://github.com/Taylor-pm-pl/InventoryKeepChance>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

declare(strict_types=1);

namespace davidglitch04\InventoryKeepChance;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{
    /**@Var Config $config*/
    public Config $config;

    public function onEnable(): void
    {
        $this->getServer()
            ->getPluginManager()
            ->registerEvents(new EventListener($this), $this);
        $this->saveResource("config.yml");
        $this->config = new Config(
            $this->getDataFolder() . "config.yml",
            Config::YAML
        );
        $this->checkUpdate();
    }

    public function checkUpdate(bool $isRetry = false): void
    {
        $this->getServer()
            ->getAsyncPool()
            ->submitTask(
                new CheckUpdateTask(
                    $this->getDescription()->getName(),
                    $this->getDescription()->getVersion()
                )
            );
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() !== "ikc") {
            return true;
        }

        $prefix = str_replace("&", "§", (string) $this->config->get("prefix"));

        if (!$sender instanceof Player) {
            $sender->sendMessage($prefix . $this->getMessage("message.useingame"));
            return false;
        }

        if (!$sender->hasPermission("inventorykeepchance.perm")) {
            $sender->sendMessage($prefix . $this->getMessage("message.permission"));
            return false;
        }

        if (!isset($args[0])) {
            $sender->sendMessage($prefix . $this->getMessage("message.usage"));
            return false;
        }

        if ($args[0] === "reload") {
            $this->reload();
            $sender->sendMessage($prefix . $this->getMessage("message.reload"));
            return false;
        }

        if ($args[0] !== "reload" && is_numeric($args[0]) && $args[0] >= 0 && $args[0] <= 100) {
            $this->config->set("chance", $args[0]);
            $this->config->save();
            $sender->sendMessage($prefix . $this->getMessage("message.success"));
            return false;
        }

        $sender->sendMessage($prefix . $this->getMessage("message.maximum"));
        return false;
    }

    public function reload(): void
    {
        $this->config->reload();
        $this->saveDefaultConfig();
        $this->config->save();
    }

    public function willKeep(): bool
    {
        $percentage = mt_rand(1, 100);
        $chance = $this->config->get("chance");
        if ($percentage <= $chance) {
            return true;
        } else {
            return false;
        }
    }

    public function getMessage(string $msg): string
    {
        $msg = $this->config->getNested($msg);
        return TextFormat::colorize($msg);
    }
}
