<?php

namespace _64FF00\xSudo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

class CCCommand extends Command implements PluginIdentifiableCommand
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
		if(!isset($args[0]) or !isset($args[1]) or !isset($args[2]))
		{
			if(!$this->checkPermission($sender)) return true;
			$sender->sendMessage(TextFormat::GREEN . "[PlayerPrivacy] Usage: /cc <add|remove> <player> <dest>");
			return false;
		}
		$player=$this->plugin->getServer()->getPlayer($args[1]);
		if($player==null){
			$player=$args[1];
		}else{
			$player=$player->getName();
		}
		$player=strtolower($player);
		
		$dest=$this->plugin->getServer()->getPlayer($args[2]);
		if($dest==null){
			$dest=$args[2];
		}else{
			$dest=$dest->getName();
		}
		$dest=strtolower($dest);
		
		if(!isset($this->plugin->commandCapture[$player])){
			$this->plugin->commandCapture=array_merge($this->plugin->commandCapture,array($player=>array()));
		}
		switch($args[0]){
			case "add":
				$this->plugin->commandCapture[$player]=array_merge($this->plugin->commandCapture[$player],array($dest=>1));
				$sender->sendMessage(TextFormat::GREEN."Regeistered redirect request from ".$player." to ".$dest.".");
				break;
			case "remove":
				$this->plugin->commandCapture[$player]=array_diff_key($this->plugin->commandCapture[$player],array($dest=>1));
				$sender->sendMessage(TextFormat::GREEN."Unregeistered redirect request from ".$player." to ".$dest.".");
				break;
		}
		return true;
	}

	public function getPlugin(){
		return $this->plugin;
	}
}