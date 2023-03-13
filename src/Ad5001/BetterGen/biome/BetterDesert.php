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
use Ad5001\BetterGen\populator\TemplePopulator;
use Ad5001\BetterGen\populator\WellPopulator;
use Ad5001\BetterGen\utils\CommonUtils;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\world\biome\Biome;
use pocketmine\world\biome\SandyBiome;

class BetterDesert extends SandyBiome implements Mountainable{

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

		$temple = new TemplePopulator ();
		$well = new WellPopulator ();

		if(!CommonUtils::in_arrayi("Cactus", BetterNormal::$options["delStruct"])){
			$this->addPopulator($cactus);
		}

		if(!CommonUtils::in_arrayi("Deadbush", BetterNormal::$options["delStruct"])){
			$this->addPopulator($deadBush);
		}

		if(!CommonUtils::in_arrayi("SugarCane", BetterNormal::$options["delStruct"])){
			$this->addPopulator($sugarCane);
		}

		if(!CommonUtils::in_arrayi("Temples", BetterNormal::$options["delStruct"])){
			$this->addPopulator($temple);
		}

		if(!CommonUtils::in_arrayi("Wells", BetterNormal::$options["delStruct"])){
			$this->addPopulator($well);
		}

		$this->setElevation(63, 70);
		$this->temperature = 0.5;
		$this->rainfall = 0;

		$this->setGroundCover([
            VanillaBlocks::SAND(),
            VanillaBlocks::SAND(),
            VanillaBlocks::SAND(),
            VanillaBlocks::SAND(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
            VanillaBlocks::SANDSTONE(),
		]);
	}

	/**
	 * Constructs the class
	 *
	 * @return string
	 */
	public function getName(): string{
		return "BetterDesert";
	}

	/**
	 * Returns biome id
	 *
	 * @return int
	 */
	public function getId(): int{
        #$this->registerBiome(BiomeRegistry::getInstance()->getBiome(BiomeIds::PLAINS));
		return BiomeIds::DESERT;
	}
}