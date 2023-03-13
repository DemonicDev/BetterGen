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
use pocketmine\world\biome\Biome;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;
use pocketmine\utils\Random;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\block\VanillaBlocks;
class DeadbushPopulator extends AmountPopulator{

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
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			if(!in_array($level->getChunk($chunkX, $chunkZ)->getBiomeId(abs($x % 16), ($z % 16)), [
				40,
				39,
				BiomeIds::DESERT
			])
			){
				continue;
			}
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y !== -1 && $level->getBlockAt($x, $y - 1, $z) == VanillaBlocks::SAND()){
                if(!$x > 16 and !$z > 16) {
                    $level->setBlockAt($x, $y, $z, Block::DEAD_BUSH());
                }
				#$level->setBlockDataAt($x, $y, $z, 1);
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
			if($b === 3 or $b === 2 or $b === 12 or $b === 24 or $b === 159 or $b === 172){
				break;
			}elseif($b !== 0){
				return -1;
			}
		}

		return ++$y;
	}
}