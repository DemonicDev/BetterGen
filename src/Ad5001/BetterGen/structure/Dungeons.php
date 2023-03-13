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

use Ad5001\BetterGen\utils\BuildingUtils;
use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\block\VanillaBlocks;

class Dungeons{

	/** @var array */
	public $overridable = [
        0 => true,
		467 => true,
		78 => true,
		162 => true
	];

	/** @var int */
	protected $height;

	/**
	 * Places a bush
	 *
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param Random       $random
	 * @return void
	 */
	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random){
		$xDepth = 3 + $random->nextBoundedInt(3);
		$zDepth = 3 + $random->nextBoundedInt(3);

		// echo "Building dungeon at $x, $y, $z\n";
		// Building walls
		list($pos1, $pos2) = BuildingUtils::minmax(new Vector3($x + $xDepth, $y, $z + $zDepth), new Vector3($x - $xDepth, $y + 5, $z - $zDepth));

		for($y = $pos1->y; $y >= $pos2->y; $y--){
			for($x = $pos1->x; $x >= $pos2->x; $x--){
				for($z = $pos1->z; $z >= $pos2->z; $z--){ // Cleaning the area first
					$level->setBlockAt($x, $y, $z,  VanillaBlocks::AIR());
				}
				// Starting random walls.
				if($random->nextBoolean()){
					$level->setBlockAt($x, $y, $pos1->z, VanillaBlocks::MOSSY_COBBLESTONE());
				}else{
					$level->setBlockAt($x, $y, $pos1->z, VanillaBlocks::COBBLESTONE());
				}
				if($random->nextBoolean()){
					$level->setBlockAt($x, $y, $pos2->z, VanillaBlocks::MOSSY_COBBLESTONE());
				}else{
					$level->setBlockAt($x, $y, $pos2->z, VanillaBlocks::COBBLESTONE());
				}
			}
			for($z = $pos1->z; $z >= $pos2->z; $z--){
				if($random->nextBoolean()){
					$level->setBlockAt($pos1->x, $y, $z, VanillaBlocks::MOSSY_COBBLESTONE());
				}else{
					$level->setBlockAt($pos1->x, $y, $z, VanillaBlocks::COBBLESTONE());
				}
				if($random->nextBoolean()){
					$level->setBlockAt($pos2->x, $y, $z, VanillaBlocks::MOSSY_COBBLESTONE());
				}else{
					$level->setBlockAt($pos2->x, $y, $z, VanillaBlocks::COBBLESTONE());
				}
			}
		}
		// Bottom & top
		for($x = $pos1->x; $x >= $pos2->x; $x--){
			for($z = $pos1->z; $z >= $pos2->z; $z--){
				if($random->nextBoolean()){
					$level->setBlockAt($x, $pos1->y, $z, VanillaBlocks::MOSSY_COBBLESTONE());
				}else{
					$level->setBlockAt($x, $pos1->y, $z, VanillaBlocks::COBBLESTONE());
				}
				if($random->nextBoolean()){
					$level->setBlockAt($x, $pos2->y, $z, VanillaBlocks::MOSSY_COBBLESTONE());
				}else{
					$level->setBlockAt($x, $pos2->y, $z, VanillaBlocks::COBBLESTONE());
				}
			}
		}

		// Setting the spawner TODO: Add chest loot
		$level->setBlockAt($x + 5, $y + 2, $z + 5, VanillaBlocks::MOB_SPAWNER());
	}
}
