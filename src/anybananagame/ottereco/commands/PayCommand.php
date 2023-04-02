<?php
namespace anybananagame\ottereco\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use anybananagame\ottereco\Main;
use pocketmine\utils\Config;

class PayCommand extends Command implements PluginOwned
{

    use PluginOwnedTrait;
    private $con;
    public function __construct(Main $plugin)
    {
        $this->owningPlugin = $plugin;
        $this->con = $plugin;
        parent::__construct("pay", "Pay someone money", "/pay <player> <amount>");
        $this->setPermission("ottereco.command.pay");
        $this->setDescription("Pay someone else money");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if(!isset($args[0])) {
            $message = $this->con->getConfig()->get("nousername");
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $message = str_replace("{cmd}", "pay", $message);
            $sender->sendMessage($message);
            return true;
        } 
        if(!isset($args[1])) {
            $message = $this->con->getConfig()->get("noamount");
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $message = str_replace("{cmd}", "pay", $message);
            $sender->sendMessage($message);
            return true;
        }
        $db = new Config($this->con->getDataFolder() . "database.json", Config::JSON);      
        $receivertolow = strtolower($args[0]);
        $sendertolow = strtolower($sender->getName());


        $receiverdata = $db->get("$receivertolow");
        if(!$receiverdata){
            $message = $this->con->getConfig()->get("userdoesnotexist");
            $message = str_replace("{username}", $receivertolow, $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $sender->sendMessage($message);
            return true;
        }
        $receiversbal = $receiverdata["money"];

        $payerdata = $db->get($sendertolow);
        $payersbal = $payerdata["money"];

        if(!is_numeric($args[1])){
            $message = $this->con->getConfig()->get("NAN");
            $message = str_replace("{arg}", $args[1], $message);
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $sender->sendMessage($message);
            return true;
        }
        if($payersbal < $args[1]){
            $message = $this->con->getConfig()->get("notenoughmoney");
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

        $this->con->getEcoFunc()->takeMoney($sendertolow, $args[1]);
        $this->con->getEcoFunc()->addMoney($receivertolow, $args[1]);

        if($this->con->getServer()->getPlayerExact($receivertolow) != null){
            $player = $this->con->getServer()->getPlayerExact($receivertolow);
            $message = $this->con->getConfig()->get("getpaid");
            $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
            $message = str_replace("{payer}", $receivertolow, $message);
            $message = str_replace("{amount}", $args[1], $message);

            $player->sendMessage($message);
        }
        $message = $this->con->getConfig()->get("youpaid");
        $message = str_replace("{prefix}", $this->con->getConfig()->get("prefix"), $message);
        $message = str_replace("{amount}", $args[1], $message);
        $message = str_replace("{receiver}", $receivertolow, $message);
        $sender->sendMessage($message);




        

        return false;
    }


}