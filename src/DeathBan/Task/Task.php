<?php

/**
 * Created by PhpStorm.
 * User: realt
 * Date: 1/28/2019
 * Time: 5:53 PM
 */

namespace DeathBan\Task;

use DeathBan\Main;
use pocketmine\scheduler\Task as T;

class Task extends T
{

    public $plugin;
    public $deathban;

    /**
     * Task constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        foreach($this->plugin->deathban->getAll() as $index => $time){
            $time--;
            $this->plugin->deathban->set($index, $time);
            $this->plugin->deathban->save();
            if($time < 1){
                $this->plugin->deathban->remove($index);
                $this->plugin->deathban->save();
            }
        }
    }
}