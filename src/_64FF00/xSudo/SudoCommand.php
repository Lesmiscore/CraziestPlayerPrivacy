<?php

namespace _64FF00\xSudo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

use pocketmine\event\TranslationContainer;

class SudoCommand extends Command implements PluginIdentifiableCommand
{
	public function __construct(xSudo $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		parent::__construct($name, $description);
	}
	
	private function checkPermission(CommandSender $sender){
		if(!(array_key_exists(strtolower($sender->getName()),$this->plugin->sudoer) or $sender->hasPermission("pp.sudoer") or $this->isConsole($sender))){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED."%commands.generic.permission"));
			return false;
		}
		return true;
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!isset($args[0]))
		{
			if(!$this->checkPermission($sender)) return true;
			$sender->sendMessage(TextFormat::GREEN . "[PlayerPrivacy] Usage: /sudo <g|grant|de|d|deprivate> <player>");
			return false;
		}

		$player=$this->plugin->getServer()->getPlayer($args[1]);
		if($player==null){
			$player=$args[1];
		}else{
			$player=$player->getName();
		}
		switch($args[0]){
		case "g":
		case "grant":
			$this->plugin->sudoer=array_merge($this->plugin->sudoer,array(strtolower($player)=>0));
			$this->plugin->broadcastSudoer(TextFormat::RED.$player." has received a sudoer permission!");
			break;
		case "de":
		case "d":
		case "deprivate":
			$this->plugin->sudoer=array_diff_key($this->plugin->sudoer,array(strtolower($player)=>0));
			$this->plugin->broadcastSudoer(TextFormat::GREEN.$player." has deprivated a sudoer permission!");
			break;
		}
		return true;
	}

	public function getPlugin(){
		return $this->plugin;
	}
	
	public function isConsole($sender){
		return $sender instanceof ConsoleCommandSender;
	}
}