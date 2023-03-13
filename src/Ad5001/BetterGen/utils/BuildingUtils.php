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

namespace Ad5001\BetterGen\utils;

use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use function in_array;
use function intval;
use pocketmine\block\VanillaBlocks;
class BuildingUtils{

	const TO_NOT_OVERWRITE = [
        9,
        11,
        7,
        81,
        5
	];

	/**
	 * Fills an area
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos1
	 * @param Vector3      $pos2
	 * @param Block        $block
	 * @return void
	 */
	public static function fill(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block = null): void{
		if($block == null){
			$block = VanillaBlocks::AIR();
		}

		list($pos1, $pos2) = self::minmax($pos1, $pos2);

		for($x = $pos1->x; $x >= $pos2->x; $x--){
			for($y = $pos1->y; $y >= $pos2->y; $y--){
				for($z = $pos1->z; $z >= $pos2->z; $z--){
					$x = intval($x);
					$y = intval($y);
					$z = intval($z);
                    if(!$x > 16 and !$z > 16) {
                        $level->setBlockAt($x, $y, $z, $block);
                    }
					#$level->setBlockDataAt($x, $y, $z, $block->getDamage());
				}
			}
		}
	}

	/**
	 * Returns two Vector three, the biggest and lowest ones based on two provided vectors
	 *
	 * @param Vector3 $pos1
	 * @param Vector3 $pos2
	 * @return array
	 */
	public static function minmax(Vector3 $pos1, Vector3 $pos2): array{
		$v1 = new Vector3(max($pos1->getFloorX(), $pos2->getFloorX()), max($pos1->getFloorY(), $pos2->getFloorY()), max($pos1->getFloorZ(), $pos2->getFloorZ()));
		$v2 = new Vector3(min($pos1->getFloorX(), $pos2->getFloorX()), min($pos1->getFloorY(), $pos2->getFloorY()), min($pos1->getFloorZ(), $pos2->getFloorZ()));

		return [
			$v1, $v2
		];
	}

	/**
	 * Fills an area randomly
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos1
	 * @param Vector3      $pos2
	 * @param Block        $block
	 * @param Random       $random
	 * @param int          $randMax
	 * @return void
	 */
	public static function fillRandom(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block = null, Random $random = null, $randMax = 3): void{
		if($block == null){
			$block = Block::get(Block::AIR);
		}

		list($pos1, $pos2) = self::minmax($pos1, $pos2);

		for($x = $pos1->x; $x >= $pos2->x; $x--){
			for($y = $pos1->y; $y >= $pos2->y; $y--){
				for($z = $pos1->z; $z >= $pos2->z; $z--){
					if($random !== null ? $random->nextBoundedInt($randMax) == 0 : rand(0, $randMax) == 0){
						$x = intval($x);
						$y = intval($y);
						$z = intval($z);

						$level->setBlockIdAt($x, $y, $z, $block->getId());
						$level->setBlockDataAt($x, $y, $z, $block->getDamage());
					}
				}
			}
		}
	}

	/**
	 * Custom area filling
	 *
	 * @param Vector3  $pos1
	 * @param Vector3  $pos2
	 * @param callable $call
	 * @param array    $params
	 * @return array
	 */
	public static function fillCallback(Vector3 $pos1, Vector3 $pos2, callable $call, ...$params): array{
		list($pos1, $pos2) = self::minmax($pos1, $pos2);

		$return = [];

		for($x = $pos1->x; $x >= $pos2->x; $x--){
			for($y = $pos1->y; $y >= $pos2->y; $y--){
				for($z = $pos1->z; $z >= $pos2->z; $z--){
					$x = intval($x);
					$y = intval($y);
					$z = intval($z);

					$return[] = call_user_func($call, new Vector3($x, $y, $z), ...$params);
				}
			}
		}

		return $return;
	}

	/**
	 * Creates walls
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos1
	 * @param Vector3      $pos2
	 * @param Block        $block
	 * @return void
	 */
	public static function walls(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block): void{
		list($pos1, $pos2) = self::minmax($pos1, $pos2);

		for($y = $pos1->y; $y >= $pos2->y; $y--){
			for($x = $pos1->x; $x >= $pos2->x; $x--){
				$x = intval($x);
				$y = intval($y);
				$z1 = intval($pos1->z);
				$z2 = intval($pos2->z);

				$level->setBlockIdAt($x, $y, $z1, $block->getId());
				$level->setBlockDataAt($x, $y, $z1, $block->getDamage());
				$level->setBlockIdAt($x, $y, $z2, $block->getId());
				$level->setBlockDataAt($x, $y, $z2, $block->getDamage());
			}

			for($z = $pos1->z; $z >= $pos2->z; $z--){
				$x1 = intval($pos1->z);
				$x2 = intval($pos2->z);
				$y = intval($y);
				$z = intval($z);

				$level->setBlockIdAt($x1, $y, $z, $block->getId());
				$level->setBlockDataAt($x1, $y, $z, $block->getDamage());
				$level->setBlockIdAt($x2, $y, $z, $block->getId());
				$level->setBlockDataAt($x2, $y, $z, $block->getDamage());
			}
		}
	}

	/**
	 * Creates the top of a structure
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos1
	 * @param Vector3      $pos2
	 * @param Block        $block
	 * @return void
	 */
	public static function top(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block): void{
		list($pos1, $pos2) = self::minmax($pos1, $pos2);

		for($x = $pos1->x; $x >= $pos2->x; $x--){
			for($z = $pos1->z; $z >= $pos2->z; $z--){
				$x = intval($x);
				$y = intval($pos1->y);
				$z = intval($z);

				$level->setBlockIdAt($x, $y, $z, $block->getId());
				$level->setBlockDataAt($x, $y, $z, $block->getDamage());
			}
		}
	}

	/**
	 * Creates the corners of the structures. Used for mineshaft "towers"
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos1
	 * @param Vector3      $pos2
	 * @param Block        $block
	 * @return void
	 */
	public static function corners(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block): void{
		list($pos1, $pos2) = self::minmax($pos1, $pos2);

		for($y = $pos1->getFloorY(); $y >= $pos2->getFloorY(); $y--){
			$level->setBlockIdAt($pos1->getFloorX(), $y, $pos1->getFloorZ(), $block->getId());
			$level->setBlockDataAt($pos1->getFloorX(), $y, $pos1->getFloorZ(), $block->getDamage());
			$level->setBlockIdAt($pos2->getFloorX(), $y, $pos1->getFloorZ(), $block->getId());
			$level->setBlockDataAt($pos2->getFloorX(), $y, $pos1->getFloorZ(), $block->getDamage());
			$level->setBlockIdAt($pos1->getFloorX(), $y, $pos2->getFloorZ(), $block->getId());
			$level->setBlockDataAt($pos1->getFloorX(), $y, $pos2->getFloorZ(), $block->getDamage());
			$level->setBlockIdAt($pos2->getFloorX(), $y, $pos2->getFloorZ(), $block->getId());
			$level->setBlockDataAt($pos2->getFloorX(), $y, $pos2->getFloorZ(), $block->getDamage());
		}
	}

	/**
	 * Fills the bottom of a structure
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos1
	 * @param Vector3      $pos2
	 * @param Block        $block
	 * @return void
	 */
	public static function bottom(ChunkManager $level, Vector3 $pos1, Vector3 $pos2, Block $block): void{
		list($pos1, $pos2) = self::minmax($pos1, $pos2);

		for($x = $pos1->getFloorX(); $x >= $pos2->getFloorX(); $x--){
			for($z = $pos1->getFloorZ(); $z >= $pos2->getFloorZ(); $z--){
				$level->setBlockIdAt($x, $pos2->getFloorY(), $z, $block->getId());
				$level->setBlockDataAt($x, $pos2->getFloorY(), $z, $block->getDamage());
			}
		}
	}

	/**
	 * Builds a structure randomly based on a circle algorithm. Used in caves and lakes.
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos
	 * @param Vector3      $infos
	 * @param Random       $random
	 * @param Block        $block
	 * @return void
	 */
	public static function buildRandom(ChunkManager $level, Vector3 $pos, Vector3 $infos, Random $random, block $block): void{
		$xBounded = $random->nextBoundedInt(3) - 1;
		$yBounded = $random->nextBoundedInt(3) - 1;
		$zBounded = $random->nextBoundedInt(3) - 1;

		$pos = $pos->round();

		for($x = $pos->x - ($infos->x / 2); $x <= $pos->x + ($infos->x / 2); $x++){
			for($y = $pos->y - ($infos->y / 2); $y <= $pos->y + ($infos->y / 2); $y++){
				for($z = $pos->z - ($infos->z / 2); $z <= $pos->z + ($infos->z / 2); $z++){
					// if(abs((abs($x) - abs($pos->x)) ** 2 +($y - $pos->y) ** 2 +(abs($z) - abs($pos->z)) ** 2) <(abs($infos->x / 2 + $xBounded) + abs($infos->y / 2 + $yBounded) + abs($infos->z / 2 + $zBounded)) ** 2
					if(abs((abs($x) - abs($pos->x)) ** 2 + ($y - $pos->y) ** 2 + (abs($z) - abs($pos->z)) ** 2) < ((($infos->x / 2 - $xBounded) + ($infos->y / 2 - $yBounded) + ($infos->z / 2 - $zBounded)) / 3) ** 2 && $y > 0){
						$x = intval($x);
						$y = intval($y);
						$z = intval($z);

						if(!in_array($level->getBlockAt($x, $y, $z)->getId(), self::TO_NOT_OVERWRITE) && !in_array($level->getBlockAt($x, $y + 1, $z)->getId(), self::TO_NOT_OVERWRITE)){
							if(!$x > 16 and !$z > 16) {
                                $level->setBlockAt($x, $y, $z, $block);
                            }
							//$level->setBlockDataAt($x, $y, $z, $block->getDamage());
						}
					}
				}
			}
		}
	}
}
