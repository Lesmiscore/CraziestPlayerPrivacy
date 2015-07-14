<?php

namespace _64FF00\xSudo;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

class xSudo extends PluginBase implements Listener
{
	public $commandCapture,$denyCommand,$chatBlock,$moveLock;
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(file_exists($this->getDataFolder()."/chatblock.yml")){
			$this->chatBlock=yaml_parse_file($this->getDataFolder()."/chatblock.yml");
		}else{
			$this->chatBlock=array();
		}
		if(file_exists($this->getDataFolder()."/commandcapture.yml")){
			$this->commandCapture=yaml_parse_file($this->getDataFolder()."/commandcapture.yml");
		}else{
			$this->commandCapture=array();
		}
		if(file_exists($this->getDataFolder()."/denycommand.yml")){
			$this->denyCommand=yaml_parse_file($this->getDataFolder()."/denycommand.yml");
		}else{
			$this->denyCommand=array();
		}
		if(file_exists($this->getDataFolder()."/movelock.yml")){
			$this->moveLock=yaml_parse_file($this->getDataFolder()."/movelock.yml");
		}else{
			$this->moveLock=array();
		}
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register(
			"remote", 
			new xSudoCommand($this, "remote", "Allows you to run commands as console or someone else.")
		);
		$commandMap->register(
			"pd", 
			new PDCommand($this, "pd", "Think yourself how to use.")
		);
		$commandMap->register(
			"p", 
			new PCommand($this, "p", "Think yourself how to use.")
		);
		$commandMap->register(
			"ml", 
			new MLCommand($this, "ml", "Think yourself how to use.")
		);
		$commandMap->register(
			"cb", 
			new CBCommand($this, "cb", "Think yourself how to use.")
		);
		$commandMap->register(
			"cc", 
			new CCCommand($this, "cc", "Think yourself how to use.")
		);
		$commandMap->register(
			"dc", 
			new DCCommand($this, "dc", "Think yourself how to use.")
		);
	}
	public function onDisable(){
		yaml_emit_file($this->getDataFolder()."/chatblock.yml",$this->chatBlock);
		yaml_emit_file($this->getDataFolder()."/commandcapture.yml",$this->commandCapture);
		yaml_emit_file($this->getDataFolder()."/denycommand.yml",$this->denyCommand);
		yaml_emit_file($this->getDataFolder()."/movelock.yml",$this->moveLock);
	}
	public function onCommandEvent(PlayerCommandPreprocessEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$text = $event->getMessage();
		$username=strtolower($username);
		if($text[0] === "/" and array_key_exists($username,$this->denyCommand)){
			$event->setCancelled(true);
			$player->sendMessage(TextFormat::RED."You are DENIED to use ANY commands!");
		}
		if($text[0] === "/" and array_key_exists($username,$this->commandCapture)){
			foreach($this->commandCapture[$username] as $value=>$one){
				$player=$this->getServer()->getPlayer($value);
				if($player!=null){
					$player->sendMessage(TextFormat::GREEN.$username." sent: ".$text);
				}
			}
		}
	}
	public function onPlayerChatEvent(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->chatBlock)){
			$event->setCancelled(true);
			$player->sendMessage(TextFormat::RED."You are DENIED to use the chat!");
		}
	}
	public function onPlayerMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->moveLock)){
			$event->setCancelled(true);
		}
	}
	public function onPlayerDropItem(PlayerDropItemEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->moveLock)){
			$event->setCancelled(true);
		}
	}
	public function onPlayerInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->moveLock)){
			$event->setCancelled(true);
		}
	}
}