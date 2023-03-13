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
use Generator;
use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use function intval;
use pocketmine\block\VanillaBlocks;

class CavePopulator extends AmountPopulator{

	/** @var bool */
	const STOP     = false;
	/** @var bool */
	const CONTINUE = true;

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
		for($i = 0; $i < $amount; $i++){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $random->nextRange(10, $this->getHighestWorkableBlock($x, $z));
			// echo "Generating cave at $x, $y, $z." . PHP_EOL;
			$this->generateCave($x, $y, $z, $random);
		}
		// echo "Finished Populating chunk $chunkX, $chunkZ !" . PHP_EOL;
		// Filling water & lava sources randomly
		for($i = 0; $i < $random->nextBoundedInt(5) + 3; $i++){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $random->nextRange(10, $this->getHighestWorkableBlock($x, $z));
			if($level->getBlockAt($x, $y, $z)->getId() == VanillaBlocks::STONE()->getId() && ($level->getBlockAt($x + 1, $y, $z)->getId() == VanillaBlocks::AIR()->getId() || $level->getBlockAt($x - 1, $y, $z)->getId() == VanillaBlocks::AIR()->getId() || $level->getBlockAt($x, $y, $z + 1)->getId() == VanillaBlocks::AIR()->getId() || $level->getBlockAt($x, $y, $z - 1)->getId() == VanillaBlocks::AIR()->getId()) && $level->getBlockAt($x, $y - 1, $z)->getId() !== VanillaBlocks::AIR()->getId() && $level->getBlockAt($x, $y + 1, $z)->getId() !== VanillaBlocks::AIR()->getId()){
				if($y < 40 && $random->nextBoolean()){
					$level->setBlockAt($x, $y, $z, VanillaBlocks::LAVA());
				}else{
					$level->setBlockAt($x, $y, $z, VanillaBlocks::WATER());
				}
			}
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
			if($b === VanillaBlocks::DIRT()->getId() or $b === VanillaBlocks::GRASS()->getId() or $b === VanillaBlocks::PODZOL()->getId() or $b === VanillaBlocks::SAND()->getId() or $b === VanillaBlocks::SNOW()->getId() or $b === VanillaBlocks::SANDSTONE()->getId()){
				break;
			}elseif($b !== 0 and $b !== VanillaBlocks::SNOW_LAYER()->getId() and $b !== VanillaBlocks::WATER()->getId()){
				return -1;
			}
		}

		return ++$y;
	}

	/**
	 * Generates a cave
	 *
	 * @param int    $x
	 * @param int    $y
	 * @param int    $z
	 * @param Random $random
	 * @return void
	 */
	public function generateCave(int $x, int $y, int $z, Random $random): void{
		$generatedBranches = $random->nextBoundedInt(10) + 1;
		foreach($gen = $this->generateBranch($x, $y, $z, 5, 3, 5, $random) as $v3){
			$generatedBranches--;
			if($generatedBranches <= 0){
				$gen->send(self::STOP);
			}else{
				$gen->send(self::CONTINUE);
			}
		}
	}

	/**
	 * Generates a cave branch
	 *
	 * @param int    $x
	 * @param int    $y
	 * @param int    $z
	 * @param int    $length
	 * @param int    $height
	 * @param int    $depth
	 * @param Random $random
	 *
	 * @yield Vector3
	 * @return Generator|null
	 */
	public function generateBranch(int $x, int $y, int $z, int $length, int $height, int $depth, Random $random): ?Generator{
		if(!(yield new Vector3($x, $y, $z))){
			for($i = 0; $i <= 4; $i++){
				BuildingUtils::buildRandom($this->level, new Vector3($x, $y, $z), new Vector3($length - $i, $height - $i, $depth - $i), $random, VanillaBlocks::AIR());
				$x += intval((($random->nextBoundedInt(intval(30 * ($length / 10)) + 1)) / 10 - 2));
				$yP = $random->nextRange(-14, 14);

				if($yP > 12){
					$y++;
				}elseif($yP < -12){
					$y--;
				}

				$z += intval(($random->nextBoundedInt(intval(30 * ($depth / 10)) + 1) / 10 - 1));

				//x,y,z values are not being used anywhere...

				return;
			}
		}

		$repeat = $random->nextBoundedInt(25) + 15;

		while($repeat-- > 0){
			BuildingUtils::buildRandom($this->level, new Vector3($x, $y, $z), new Vector3($length, $height, $depth), $random, VanillaBlocks::AIR());
			$x += intval(($random->nextBoundedInt(intval(30 * ($length / 10)) + 1) / 10 - 2));
			$yP = $random->nextRange(-14, 14);

			if($yP > 12){
				$y++;
			}elseif($yP < -12){
				$y--;
			}

			$z += intval(($random->nextBoundedInt(intval(30 * ($depth / 10)) + 1) / 10 - 1));
			$height += $random->nextBoundedInt(3) - 1;
			$length += $random->nextBoundedInt(3) - 1;
			$depth += $random->nextBoundedInt(3) - 1;

			if($height < 3){
				$height = 3;
			}
			if($length < 3){
				$length = 3;
			}
			if($height < 3){
				$height = 3;
			}
			if($height < 7){
				$height = 7;
			}
			if($length < 7){
				$length = 7;
			}
			if($height < 7){
				$height = 7;
			}

			if($random->nextBoundedInt(10) == 0){
				foreach($generator = $this->generateBranch($x, $y, $z, $length, $height, $depth, $random) as $gen){
					if(!(yield $gen)){
						$generator->send(self::STOP);
					}
				}
			}
		}

		return;
	}
}