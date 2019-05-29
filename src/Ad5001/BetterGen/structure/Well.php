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

namespace Ad5001\BetterGen\structure;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\PopulatorObject;
use pocketmine\utils\Random;

class Well extends PopulatorObject{

	/** @var array */
	public $overridable = [
		Block::AIR        => true,
		Block::SAPLING    => true,
		Block::LEAVES     => true,
		Block::WOOD       => true,
		Block::DANDELION  => true,
		Block::POPPY      => true,
		Block::SNOW_LAYER => true,
		Block::LOG2       => true,
		Block::LEAVES2    => true,
		Block::CACTUS     => true
	];
	/** @var ChunkManager */
	protected $level;
	/** @var array */
	protected $directions = [
		[
			1,
			1
		],
		[
			1,
			-1
		],
		[
			-1,
			-1
		],
		[
			-1,
			1
		]
	];

	/**
	 * Checks if a Well is place-able
	 *
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param Random       $random
	 *
	 * @return bool
	 */
	public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z, Random $random): bool{
		$this->level = $level;
		for($xx = $x - 2; $xx <= $x + 2; $xx++){
			for($yy = $y; $yy <= $y + 3; $yy++){
				for($zz = $z - 2; $zz <= $z + 2; $zz++){
					if(!isset($this->overridable[$level->getBlockIdAt($xx, $yy, $zz)])){
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Places a well
	 *
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param Random       $random
	 * @return void
	 */
	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random): void{
		$this->level = $level;
		foreach($this->directions as $direction){
			// Building pillars
			for($yy = $y; $yy < $y + 3; $yy++){
				$this->placeBlock($x + $direction [0], $yy, $z + $direction [1], Block::SANDSTONE);
			}

			// Building corners
			$this->placeBlock($x + ($direction [0] * 2), $y, $z + $direction [1], Block::SANDSTONE);
			$this->placeBlock($x + $direction [0], $y, $z + ($direction [1] * 2), Block::SANDSTONE);
			$this->placeBlock($x + ($direction [0] * 2), $y, $z + ($direction [1] * 2), Block::SANDSTONE);

			// Building slabs on the sides. Places two times due to all directions.
			$this->placeBlock($x + ($direction [0] * 2), $y, $z, 44, 1);
			$this->placeBlock($x, $y, $z + ($direction [1] * 2), 44, 1);

			// Placing water.Places two times due to all directions.
			$this->placeBlock($x + $direction [0], $y, $z, Block::WATER);
			$this->placeBlock($x, $y, $z + $direction [1], Block::WATER);
		}

		// Final things
		for($xx = $x - 1; $xx <= $x + 1; $xx++){
			for($zz = $z - 1; $zz <= $z + 1; $zz++){
				$this->placeBlock($xx, $y + 3, $zz);
			}
		}
		$this->placeBlock($x, $y + 3, $z, Block::SANDSTONE, 1);
		$this->placeBlock($x, $y, $z, Block::WATER);
	}

	/**
	 * Places a block
	 *
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @param int $id
	 * @param int $meta
	 * @return void
	 */
	public function placeBlock(int $x, int $y, int $z, int $id = Block::AIR, int $meta = 0){
		$this->level->setBlockIdAt($x, $y, $z, $id);
		$this->level->setBlockDataAt($x, $y, $z, $meta);
	}
}