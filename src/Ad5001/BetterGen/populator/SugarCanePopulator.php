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

use Ad5001\BetterGen\structure\SugarCane;
use pocketmine\block\Block;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;
use pocketmine\utils\Random;

class SugarCanePopulator extends AmountPopulator{

	/** @var ChunkManager */
	protected $level;

	/**
	 * Constructs the class
	 */
	public function __construct(){
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
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random): void{
		$this->level = $level;
		$amount = $this->getAmount($random);
		$sugarcane = new SugarCane ();
		for($i = 0; $i < $amount; $i++){
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y !== -1 and $sugarcane->canPlaceObject($level, $x, $y, $z, $random)){
				$sugarcane->placeObject($level, $x, $y, $z);
			}
		}
	}

	/**
	 * Gets the top block (y) on an x and z axes
	 *
	 * @param $x int
	 * @param $z int
	 * @return int
	 */
	protected function getHighestWorkableBlock($x, $z){
		for($y = World::Y_MAX - 1; $y >= 0; --$y){
			$b = $this->level->getBlockAt($x, $y, $z);
			if($b !== 0 and $b !== 18 and $b !== 161){
				break;
			}
		}

		return $y === 0 ? -1 : ++$y;
	}
}