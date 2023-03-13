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

use Ad5001\BetterGen\utils\BuildingUtils;
use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;
use pocketmine\utils\Random;
use function intval;
use pocketmine\block\VanillaBlocks;

class RavinePopulator extends AmountPopulator{

	/** @var int */
	const NOISE = 250;
	/** @var ChunkManager */
	protected $level;

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
		if($amount > 50){ // Only build one per chunk
			$depth = $random->nextBoundedInt(60) + 30; // 2Much4U?
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $random->nextRange(5, $this->getHighestWorkableBlock($x, $z));
			$deffX = $x;
			$deffZ = $z;
			$height = $random->nextRange(15, 30);
			$length = $random->nextRange(5, 12);
			for($i = 0; $i < $depth; $i++){
				$this->buildRavinePart($x, $y, $z, $height, $length, $random);
				$diffX = $x - $deffX;
				$diffZ = $z - $deffZ;
				if($diffX > $length / 2){
					$diffX = $length / 2;
				}
				if($diffX < -$length / 2){
					$diffX = -$length / 2;
				}
				if($diffZ > $length / 2){
					$diffZ = $length / 2;
				}
				if($diffZ < -$length / 2){
					$diffZ = -$length / 2;
				}
				if($length > 10){
					$length = 10;
				}
				if($length < 5){
					$length = 5;
				}
				$x += $random->nextRange(intval(0 + $diffX), intval(2 + $diffX)) - 1;
				$y += $random->nextRange(0, 2) - 1;
				$z += $random->nextRange(intval(0 + $diffZ), intval(2 + $diffZ)) - 1;
				$height += $random->nextRange(0, 2) - 1;
				$length += $random->nextRange(0, 2) - 1;
			}
		}
	}

	/*
	 * Gets the top block (y) on an x and z axes
	 * @param $x int
	 * @param $z int
	 * @return int
	 */
	protected function getHighestWorkableBlock(int $x, int $z): int{
		for($y = World::Y_MAX - 1; $y > 0; --$y){
			$b = $this->level->getBlockAt($x, $y, $z)->getId();
			if($b === VanillaBlocks::DIRT() or $b === VanillaBlocks::GRASS() or $b === VanillaBlocks::PODZOL() or $b === VanillaBlocks::SAND() or $b === VanillaBlocks::SNOW() or $b === VanillaBlocks::SANDSTONE()){
				break;
			}elseif($b !== 0 and $b !== VanillaBlocks::SNOW_LAYER() and $b !== VanillaBlocks::WATER()){
				return -1;
			}
		}

		return ++$y;
	}

	/**
	 * Buidls a ravine part
	 *
	 * @param int    $x
	 * @param int    $y
	 * @param int    $z
	 * @param int    $height
	 * @param int    $length
	 * @param Random $random
	 * @return void
	 */
	protected function buildRavinePart(int $x, int $y, int $z, int $height, int $length, Random $random){
		$xBounded = 0;
		$zBounded = 0;
		for($xx = $x - $length; $xx <= $x + $length; $xx++){
			for($yy = $y; $yy <= $y + $height; $yy++){
				for($zz = $z - $length; $zz <= $z + $length; $zz++){
					$oldXB = $xBounded;
					$xBounded = $random->nextBoundedInt(self::NOISE * 2) - self::NOISE;
					$oldZB = $zBounded;
					$zBounded = $random->nextBoundedInt(self::NOISE * 2) - self::NOISE;
					if($xBounded > self::NOISE - 2){
						$xBounded = 1;
					}elseif($xBounded < -self::NOISE + 2){
						$xBounded = -1;
					}else{
						$xBounded = $oldXB;
					}
					if($zBounded > self::NOISE - 2){
						$zBounded = 1;
					}elseif($zBounded < -self::NOISE + 2){
						$zBounded = -1;
					}else{
						$zBounded = $oldZB;
					}
					if(abs((abs($xx) - abs($x)) ** 2 + (abs($zz) - abs($z)) ** 2) < ((($length / 2 - $xBounded) + ($length / 2 - $zBounded)) / 2) ** 2 && $y > 0 && !in_array($this->level->getBlockAt(( int) round($xx), (int) round($yy), (int) round($zz))->getId(), BuildingUtils::TO_NOT_OVERWRITE) && !in_array($this->level->getBlockAt(( int) round($xx), (int) round($yy + 1), (int) round($zz))->getId(), BuildingUtils::TO_NOT_OVERWRITE)){
                        if(!$x > 16 and !$z > 16) {
                            $this->level->setBlockAt((int)round($xx), (int)round($yy), (int)round($zz), VanillaBlocks::AIR());
                        }
					}
				}
			}
		}
	}
}