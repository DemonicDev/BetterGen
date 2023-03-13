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

namespace Ad5001\BetterGen\generator;

use pocketmine\world\biome\Biome;
use pocketmine\world\generator\biome\BiomeSelector;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\utils\Random;

class BetterBiomeSelector extends BiomeSelector{

	/** @var Biome */
	protected $fallback;

	/** @var Simplex */
	protected $temperature;
	/** @var Simplex */
	protected $rainfall;

	/** @var Biome[] */
	protected $biomes = [];

	/**
	 * Constructs the class
	 *
	 * @param Random $random
	 * @param Biome  $fallback
	 */
	public function __construct(Random $random, Biome $fallback){
		parent::__construct($random);

		$this->fallback = $fallback;
		$this->temperature = new Simplex($random, 2, 1 / 16, 1 / 512);
		$this->rainfall = new Simplex($random, 2, 1 / 16, 1 / 512);
	}

/**
	 * Inherited function
	 *
	 * @return void
	 */
	public function recalculate(): void{
	}

	/**
	 * Adds a biome to the selector. Don't do this directly. Use BetterNormal::registerBiome
	 *
	 * @param Biome $biome
	 * @return void
	 * @internal This method is called by BetterNormal::registerBiome
	 */
	public function addBiome(Biome $biome): void{
		$this->biomes[$biome->getId()] = $biome;
	} // Using our own system, No need for that

	/**
	 * Picks a biome relative to $x and $z
	 *
	 * @param int|float $x
	 * @param int|float $z
	 *
	 * @return Biome
	 */
	public function pickBiome($x, $z): Biome{
		$temperature = ($this->getTemperature($x, $z));
		$rainfall = ($this->getRainfall($x, $z));

		$biomeId = BetterNormal::getBiome($temperature, $rainfall);
		$b = (($biomeId instanceof Biome) ? $biomeId : ($this->biomes[$biomeId] ?? $this->fallback));

		return $b;
	}

	/**
	 * Returns the temperature from a location
	 *
	 * @param int $x
	 * @param int $z
	 *
	 * @return float|int
	 */
	public function getTemperature(float $x,float $z): float{
		return ($this->temperature->noise2D($x, $z, true) + 1) / 2;
	}

	/**
	 * Returns the rainfall from a location
	 *
	 * @param int $x
	 * @param int $z
	 *
	 * @return float|int
	 */
	public function getRainfall(float $x,float $z): float{
		return ($this->rainfall->noise2D($x, $z, true) + 1) / 2;
	}

	/**
	 * @param float $temperature
	 * @param float $rainfall
	 * @return int
	 */
	protected function lookup(float $temperature, float $rainfall): int{
		return (int) BetterNormal::getBiome($temperature, $rainfall);
	}
}