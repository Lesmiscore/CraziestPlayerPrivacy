<?php

namespace _64FF00\xSudo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

class xSudoCommand extends Command implements PluginIdentifiableCommand
{
	private $enable = false;
	public function __construct(xSudo $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		parent::__construct($name, $description);
	}
	
	private function checkPermission(CommandSender $sender){
		if($this->enable){
			return true;
		}
		if(!($sender->isOp() or $sender->hasPermission("pp.all"))){
			$sender->sendMessage(TextFormat::RED . "You don't have permission to use this command.");
			return false;
		}
		return true;
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!isset($args[0]))
		{
			if(!$this->checkPermission($sender)) return true;
			$sender->sendMessage(TextFormat::GREEN . "[PlayerPrivacy] Usage: /remote <console / info / username> <COMMAND>");
			return false;
		}

		switch($args[0]){
			case "console":
				if(!$this->checkPermission($sender)) break;
				if(!isset($args[1]))
				{
					$sender->sendMessage(TextFormat::GREEN . "[PlayerPrivacy] Usage: /remote console <COMMAND>");
					return false;
				}
				$console = new ConsoleCommandSender();
				$command = substr(strtolower($args[1]), 0, 1) == "/" ? substr(strtolower($args[1]), 1) : strtolower($args[1]);
				$arguments = array_diff($args, [$args[0], $args[1]]);
				$this->plugin->getServer()->dispatchCommand($console, $command . " " . implode(" ", $arguments));
				break;
			case "info":
				if(!$this->checkPermission($sender)) break;
				$sender->sendMessage(TextFormat::GREEN."[PlayerPrivacy] <-- PlayerPrivacy v".$this->plugin->getDescription()->getVersion()." by " . $this->plugin->getDescription()->getAuthors()[0] . "! -->");
				break;
			/*case "e":
				$this->enable=true;
				break;
			case "d":
				$this->enable=false;
				break;*/
			default:
				if(!$this->checkPermission($sender)) break;
				if(!isset($args[1]))
				{
					$sender->sendMessage(TextFormat::GREEN . "[PlayerPrivacy] Usage: /remote ".$args[0]." <COMMAND>");
					return false;
				}
				$player = $this->plugin->getServer()->getPlayer($args[0]);
				$command = substr(strtolower($args[1]), 0, 1) == "/" ? substr(strtolower($args[1]), 1) : strtolower($args[1]);
				if(!$player instanceof Player){
					$sender->sendMessage(TextFormat::RED . "[PlayerPrivacy] [ERROR] Player ".$args[0]." not found.");
					return false;
				}
				$arguments = array_diff($args, [$args[0], $args[1]]);
				$this->plugin->getServer()->dispatchCommand($player, $command." ".implode(" ", $arguments));
				break;
		}
		return true;
	}

	public function getPlugin(){
		return $this->plugin;
	}
}