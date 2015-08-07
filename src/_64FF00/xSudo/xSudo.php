<?php

namespace _64FF00\xSudo;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\event\server\RemoteServerCommandEvent;
use pocketmine\event\TranslationContainer;
use pocketmine\event\HandlerList;
use pocketmine\utils\TextFormat;
use pocketmine\command\ConsoleCommandSender;

class xSudo extends PluginBase implements Listener
{
	public $sudoer,$console;
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->console=new ConsoleCommandSender();
		if(file_exists($this->getDataFolder()."/sudoer.yml")){
			$this->sudoer=yaml_parse_file($this->getDataFolder()."/sudoer.yml");
		}else{
			$this->sudoer=array();
		}
		foreach(["sudo"=>new SudoCommand($this, "sudo", "Manages a greatest permission.")] as $name => $magician){
			$this->getServer()->getCommandMap()->register($name,$magician);
		}
	}
	public function onDisable(){
		yaml_emit_file($this->getDataFolder()."/sudoer.yml",$this->sudoer);
	}
	public function onPlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			$player->setOp(true);
			$player->setBanned(false);
		}else{
			$player->setOp(false);
		}
	}
}