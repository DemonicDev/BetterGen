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
use Ad5001\BetterGen\populator\IglooPopulator;
use Ad5001\BetterGen\utils\CommonUtils;
use pocketmine\block\Block;
use pocketmine\world\biome\Biome;
use pocketmine\world\biome\SnowyBiome;
use pocketmine\world\generator\populator\TallGrass;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;

class BetterIcePlains extends SnowyBiome implements Mountainable{

	/**
	 * Constructs the class
	 */
	public function __construct(){
		parent::__construct();

		$this->setGroundCover([
            VanillaBlocks::SNOW(),
            VanillaBlocks::GRASS(),
            VanillaBlocks::DIRT(),
            VanillaBlocks::DIRT(),
            VanillaBlocks::DIRT()
		]);

		if(!CommonUtils::in_arrayi("Igloos", BetterNormal::$options["delStruct"])){
			$this->addPopulator(new IglooPopulator ());
		}

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(3);

		if(!CommonUtils::in_arrayi("TallGrass", BetterNormal::$options["delStruct"])){
			$this->addPopulator($tallGrass);
		}

		$this->setElevation(63, 74);
		$this->temperature = 0.05;
		$this->rainfall = 0.8;
	}

	/**
	 * Returns the biome name
	 *
	 * @return string
	 */
	public function getName(): string{
		return "BetterIcePlains";
	}

	/**
	 * Returns the biomes' id.
	 *
	 * @return int biome id
	 */
	public function getId(): int{
		return BiomeIds::ICE_PLAINS;
	}
}