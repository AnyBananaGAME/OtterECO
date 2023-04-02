<?php
namespace anybananagame\ottereco\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use anybananagame\ottereco\Main;
use pocketmine\utils\Config;

class AddBalanceCommand extends Command implements PluginOwned
{

    use PluginOwnedTrait;
    private $con;
    public function __construct(Main $plugin)
    {
        $this->owningPlugin = $plugin;
        $this->con = $plugin;
        parent::__construct("addbalance", "Add someone more money", "/addbalance <player> <amount>");
        $this->setPermission("ottereco.command.setbal");
        $this->setDescription("add money to someone");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if(!isset($args[0])) {
            $message = $this->con->getConfig()->get("nousername");
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $message = str_replace("{cmd}", "addbalance", $message);

            $sender->sendMessage($message);
            return true;
        } 
        if(!isset($args[1])) {
            $message = $this->con->getConfig()->get("noamount");
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $message = str_replace("{cmd}", "addbalance", $message);

            $sender->sendMessage($message);
            return true;
        }               
        
        $db = new Config($this->con->getDataFolder() . "database.json", Config::JSON);      
    
        $lowuser = strtolower($args[0]);
        $data = $db->get("$lowuser");
        if(!$data){
            $message = $this->con->getConfig()->get("userdoesnotexist");
            $message = str_replace("{username}", $lowuser, $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);

            $sender->sendMessage($message);
            return true;
        }
        if(!is_numeric($args[1])){
            $message = $this->con->getConfig()->get("NAN");
            $message = str_replace("{arg}", $args[1], $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $sender->sendMessage($message);
            return true;
        }
        if($args[1] < 0){
            $message = $this->con->getConfig()->get("belowzero");
            $message = str_replace("{arg}", $args[1], $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $sender->sendMessage($message);
            return true;
        }

        $bal = $data["money"];
        $totalbal = $bal+$args[1];


       $this->con->getEcoFunc()->addMoney($lowuser, $args[1]);

       $message = $this->con->getConfig()->get("adduserbal");
       $message = str_replace("{username}", $lowuser, $message);
       $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
       $message = str_replace("{bal}", $args[1], $message);
       $message = str_replace("{totalbal}", $totalbal, $message);
       $sender->sendMessage($message);

        return false;
    }


}