<?php
declare(strict_types = 1);

/**
 *  ____             __     __                    ____
 * /\  _`\          /\ \__ /\ \__                /\  _`\
 * \ \ \L\ \     __ \ \ ,_\\ \ ,_\     __   _ __ \ \ \L\_\     __     ___
 *  \ \  _ <'  /'__`\\ \ \/ \ \ \/   /'__`\/\`'__\\ \ \L_L   /'__`\ /' _ `\
 *   \ \ \L\ \/\  __/ \ \ \_ \ \ \_ /\  __/\ \ \/  \ \ \/, \/\  __/ /\ \/\ \
 *    \ \____/\ \____\ \ \__\ \ \__\\ \____\\ \_\   \ \____/\ \____\\ \_\ \_\
 *     \/___/  \/____/  \/__/  \/__/ \/____/ \/_/    \/___/  \/____/ \/_/\/_/
 *
 * Tomorrow's pocketmine generator.
 *
 * @author   Ad5001 <mail@ad5001.eu>, XenialDan <https://github.com/thebigsmileXD>
 * @link     https://github.com/Ad5001/BetterGen
 * @category World Generator
 */

namespace Ad5001\BetterGen;

use Ad5001\BetterGen\biome\BetterForest;
use Ad5001\BetterGen\generator\BetterNormal;
use Ad5001\BetterGen\loot\LootTable;
use Ad5001\BetterGen\structure\Temple;
use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\level\ChunkPopulateEvent;
use pocketmine\event\Listener;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\utils\Config;
use pocketmine\utils\Random;
use function intval;
use function mkdir;

class Main extends PluginBase implements Listener{

	/** @var string */
	public const PREFIX = "§l§o§b[§r§l§2Better§aGen§o§b]§r§f ";

	/** @var int */
	public const SAKURA_FOREST = 100;

	/**
	 * Registers a biome to betternormal
	 *
	 * @param int   $id
	 * @param Biome $biome
	 * @return void
	 */
	public static function registerBiome(int $id, Biome $biome): void{
		//todo figure out what the id will do... or maybe remove it?
		BetterNormal::registerBiome($biome);
	}

	/**
	 * Save the resources first before enabling the plugin.
	 */
	public function onLoad(): void{
		@mkdir($this->getDataFolder(). "loots");
		$this->saveResource("loots/igloo.json");
		$this->saveResource("loots/mineshaft.json");
		$this->saveResource("loots/temple.json");
		$this->saveResource("processingLoots.json");
	}

	/**
	 * Called when the plugin enables
	 *
	 * @return void
	 */
	public function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		GeneratorManager::addGenerator(BetterNormal::class, "betternormal");
	}

	/**
	 * Called when a command executes
	 *
	 * @param CommandSender $sender
	 * @param Command       $cmd
	 * @param int           $label
	 * @param array         $args
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args): bool{
		switch($cmd->getName()){
			case "createworld": // /createworld <name> [generator = betternormal] [seed = rand()] [options(json)]
				switch(count($args)){
					case 0 :
						return false;
					break;
					case 1 : // /createworld <name>
						$name = $args[0];
						$generator = GeneratorManager::getGenerator("betternormal");
						$generatorName = "betternormal";
						$seed = $this->generateRandomSeed();
						$options = [];
					break;
					case 2 : // /createworld <name> [generator = betternormal]
						$name = $args[0];
						$generator = GeneratorManager::getGenerator($args[1]);
						if(GeneratorManager::getGeneratorName($generator) !== strtolower($args[1])){
							$sender->sendMessage(self::PREFIX . "§4Could not find generator {$args[1]}. Are you sure it is registered?");

							return true;
						}
						$generatorName = strtolower($args[1]);
						$seed = $this->generateRandomSeed();
						$options = [];
					break;
					case 3 : // /createworld <name> [generator = betternormal] [seed = rand()]
						$name = $args[0];
						$generator = GeneratorManager::getGenerator($args[1]);
						if(GeneratorManager::getGeneratorName($generator) !== strtolower($args[1])){
							$sender->sendMessage(self::PREFIX . "§4Could not find generator {$args[1]}. Are you sure it is registered?");

							return true;
						}
						$generatorName = strtolower($args[1]);
						$parts = str_split($args[2]);
						foreach($parts as $key => $str){
							if(is_numeric($str) == false && $str <> '-'){
								$parts[$key] = ord($str);
							}
						}
						$seed = (int) implode("", $parts);
						$options = [];
					break;
					default : // /createworld <name> [generator = betternormal] [seed = rand()] [options(json)]
						$name = $args[0];
						$generator = GeneratorManager::getGenerator($args[1]);
						if(GeneratorManager::getGeneratorName($generator) !== strtolower($args[1])){
							$sender->sendMessage(self::PREFIX . "§4Could not find generator {$args[1]}. Are you sure it is registered?");

							return true;
						}
						$generatorName = strtolower($args[1]);
						if($args[2] == "rand"){
							$args[2] = $this->generateRandomSeed();
						}
						$parts = str_split($args[2]);
						foreach($parts as $key => $str){
							if(is_numeric($str) == false && $str <> '-'){
								$parts[$key] = ord($str);
							}
						}
						$seed = (int) implode("", $parts);
						unset($args[0], $args[1], $args[2]);
						$options = json_decode($args[3], true);
						if(!is_array($options)){
							$sender->sendMessage(Main::PREFIX . "§4Invalid JSON for options.");

							return true;
						}
					break;
				}
				$options["preset"] = json_encode($options);
				if((int) $seed == 0/*String*/){
					$seed = $this->generateRandomSeed();
				}
				$this->getServer()->broadcastMessage(Main::PREFIX . "§aGenerating level $name with generator $generatorName and seed $seed..");
				$this->getServer()->generateLevel($name, $seed, $generator, $options);
				$this->getServer()->loadLevel($name);

				return true;
			break;

			case "worldtp":
				if(!$sender instanceof Player){
					return false;
				}

				if(isset($args[0])){
					if(is_null($this->getServer()->getLevelByName($args[0]))){
						$this->getServer()->loadLevel($args[0]);
						if(is_null($this->getServer()->getLevelByName($args[0]))){
							$sender->sendMessage("Could not find level {$args[0]}.");

							return false;
						}
					}
					$sender->teleport(Position::fromObject($sender, $this->getServer()->getLevelByName($args[0])));
					$sender->sendMessage("§aTeleporting to {$args[0]}...");

					return true;
				}else{
					return false;
				}
			break;

			case 'temple':
				{
					if($sender instanceof ConsoleCommandSender){
						return false;
					}
					/** @var Player $sender */
					$temple = new Temple();
					$temple->placeObject($sender->getLevel(), $sender->x, $sender->y, $sender->z, new Random(microtime()));

					return true;
				}
		}

		return false;
	}

	/**
	 * Generates a (semi) random seed.
	 *
	 * @return int
	 */
	public function generateRandomSeed(): int{
		return intval(rand(0, intval(time() / memory_get_usage(true) * (int) str_shuffle("127469453645108") / (int) str_shuffle("12746945364"))));
	}

	/**
	 * Registers a forest from a tree class
	 *
	 * @param string $name
	 * @param string $treeClass
	 * @param array  $infos
	 * @return bool
	 */
	public function registerForest(string $name, string $treeClass, array $infos): bool{
		if(!@class_exists($treeClass)){
			return false;
		}
		if(!@is_subclass_of($treeClass, "pocketmine\\level\\generator\\normal\\object\\Tree")){
			return false;
		}
		if(count($infos) < 2 or !is_float($infos[0]) or !is_float($infos[1])){
			return false;
		}

		return BetterForest::registerForest($name, $treeClass, $infos);
	}

	/**
	 * Checks when a chunk populates to populate chests back
	 *
	 * @param ChunkPopulateEvent $event
	 * @return void
	 */
	public function onChunkPopulate(ChunkPopulateEvent $event){
		$cfg = new Config(LootTable::getPluginFolder() . "processingLoots.json", Config::JSON);
		foreach($cfg->getAll() as $key => $value){
			list($x, $y, $z) = explode(";", $key);

			$x = intval($x);
			$y = intval($y);
			$z = intval($z);

			if($value["saveAs"] == "chest" && $event->getLevel()->getBlockIdAt($x, $y, $z) == Block::AIR){
				$event->getLevel()->setBlockIdAt($x, $y, $z, Block::CHEST);
			}else{
				$cfg->remove($key);
				$cfg->save();
			}
		}
	}

	/**
	 * Loads chest tiles on chest blocks when a chunk is loaded
	 *
	 * @param ChunkLoadEvent $event
	 */
	public function onChunkLoad(ChunkLoadEvent $event){
		if($event->getLevel()->getProvider()->getGenerator() === "betternormal"){
			$chunk = $event->getChunk();
			for($x = 0; $x < 16; $x++){
				for($z = 0; $z < 16; $z++){
					for($y = 0; $y <= Level::Y_MAX; $y++){
						$id = $chunk->getBlockId($x, $y, $z);
						$tile = $chunk->getTile($x, $y, $z);
						if($id === Block::CHEST and $tile === null){
							Tile::createTile(Tile::CHEST, $event->getLevel(), Chest::createNBT($pos = new Vector3($chunk->getX() * 16 + $x, $y, $chunk->getZ() * 16 + $z), null)); //TODO: set face correctly
						}
					}
				}
			}
		}
	}
}