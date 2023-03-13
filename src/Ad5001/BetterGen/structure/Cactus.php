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
use pocketmine\world\ChunkManager;
use pocketmine\utils\Random;
use pocketmine\block\VanillaBlocks;

class Cactus{

	/** @var int */
	protected $totalHeight;

	/**
	 * Checks if a cactus is placeable
	 *
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param Random       $random
	 * @return bool
	 */
	public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z, Random $random): bool{
		$this->totalHeight = 1 + $random->nextBoundedInt(3);
		$below = $level->getBlockAt($x, $y - 1, $z)->getId();
		for($yy = $y; $yy <= $y + $this->totalHeight; $yy++){
			if($level->getBlockAt($x, $yy, $z) !== VanillaBlocks::AIR() || ($below !== VanillaBlocks::SAND() && $below !== VanillaBlocks::CACTUS()) || ($level->getBlockAt($x - 1, $yy, $z) !== VanillaBlocks::AIR() || $level->getBlockAt($x + 1, $yy, $z) !== VanillaBlocks::AIR() || $level->getBlockAt($x, $yy, $z - 1) !== VanillaBlocks::AIR() || $level->getBlockAt($x, $yy, $z + 1) !== VanillaBlocks::AIR())){
				return false;
			}
		}

		return true;
	}

	/**
	 * Places a cactus
	 *
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @return void
	 */
	public function placeObject(ChunkManager $level, int $x, int $y, int $z): void{
		for($yy = 0; $yy < $this->totalHeight; $yy++){
			if($level->getBlockIdAt($x, $y + $yy, $z) != Block::AIR){
				return;
			}
			$level->setBlockIdAt($x, $y + $yy, $z, Block::CACTUS);
		}
	}
}
