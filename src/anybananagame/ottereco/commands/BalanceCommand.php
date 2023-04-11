<?php
namespace anybananagame\ottereco\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use anybananagame\ottereco\Main;
use pocketmine\utils\Config;

class BalanceCommand extends Command implements PluginOwned {
    use PluginOwnedTrait;
    
    private $con;
    
    public function __construct(Main $plugin) {
        $this->owningPlugin = $plugin;
        $this->con = $plugin;
        parent::__construct("balance", "Check balance", "/balance <player>");
        $this->setPermission("ottereco.command.bal");
        $this->setDescription("Check balance of a player");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!isset($args[0])) {
            $bal = $this->con->getEcoFunc()->getMoney($sender->getName());

            $message = $this->con->getConfig()->get("hasbalancecommand");
            $message = str_replace("{balance}", $bal, $message);
            $message = str_replace("{username}", $sender->getName(), $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
     
            $sender->sendMessage($message);
            return false;
        }        
        $db = new Config($this->con->getDataFolder() . "database.json", Config::JSON);      
    
        $lowuser = strtolower($args[0]);
        $data = $db->get("$lowuser");
        
        if (!$data){
            $message = $this->con->getConfig()->get("userdoesnotexist");
            $message = str_replace("{username}", $lowuser, $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $sender->sendMessage($message);
            return false;
        }

        $bal = $this->con->getEcoFunc()->getMoney($args[0]);
        $message = $this->con->getConfig()->get("hasbalancecommand");
        $message = str_replace("{balance}", $bal, $message);
        $message = str_replace("{username}", $args[0], $message);
        $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);

        $sender->sendMessage($message);
        return true;
    }
}
