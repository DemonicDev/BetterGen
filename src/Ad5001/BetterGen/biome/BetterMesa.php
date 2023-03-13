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
use Ad5001\BetterGen\populator\CactusPopulator;
use Ad5001\BetterGen\populator\DeadbushPopulator;
use Ad5001\BetterGen\populator\SugarCanePopulator;
use Ad5001\BetterGen\populator\TreePopulator;
use Ad5001\BetterGen\utils\CommonUtils;
use pocketmine\block\Block;
use pocketmine\block\GoldOre;
use pocketmine\world\biome\SandyBiome;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Ore;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;

class BetterMesa extends SandyBiome{

	/**
	 * Constructs the class
	 */
	public function __construct(){
		parent::__construct();

		$deadBush = new DeadbushPopulator ();
		$deadBush->setBaseAmount(1);
		$deadBush->setRandomAmount(2);

		$cactus = new CactusPopulator ();
		$cactus->setBaseAmount(1);
		$cactus->setRandomAmount(2);

		$sugarCane = new SugarCanePopulator ();
		$sugarCane->setRandomAmount(20);
		$sugarCane->setBaseAmount(3);

		$sugarCane = new TreePopulator ();
		$sugarCane->setRandomAmount(2);
		$sugarCane->setBaseAmount(0);

		$ores = new Ore();
        $test = VanillaBlocks::RED_SANDSTONE();
		$ores->setOreTypes([
			new OreType(VanillaBlocks::GOLD_ORE(), $test, 2, 8, 0, 32)
		]);

		if(!CommonUtils::in_arrayi("Cactus", BetterNormal::$options["delStruct"])){
			$this->addPopulator($cactus);
		}

		if(!CommonUtils::in_arrayi("DeadBush", BetterNormal::$options["delStruct"])){
			$this->addPopulator($deadBush);
		}

		if(!CommonUtils::in_arrayi("SugarCane", BetterNormal::$options["delStruct"])){
			$this->addPopulator($sugarCane);
		}

		if(!CommonUtils::in_arrayi("Ores", BetterNormal::$options["delStruct"])){
			$this->addPopulator($ores);
		}

		$this->setElevation(80, 83);
		$this->temperature = 0.8;
		$this->rainfall = 0;

		$this->setGroundCover([
            VanillaBlocks::DIRT(),
            VanillaBlocks::CLAY(),
            VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::CLAY(),
			VanillaBlocks::CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::STAINED_CLAY(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE()
		]);
	}

	/**
	 * Returns the biome name
	 *
	 * @return string
	 */
	public function getName(): string{
		return "BetterMesa";
	}

	/**
	 * Returns biome id
	 *
	 * @return int
	 */
	public function getId(): int{
		return 39;
	}
}
