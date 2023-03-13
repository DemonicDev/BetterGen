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

use Ad5001\BetterGen\generator\BetterNormal;
use pocketmine\block\Block;
use pocketmine\block\CoalOre;
use pocketmine\block\DiamondOre;
use pocketmine\block\GoldOre;
use pocketmine\block\IronOre;
use pocketmine\block\LapisOre;
use pocketmine\block\RedstoneOre;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Ore;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use function intval;
use pocketmine\block\VanillaBlocks;
class FloatingIslandPopulator extends AmountPopulator{

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
		if($this->getAmount($random) > 130){
			$x = $random->nextRange(($chunkX << 4), ($chunkX << 4) + 15);
			$z = $random->nextRange(($chunkX << 4), ($chunkX << 4) + 15);
			$y = $random->nextRange($this->getHighestWorkableBlock($x, $z) < 96 ? $this->getHighestWorkableBlock($x, $z) + 20 : $this->getHighestWorkableBlock($x, $z), 126);
			$radius = $random->nextRange(5, 8);
			$height = $this->buildIslandBottomShape($level, new Vector3($x, $y, $z), $radius, $random);
			$this->populateOres($level, new Vector3($x, $y - 1, $z), $radius * 2, $height, $random);
			$chunk = $level->getChunk($chunkX, $chunkZ);
			$biome = BetterNormal::$biomeById[$chunk->getBiomeId($x % 16, $z % 16)];
			$populators = $biome->getPopulators();
			foreach($populators as $populator){
				$populator->populate($level, $chunkX, $chunkZ, $random);
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
			if($b === VanillaBlocks::DIRT() or $b === VanillaBlocks::GRASS() or $b === VanillaBlocks::PODZOL() or $b === VanillaBlocks::SAND()){
				break;
			}elseif($b !== 0 and $b !== VanillaBlocks::SNOW_LAYER()){
				return 90;
			}
		}

		return ++$y;
	}

	/**
	 * Builds an island bottom shape
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos
	 * @param int          $radius
	 * @param Random       $random
	 * @return int Bottom place of the island
	 */
	public function buildIslandBottomShape(ChunkManager $level, Vector3 $pos, int $radius, Random $random): int{
		$pos = $pos->round();
		$currentLen = 1;
		$current = 0;
		for($y = $pos->y - 1; $radius > 0; $y--){
			for($x = $pos->x - $radius; $x <= $pos->x + $radius; $x++){
				for($z = $pos->z - $radius; $z <= $pos->z + $radius; $z++){
					if(abs(abs($x - $pos->x) ** 2) + abs(abs($z - $pos->z) ** 2) == ($radius ** 2) * 0.67){
						$isEdge = true;
					}else{
						$isEdge = false;
					}
					if(abs(abs($x - $pos->x) ** 2) + abs(abs($z - $pos->z) ** 2) <= ($radius ** 2) * 0.67 && $y < 128){
						if($chunk = $level->getChunk($x >> 4, $z >> 4)){
							$biome = BetterNormal::$biomeById[$chunk->getBiomeId($x % 16, $z % 16)];
							$block = $biome->getGroundCover()[$pos->y - $y - 1] ?? VanillaBlocks::STONE();
							$block = $block->getId();
						}elseif($random->nextBoundedInt(5) == 0 && $isEdge){
							$block = VanillaBlocks::AIR();
						}else{
							$block = VanillaBlocks::STONE();
						}
                        if(!$x > 16 and !$z > 16) {
                            $level->setBlockAt($x, $y, $z, $block ?? VanillaBlocks::STONE())->getId();
                        }
					}
				}
			}
			$current++;
			$hBound = $random->nextFloat();
			if($current >= $currentLen + $hBound){
				if($radius == 0){
					return $pos->y;
				}
				$current = 0;
				$currentLen += 0.3 * ($random->nextFloat() + 0.5);
				$radius--;
			}
		}

		return $pos->y - 1 - $y;
	}

	/**
	 * Populates the island with ores
	 *
	 * @param ChunkManager $level
	 * @param Vector3      $pos
	 * @param int          $width
	 * @param int          $height
	 * @param Random       $random
	 * @return void
	 */
	public function populateOres(ChunkManager $level, Vector3 $pos, int $width, int $height, Random $random): void{
		$ores = new Ore();
        $stone = VanillaBlocks::STONE();
		$ores->setOreTypes([
			new OreType(VanillaBlocks::COAL_ORE(), $stone, 20, 16, $pos->y - $height, $pos->y),
			new OreType(VanillaBlocks::IRON_ORE(), $stone, 20, 8, $pos->y - $height, $pos->y - intval($height * 0.75)),
			new OreType(VanillaBlocks::REDSTONE_ORE(), $stone, 8, 7, $pos->y - $height, $pos->y - intval($height / 2)),
			new OreType(VanillaBlocks::LAPIS_LAZULI_ORE(), $stone, 1, 6, $pos->y - $height, $pos->y - intval($height / 2)),
			new OreType(VanillaBlocks::GOLD_ORE(), $stone, 2, 8, $pos->y - $height, $pos->y - intval($height / 2)),
			new OreType(VanillaBlocks::DIAMOND_ORE(), $stone, 1, 7, $pos->y - $height, $pos->y - intval($height / 4))
		]);
		$ores->populate($level, $pos->x >> 4, $pos->z >> 4, $random);//x z undefined
	}
}