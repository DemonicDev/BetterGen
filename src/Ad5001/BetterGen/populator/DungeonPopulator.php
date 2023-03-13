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

use Ad5001\BetterGen\structure\Dungeons;
use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;
use pocketmine\utils\Random;
use pocketmine\block\VanillaBlocks;

class DungeonPopulator extends AmountPopulator{

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
		if($amount == 5){ // 1 out of 10 chunks
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $random->nextRange(10, $this->getHighestWorkableBlock($x, $z) - 6);
			$d = new Dungeons();
			$d->placeObject($level, $x, $y, $z, $random);
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
			if($b === VanillaBlocks::DIRT() or $b === VanillaBlocks::GRASS() or $b === VanillaBlocks::PODZOL() or $b === VanillaBlocks::SAND() or $b === VanillaBlocks::SNOW() or $b === VanillaBlocks::SANDSTONE()){
				break;
			}elseif($b !== 0 and $b !== VanillaBlocks::SNOW_LAYER() and $b !== VanillaBlocks::WATER()){
				return -1;
			}
		}

		return ++$y;
	}
}