<?php
namespace anybananagame\ottereco;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use anybananagame\ottereco\commands\BalanceCommand;
use anybananagame\ottereco\commands\SetBalanceCommand;
use anybananagame\ottereco\commands\AddBalanceCommand;
use anybananagame\ottereco\commands\PayCommand;

class Main extends PluginBase implements Listener{
    private static self $instance;
    private EcoFunctions $EcoFunc;

    public static function getInstance(): Main {
        return self::$instance;
    }
    
    public function onLoad(): void {
        self::$instance = $this;
    }

    public function onEnable(): void { 
        $this->saveDefaultConfig();

	$this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->EcoFunc = new EcoFunctions($this);

        $this->getServer()->getCommandMap()->registerAll("ottereco", [ new BalanceCommand($this), new SetBalanceCommand($this), new AddBalanceCommand($this), new PayCommand($this) ]);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $username = strtolower($player->getName());
        $path = $this->getDataFolder(). "database.json";

        $db = new Config($this->getDataFolder() . "database.json", Config::JSON);

        $lowuser = strtolower($username);
        $data = $db->get("$lowuser");
        
	if (!$data) {
            $player->sendMessage("Database created!");
            $db->setDefaults([
                "$username" => [
                    "money" => 1000
                ]
            ]);           
            $db->save(); 
        }
    }

    public function onBreak(BlockBreakEvent $event) {
            $player = $event->getPlayer();
    }

    public function getEcoFunc(): ? EcoFunctions {
		return $this->EcoFunc ?? null;
    }
}
