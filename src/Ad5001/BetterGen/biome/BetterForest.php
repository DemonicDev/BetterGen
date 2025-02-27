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

namespace Ad5001\BetterGen\biome;

use Ad5001\BetterGen\generator\BetterNormal;
use Ad5001\BetterGen\Main;
use Ad5001\BetterGen\populator\BushPopulator;
use Ad5001\BetterGen\populator\FallenTreePopulator;
use Ad5001\BetterGen\populator\TreePopulator;
use Ad5001\BetterGen\utils\CommonUtils;
use pocketmine\world\biome\Biome;
use pocketmine\world\biome\ForestBiome;
use pocketmine\world\generator\populator\TallGrass;
/**use pocketmine\world\generator\object\OakTree;
use pocketmine\world\generator\object\BirchTree;
use pocketmine\world\generator\object\SpruceTree;**/
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\block\utils\TreeType;

class BetterForest extends ForestBiome implements Mountainable{

	/** @var string[] * */
	static $types = [
		"Oak Forest",
		"Birch Forest",
		"Sakura Forest"
	];

	/** @var int[] * */
	static $ids = [
        BiomeIds::FOREST,
        BiomeIds::BIRCH_FOREST,
		Main::SAKURA_FOREST
	];

	/**
	 * Constructs the class
	 *
	 * @param int $type = 0
	 * @param array $infos
	 */
    public function replaceForMaxTrees(){
        if(mt_rand(0,10) % 2 == 0){
            return TRUE;
        }
        return false;
    }
	public function __construct(int $type = 0, array $infos = [0.6, 0.5]){
        /**ADD TREETYPE BY DemonicDev**/
        switch($type) {
            case 0:
                $test = TreeType::OAK();
            break;
            case 1:
                $test = TreeType::SPRUCE();
            break;
            case 3:
                $test = TreeType::BIRCH();
            break;
            default:
               $test = TreeType::OAK();
            break;
        }
		parent::__construct($test);

		$this->clearPopulators();
		$this->type = $type;

		$bush = new BushPopulator($type);
		$bush->setBaseAmount(10);

		if(!CommonUtils::in_arrayi("Bushes", BetterNormal::$options["delStruct"])){
			$this->addPopulator($bush);
		}

		$ft = new FallenTreePopulator($type);
		$ft->setBaseAmount(0);
		$ft->setRandomAmount(4);

		if(!CommonUtils::in_arrayi("FallenTrees", BetterNormal::$options["delStruct"])){
			$this->addPopulator($ft);
		}

		$trees = new TreePopulator($type);
        /**maxPerChunk dont exist ->DemonicDev*/
		#$trees->setBaseAmount((null !== @constant(TreePopulator::$types[$type] . "::maxPerChunk")) ? constant(TreePopulator::$types[$type] . "::maxPerChunk") : 5);
		$trees->setBaseAmount($this->replaceForMaxTrees() ? 10 : 5);

		if(!CommonUtils::in_arrayi("Trees", BetterNormal::$options["delStruct"])){
			$this->addPopulator($trees);
		}

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(3);

		if(!CommonUtils::in_arrayi("TallGrass", BetterNormal::$options["delStruct"])){
			$this->addPopulator($tallGrass);
		}

		$this->setElevation(63, 69);
		$this->temperature = $infos[0];
		$this->rainfall = $infos[1];
	}

	/**
	 * Registers a forest
	 *
	 * @param string $name
	 * @param string $treeClass
	 * @param array  $infos
	 * @return bool
	 */
	public static function registerForest(string $name, string $treeClass, array $infos): bool{
		self::$types[] = str_ireplace("tree", "", explode("\\", $treeClass)[count(explode("\\", $treeClass))]) . " Forest";
		TreePopulator::$types[] = $treeClass;

		self::$ids[] = Main::SAKURA_FOREST + (count(self::$types) - 2);
		Main::registerBiome(Main::SAKURA_FOREST + (count(self::$types) - 2), new BetterForest(count(self::$types) - 1, $infos));

		return true;
	}

	public function getName(): string{
		return str_ireplace(" ", "", self::$types[$this->type]);
	}

	/**
	 * Returns the ID relatively.
	 *
	 * @return int
	 */
	public function getId(): int{
		return self::$ids[$this->type];
	}
}
