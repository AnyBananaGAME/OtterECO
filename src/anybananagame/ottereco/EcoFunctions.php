<?php

namespace anybananagame\ottereco;

use pocketmine\player\Player;
use pocketmine\utils\Config;


class EcoFunctions
{
    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }


    /** @param username */
    public function getMoney($usern)
    {
        $player = $this->plugin->getServer()->getPlayerExact($usern);
        if ($player == null) {
            $player = $this->plugin->getServer()->getOfflinePlayer($usern);
            $username = strtolower($player->getName());

            $this->plugin->getServer()->getLogger()->warning("> $username");
            $db = new Config($this->plugin->getDataFolder() . "database.json", Config::JSON);
            $data = $db->get("$username");
            $bal = $data["money"];
            return $bal;
        }
        $username = strtolower($player->getName());
        $db = new Config($this->plugin->getDataFolder() . "database.json", Config::JSON);
        $data = $db->get("$username");
        $bal = $data["money"];
        return $bal;
    }
    public function setMoney($usern, $amount)
    {
        $player = $this->plugin->getServer()->getOfflinePlayer($usern);
        $username = strtolower($player->getName());

        $this->plugin->getServer()->getLogger()->warning("> $username");
        $db = new Config($this->plugin->getDataFolder() . "database.json", Config::JSON);
        $data = $db->get("$username");
        $bal = $data["money"];

        $db->setNested("$username.money", $amount);
        return $db->save();
    }
    public function addMoney($usern, $amount)
    {
        $player = $this->plugin->getServer()->getOfflinePlayer($usern);
        $username = strtolower($player->getName());

        $this->plugin->getServer()->getLogger()->warning("> $username");
        $db = new Config($this->plugin->getDataFolder() . "database.json", Config::JSON);
        $data = $db->get("$username");
        $bal = $data["money"];

        $db->setNested("$username.money", $bal + $amount);
        return $db->save();
    }
    public function takeMoney($usern, $amount)
    {
        $player = $this->plugin->getServer()->getOfflinePlayer($usern);
        $username = strtolower($player->getName());

        $this->plugin->getServer()->getLogger()->warning("> $username");
        $db = new Config($this->plugin->getDataFolder() . "database.json", Config::JSON);
        $data = $db->get("$username");
        $bal = $data["money"];

        $db->setNested("$username.money", $bal - $amount);
        return $db->save();
    }



    public function getConfig(){
        return $this->getConfig();
    }
}
