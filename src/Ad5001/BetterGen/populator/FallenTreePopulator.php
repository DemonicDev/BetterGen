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

use Ad5001\BetterGen\structure\FallenTree;
use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;
use pocketmine\utils\Random;
use pocketmine\block\VanillaBlocks;

class FallenTreePopulator extends AmountPopulator{

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

		$this->setBaseAmount(1);
		$this->setRandomAmount(2);
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
		$tree = TreePopulator::$types[$this->type];

		$fallenTree = new FallenTree(
			new $tree()
		);

		for($i = 0; $i < $amount; $i++){
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if(isset(FallenTree::$overridable[$level->getBlockAt($x, $y, $z)->getId()])){
				$y--;
			} // Changing $y if 1 block to high.
			if($y !== -1 and $fallenTree->canPlaceObject($level, $x, $y + 1, $z, $random)){
				$fallenTree->placeObject($level, $x, $y + 1, $z);
			}
		}
	}

	/**
	 * Gets the top block (y) on an x and z axes
	 *
	 * @param $x
	 * @param $z
	 * @return int
	 */
	protected function getHighestWorkableBlock(int $x, int $z): int{
		for($y = World::Y_MAX - 1; $y > 0; --$y){
			$b = $this->level->getBlockAt($x, $y, $z)->getId();
			if($b === VanillaBlocks::DIRT() or $b === VanillaBlocks::GRASS()){
				break;
			}elseif($b !== VanillaBlocks::AIR() and $b !== VanillaBlocks::SNOW_LAYER()){
				return -1;
			}
		}

		return ++$y;
	}
}