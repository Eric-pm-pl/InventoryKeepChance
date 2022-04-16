<?php 

/*
 * InventoryKeepChance plugin for PocketMine-MP
 * Copyright (C) 2022 David-pm-pl <https://github.com/David-pm-pl/InventoryKeepChance>
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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;

class EventListener implements Listener
{
	/**@Var Main plugin*/
	public Main $plugin;
	
	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
	}

	public function getMain(): Main
	{
		return $this->plugin;
	}
	/**
	 * @param  Player $player
	 * @return void
	 */
	public function sendMessage(Player $player): void{
		$message = $this->getMain()->getConfig()->get("msg-after-dealth");
		switch ($this->getMain()->getConfig()->get("msg-type")) {
			case "message":
				$player->sendMessage($message);
				break;
			case "title":
				$player->sendTitle($message);
				break;
			case "popup":
				$player->sendPopup($message);
				break;
			case "tip":
				$player->sendTip($message);
				break;
			case "actionbar":
				$player->sendActionBarMessage($message);
				break;
		}
	}
	/**
	 * @param PlayerDeathEvent $event
	 * @return void
	 */
	public function PlayerDeath(PlayerDeathEvent $event): void
	{
		$player = $event->getPlayer();
		$worldName = $event->getPlayer()->getWorld()->getDisplayName();
		$worlds = $this->getMain()->getConfig()->get("worlds");
		if($this->getMain()->willKeep())
		{
			switch ($this->getMain()->getConfig()->get("mode")) {
				case "all":
					$event->setKeepInventory(true);
					$this->sendMessage($player);
					break;
				case "whitelist":
					if (in_array($worldName, $worlds)) {
						$event->setKeepInventory(true);
						$this->sendMessage($player);
					}
					break;
				case "blacklist":
					if (!in_array($worldName, $worlds)) {
						$event->setKeepInventory(true);
						$this->sendMessage($player);
					}
					break;
			}
		}
	}
}
