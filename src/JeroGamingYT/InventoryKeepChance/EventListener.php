<?php 

/*
 * InventoryKeepChance plugin for PocketMine-MP
 * Copyright (C) 2022 JeroGamingYT-pm-pl <https://github.com/JeroGamingYT-pm-pl/InventoryKeepChance>
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

namespace JeroGamingYT\InventoryKeepChance;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;

class EventListener implements Listener
{
	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
	}

	public function getMain(): Main
	{
		return $this->plugin;
	}

	public function PlayerDeath(PlayerDeathEvent $event)
	{
		$player = $event->getPlayer();
		if($this->getMain()->willKeep())
		{
			$event->setKeepInventory(true);
		}
	}
}