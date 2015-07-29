<?php

namespace _64FF00\xSudo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;
use pocketmine\OfflinePlayer;
use pocketmine\Server;

use pocketmine\utils\TextFormat;

class PCommand extends Command implements PluginIdentifiableCommand
{
	public function __construct(xSudo $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		parent::__construct($name, $description);
	}
	
	private function checkPermission(CommandSender $sender){
		if(!($sender->isOp() or $sender->hasPermission("pp.all"))){
			$sender->sendMessage(TextFormat::RED . "You don't have permission to use this command.");
			return false;
		}
		return true;
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!isset($args[0])){
			if($this->checkPermission($sender))$sender->sendMessage(TextFormat::RED . "/p <func>");
			return false;
		}
		if(!$this->checkPermission($sender)) return true;	
		switch($args[0]){
			case "b":
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$player = $this->plugin->getServer()->getOfflinePlayer($args[1]);
				}
				$player->setBanned(true);
				$sender->sendMessage("Banned ".$player->getName().".");
				break;
			case "p":
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$player = $this->plugin->getServer()->getOfflinePlayer($args[1]);
				}
				$player->setBanned(false);
				$sender->sendMessage("Pardoned ".$player->getName().".");
				break;
			case "bi":
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$player = $this->plugin->getServer()->getOfflinePlayer($args[1]);
				}
				$value = $player->getName();
				if(preg_match("/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/", $value)){
					$this->processIPBan($value, $sender);
					$sender->sendMessage("Banned IP ".$value." .");
				}else{
					if(($player = $sender->getServer()->getPlayer($value)) instanceof Player){
						$this->processIPBan($player->getAddress(), $sender);
						$sender->sendMessage("Banned IP ".$player->getAddress()." .");
					}else{
						$sender->sendMessage("Invalid IP or name.");
						return false;
					}
				}
				break;
			case "pi":
				if(preg_match("/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/", $args[0])){
					$sender->getServer()->getIPBans()->remove($args[1]);
					$sender->sendMessage("Pardoned IP ".$args[1]." .");
				}else{
					$sender->sendMessage("Invalid IP.");
				}
				break;
			case "wa":
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$player = $this->plugin->getServer()->getOfflinePlayer($args[1]);
				}
				$player->setWhitelisted(true);
				$sender->sendMessage("Added ".$player->getName()." in the whitelist.");
				break;
			case "wd":
			case "wr":
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$player = $this->plugin->getServer()->getOfflinePlayer($args[1]);
				}
				$player->setWhitelisted(false);
				$sender->sendMessage("Removed ".$player->getName()." from the whitelist.");
				break;
			case "o":
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$player = $this->plugin->getServer()->getOfflinePlayer($args[1]);
				}
				$player->setOp(true);
				$sender->sendMessage("Opped ".$player->getName().".");
				break;
			case "do":
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$player = $this->plugin->getServer()->getOfflinePlayer($args[1]);
				}
				$player->setOp(false);
				$sender->sendMessage("De-opped ".$player->getName().".");
				break;
			case "gm":
				$player=null;
				if(!isset($args[2])){
					$sender->sendMessage("Unknown game mode");
					return true;
				}
				$gameMode = Server::getGamemodeFromString($args[2]);
				if($gameMode === -1){
					$sender->sendMessage("Unknown game mode");
					return true;
				}
				if(isset($args[1])){
					$player = $this->plugin->getServer()->getPlayer($args[1]);
				}else{
					$player = $sender;
				}
				if(!($player instanceof Player)){
					$sender->sendMessage("The target not found.");
					return true;
				}
				if($player instanceof ConsoleCommandSender){
					$sender->sendMessage("The target is console.");
					return true;
				}
				$player->setGamemode($gameMode);
				break;
			default:
				$sender->sendMessage(TextFormat::GREEN . "/p <func> <player>");
				return false;
		}
		return true;
	}

	public function getPlugin(){
		return $this->plugin;
	}
	private function processIPBan($ip, CommandSender $sender){
		$sender->getServer()->getIPBans()->addBan($ip, "", null, $sender->getName());
		foreach($sender->getServer()->getOnlinePlayers() as $player){
			if($player->getAddress() === $ip){
				$player->kick($reason !== "" ? $reason : "IP banned.");
			}
		}
		$sender->getServer()->getNetwork()->blockAddress($ip, -1);
	}
}