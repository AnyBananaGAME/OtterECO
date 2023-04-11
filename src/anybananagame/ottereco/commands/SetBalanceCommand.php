<?php
namespace anybananagame\ottereco\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use anybananagame\ottereco\Main;
use pocketmine\utils\Config;

class SetBalanceCommand extends Command implements PluginOwned {
    use PluginOwnedTrait;
    private $con;
    
    public function __construct(Main $plugin) {
        $this->owningPlugin = $plugin;
        $this->con = $plugin;
        parent::__construct("setbalance", "Set someones balance", "/setbalance <player> <amount>");
        $this->setPermission("ottereco.command.setbal");
        $this->setDescription("Set Balance on user");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!isset($args[0])) {
            $message = $this->con->getConfig()->get("nousername");
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $message = str_replace("{cmd}", "setbalance", $message);
            $sender->sendMessage($message);
            return true;
        } 

        if (!isset($args[1])) {
            $message = $this->con->getConfig()->get("noamount");
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $message = str_replace("{cmd}", "setbalance", $message);
            $sender->sendMessage($message);
            return true;
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
        
        if (!is_numeric($args[1])){
            $message = $this->con->getConfig()->get("NAN");
            $message = str_replace("{arg}", $args[1], $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $sender->sendMessage($message);
             return false;
        }
        
        if ($args[1] < 0){
            $message = $this->con->getConfig()->get("belowzero");
            $message = str_replace("{arg}", $args[1], $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $sender->sendMessage($message);
            return true;
        }
        
       $this->con->getEcoFunc()->setMoney($lowuser, $args[1]);
       $message = $this->con->getConfig()->get("setbalance");
       $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
       $message = str_replace("{username}", $lowuser, $message);
       $message = str_replace("{balance}", $args[1], $message);

       $sender->sendMessage($message);
       return true;
    }
}
