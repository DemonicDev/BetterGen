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

namespace Ad5001\BetterGen\populator;

use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\Tree;
use pocketmine\world\World;
use pocketmine\utils\Random;

class TreePopulator extends AmountPopulator{

	/** @var Tree[] */
	static $types = [
		"pocketmine\\world\\generator\\object\\OakTree",
		"pocketmine\\world\\generator\\object\\BirchTree",
		"Ad5001\\BetterGen\\structure\\SakuraTree"
	];

	/** @var ChunkManager */
	protected $level;
	/** @var int */
	protected $type;

	/**
	 * Constructs the class
	 *
	 * @param int $type
	 */
	public function __construct(int $type = 0){
		$this->type = $type;
	}

	/**
	 * Populates the chunk
	 *
	 * @param ChunkManager $level
	 * @param int          $chunkX
	 * @param int          $chunkZ
	 * @param Random       $random
	 * @return void
	 */
	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void{
		$this->level = $level;
		$amount = $this->getAmount($random);
		for($i = 0; $i < $amount; $i++){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y === -1){
				continue;
			}
			$treeC = self::$types[$this->type];
			/** @var Tree $tree */
			$tree = new $treeC();
			$tree->placeObject($level, $x, $y, $z, $random);
		}
	}

	/**
	 * Gets the top block (y) on an x and z axes
	 *
	 * @param int $x
	 * @param int $z
	 *
	 * @return int
	 */
	protected function getHighestWorkableBlock(int $x, int $z): int{
		for($y = World::Y_MAX - 1; $y > 0; --$y){
			$b = $this->level->getBlockAt($x, $y, $z)->getId();
			if($b === VanillaBlocks::DIRT() or $b === VanillaBlocks::GRASS() or $b === VanillaBlocks::PODZOL()){
				break;
			}elseif($b !== 0 and $b !== VanillaBlocks::SNOW_LAYER()){
				return -1;
			}
		}

		return ++$y;
	}
}