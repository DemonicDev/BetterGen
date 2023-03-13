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
use pocketmine\world\generator\object\Tree as ObjectTree;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use function intval;
use pocketmine\block\VanillaBlocks;
class FallenTree{

	/** @var array */
	public static $overridable = [
		0 => true,
		6 => true,
		467 => true,
		18 => true,
		37 => true,
		38 => true,
		78 => true,
		162 => true,
		161 => true,
		81 => true
	];

	public $trunk = [];
	/** @var Tree */
	protected $tree;
	/** @var int */
	protected $direction;
	/** @var Random */
	protected $random;
	/** @var int */
	protected $length = 0;

	/**
	 * Constructs the class
	 *
	 * @param ObjectTree $tree
	 */
	public function __construct(ObjectTree $tree){
		$this->tree = $tree;
	}

	/**
	 * Checks the placement a fallen tree
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
		//echo "Checking at $x $y $z FallenTree\n";
		$randomHeight = round($random->nextBoundedInt($this->tree->treeHeight < 6 ? 6 : $this->tree->treeHeight) - ($this->tree->treeHeight < 6 ? 3 : $this->tree->treeHeight / 2));
		$this->length = intval(($this->tree->treeHeight ?? 5) + $randomHeight);
		$this->direction = $random->nextBoundedInt(4);
		$this->random = $random;

		switch($this->direction){
			case 0:
			case 1:// Z+
				$return = array_merge(
					BuildingUtils::fillCallback(new Vector3($x, $y, $z), new Vector3($x, $y, $z + $this->length),
						function($v3, ChunkManager $level){
							if(!isset(self::$overridable[$level->getBlockIdAt($v3->x, $v3->y, $v3->z)])){
								//echo "$v3 is not overwritable (" . $level->getBlockIdAt($v3->x, $v3->y, $v3->z) . ").\n";
								return false;
							}

							return true;
						},
						$level
					),

					BuildingUtils::fillCallback(new Vector3($x, $y - 1, $z), new Vector3($x, $y - 1, $z + $this->length),
						function($v3, ChunkManager $level){
							if(isset(self::$overridable[$level->getBlockIdAt($v3->x, $v3->y, $v3->z)])){
								//echo "$v3 is overwritable (" . $level->getBlockIdAt($v3->x, $v3->y, $v3->z) . ").\n";
								return false;
							}

							return true;
						},
						$level
					)
				);

				if(in_array(false, $return, true)){
					return false;
				}
			break;

			case 2:
			case 3: // X+
				$return = array_merge(
					BuildingUtils::fillCallback(new Vector3($x, $y, $z), new Vector3($x + $this->length, $y, $z),
						function($v3, ChunkManager $level){
							if(!isset(self::$overridable[$level->getBlockIdAt($v3->x, $v3->y, $v3->z)])){
								//echo "$v3 is not overwritable (" . $level->getBlockIdAt($v3->x, $v3->y, $v3->z) . ").\n";
								return false;
							}

							return true;
						},
						$level
					),

					BuildingUtils::fillCallback(new Vector3($x, $y - 1, $z), new Vector3($x + $this->length, $y - 1, $z),
						function($v3, ChunkManager $level){
							if(isset(self::$overridable[$level->getBlockIdAt($v3->x, $v3->y, $v3->z)])){
								//echo "$v3 is overwritable (" . $level->getBlockIdAt($v3->x, $v3->y, $v3->z) . ").\n";
								return false;
							}

							return true;
						},
						$level
					)
				);

				if(in_array(false, $return, true)){
					return false;
				}
			break;
		}

		return true;
	}

	/**
	 * Places a fallen tree
	 *
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @return void
	 */
	public function placeObject(ChunkManager $level, int $x, int $y, int $z): void{
		$length = intval($this->length);

		switch($this->direction){
			case 0:
				$level->setBlockIdAt($x, $y, $z, $this->tree->trunkBlock);
				$level->setBlockDataAt($x, $y, $z, $this->tree->type);
				$z += 2;
			break;

			case 1:// Z+
				BuildingUtils::fill($level, new Vector3($x, $y, $z), new Vector3($x, $y, $z + $length), Block::get($this->tree->trunkBlock, $this->tree->type + 8));
				BuildingUtils::fillRandom($level, new Vector3($x + 1, $y, $z), new Vector3($x + 1, $y, $z + $length), Block::get(Block::VINE), $this->random);
				BuildingUtils::fillRandom($level, new Vector3($x - 1, $y, $z), new Vector3($x - 1, $y, $z + $length), Block::get(Block::VINE), $this->random);
			break;

			case 2:
				$level->setBlockIdAt($x, $y, $z, $this->tree->trunkBlock);
				$level->setBlockDataAt($x, $y, $z, $this->tree->type);
				$x += 2;
			break;

			case 3: // X+
				BuildingUtils::fill($level, new Vector3($x, $y, $z), new Vector3($x + $length, $y, $z), Block::get($this->tree->trunkBlock, $this->tree->type + 4));
				BuildingUtils::fillRandom($level, new Vector3($x, $y, $z + 1), new Vector3($x + $length, $y, $z + 1), Block::get(Block::VINE), $this->random);
				BuildingUtils::fillRandom($level, new Vector3($x, $y, $z - 1), new Vector3($x + $length, $y, $z - 1), Block::get(Block::VINE), $this->random);
			break;
		}

		// Second call to build the last wood block
		switch($this->direction){
			case 1:
				$level->setBlockIdAt($x, $y, $z + $length + 2, $this->tree->trunkBlock);
				$level->setBlockDataAt($x, $y, $z + $length + 2, $this->tree->type);
			break;

			case 3:
				$level->setBlockIdAt($x + $length + 2, $y, $z, $this->tree->trunkBlock);
				$level->setBlockDataAt($x + $length + 2, $y, $z, $this->tree->type);
			break;
		}
	}

	/**
	 * Places a block
	 *
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param ChunkManager $level
	 * @return void
	 */
	public function placeBlock(int $x, int $y, int $z, ChunkManager $level): void{
		if(isset(self::$overridable[$level->getBlockIdAt($x, $y, $z)]) && !isset(self::$overridable[$level->getBlockIdAt($x, $y - 1, $z)])){
			$level->setBlockIdAt($x, $y, $z, $this->trunk[0]);
			$level->setBlockDataAt($x, $y, $z, $this->trunk[1]);
		}
	}
}
