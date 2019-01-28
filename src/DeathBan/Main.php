<?php

/**
 * Created by PhpStorm.
 * User: realt
 * Date: 1/28/2019
 * Time: 5:53 PM
 */

namespace DeathBan;

use DeathBan\Task\Task;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerDeathEvent,PlayerPreLoginEvent};
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{Config,TextFormat};

class Main extends PluginBase implements Listener
{
    public $deathban;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("DeathBan enabled!");

        $this->deathban = new Config($this->getDataFolder() . "deathban.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new Task($this), 1 * 20);
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        $this->addBan($player);
    }

    /**
     * @param PlayerPreLoginEvent $event
     */
    public function onLogin(PlayerPreLoginEvent $event): void
    {
        $player = $event->getPlayer();
        foreach($this->deathban->getAll() as $index => $time) {
            $hours = floor($time / 3600);
            $minutes = floor(($time / 60) % 60);
            $seconds = $time % 60;
            if ($this->deathban->exists($player->getName())) {
                $player->close("", TextFormat::RED . "You are Death Banned for another: " . $hours . ":" . $minutes . ":" .$seconds);
            }
        }
    }

    /**
     * @param Player $player
     */
    public function addBan(Player $player): void
    {
        if ($player->hasPermission("deathban")){
            $this->deathban->set($player->getName(), 1800);
            $this->deathban->save();
            $player->kick(TextFormat::RED . "You have been Death Banned for 30 minutes.");
        }else {
            $this->deathban->set($player->getName(), 3600);
            $this->deathban->save();
            $player->kick(TextFormat::RED . "You have been Death Banned for 1 Hour.");
        }
    }

    public function onDisable(): void
    {
        $this->getLogger()->info("DeathBan disabled!");
    }
}
