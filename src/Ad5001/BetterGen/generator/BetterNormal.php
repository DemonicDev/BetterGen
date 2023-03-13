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

use Ad5001\BetterGen\biome\BetterDesert;
use Ad5001\BetterGen\biome\BetterForest;
use Ad5001\BetterGen\biome\BetterIcePlains;
use Ad5001\BetterGen\biome\BetterMesa;
use Ad5001\BetterGen\biome\BetterMesaPlains;
use Ad5001\BetterGen\biome\BetterRiver;
use Ad5001\BetterGen\biome\Mountainable;
use Ad5001\BetterGen\populator\CavePopulator;
use Ad5001\BetterGen\populator\DungeonPopulator;
use Ad5001\BetterGen\populator\FloatingIslandPopulator;
use Ad5001\BetterGen\populator\LakePopulator;
use Ad5001\BetterGen\populator\MineshaftPopulator;
use Ad5001\BetterGen\populator\RavinePopulator;
use Ad5001\BetterGen\utils\CommonUtils;
use pocketmine\block\Block;
use pocketmine\block\CoalOre;
use pocketmine\block\DiamondOre;
use pocketmine\block\Dirt;
use pocketmine\block\EmeraldOre;
use pocketmine\block\GoldOre;
use pocketmine\block\Gravel;
use pocketmine\block\IronOre;
use pocketmine\block\LapisOre;
use pocketmine\block\RedstoneOre;
use pocketmine\world\biome\Biome;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\world\biome\BiomeRegistry;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\GroundCover;
use pocketmine\world\generator\populator\Ore;
use pocketmine\world\generator\populator\Populator;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\block\VanillaBlocks;

class BetterNormal extends Generator{

	/** @var array */
  /**  const NOT_OVERWRITABLE = [
        VanillaBlocks::STONE(),
        VanillaBlocks::GRAVEL(),
        VanillaBlocks::BEDROCK(),
        VanillaBlocks::DIAMOND_ORE(),
        VanillaBlocks::GOLD_ORE(),
        VanillaBlocks::LAPIS_ORE(),
        VanillaBlocks::REDSTONE_ORE(),
        VanillaBlocks::IRON_ORE(),
        VanillaBlocks::COAL_ORE(),
        VanillaBlocks::WATER(),
        VanillaBlocks::STILL_WATER()
	];**/
    const NOT_OVERWRITABLE = [
        1,
        13,
        7,
        56,
        14,
        21,
        73,
        15,
        16,
        9,
        9
    ];


	/** @var Biome[][] */
	public static $biomes = [];
	/** @var Biome[] */
	public static $biomeById = [];
	/** @var Level[] */
	public static $levels = [];
	/** @var mixed[][] */
	public static $options = [
		"delBio"    => [
		],
		"delStruct" => [
			"Lakes"
		]
	];

	/** @var int[][] */
	protected static $GAUSSIAN_KERNEL = null;
	/** @var int */
	protected static $SMOOTH_SIZE = 2;
	/** @var BetterBiomeSelector */
	protected $selector;
	/** @var Level */
	protected $level;
		/** @var Random */
	protected $random; // From main class
	/** @var Populator[] */
	protected $populators = [];
	/** @var Populator[] */
	protected $generationPopulators = [];
	/** @var int */
	protected $waterHeight = 63;
	/** @var Simplex */
	protected $noiseBase;

    protected $seed;

    protected string $preset;

	/**
	 * Constructs the class
	 *
	 * @param array $options
	 */
	public function __construct(int $seed, string $preset){
        $this->seed = $seed;
        $this->preset = $preset;
        $this->random = new Random($seed);
        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);
		#self::$options["preset"] = $options["preset"];
		#$options = (array) json_decode($options["preset"]);
		#if(isset($options["delBio"])){
			#if(is_string($options["delBio"])){
			#	$options["delBio"] = explode(",", $options["delBio"]);
			#}
			#if(count($options["delBio"]) !== 0){
			#	self::$options["delBio"] = $options["delBio"];
			#}
		#}
		#if(isset($options["delStruct"])){
		#	if(is_string($options["delStruct"])){
		#		$options["delStruct"] = explode(",", $options["delStruct"]);
		#	}
		#	if(count($options["delStruct"]) !== 0){
			#	self::$options["delStruct"] = $options["delStruct"];
			#}
	#	}
		if(self::$GAUSSIAN_KERNEL === null){
			self::generateKernel();
		}
	}

	/**
	 * Generates the generation kernel based on smooth size (here 2)
	 */
	protected static function generateKernel(){
		self::$GAUSSIAN_KERNEL = [];

		$bellSize = 1 / self::$SMOOTH_SIZE;
		$bellHeight = 2 * self::$SMOOTH_SIZE;

		for($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; $sx++){
			self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];

			for($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; $sz++){
				$bx = $bellSize * $sx;
				$bz = $bellSize * $sz;
				self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] [$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
			}
		}
	}

	/*
	 * Adds a biome to the selector. Do not use this method directly use Main::registerBiome which registers it properly
	 * @param $biome Biome
	 * @return bool
	 */

	/**
	 * Inits the class for the var
	 *
	 * @param ChunkManager $level
	 * @param Random       $random
	 * @return        void
	 */
	public function init(ChunkManager $level): void{
		$this->level = $level;

		self::$levels[] = $level;

		$this->random->setSeed($this->seed);
		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);
		$this->random->setSeed($this->seed);

		$this->registerBiome(BiomeRegistry::getInstance()->getBiome(BiomeIds::OCEAN));
		$this->registerBiome(BiomeRegistry::getInstance()->getBiome(BiomeIds::PLAINS));
		$this->registerBiome(new BetterDesert());
		$this->registerBiome(new BetterMesa());
		$this->registerBiome(new BetterMesaPlains());
		$this->registerBiome(BiomeRegistry::getInstance()->getBiome(BiomeIds::TAIGA));
		$this->registerBiome(BiomeRegistry::getInstance()->getBiome(BiomeIds::SWAMPLAND));
		$this->registerBiome(new BetterRiver());
		$this->registerBiome(new BetterIcePlains ());
		$this->registerBiome(new BetterForest(0, [0.6, 0.5]));
		$this->registerBiome(new BetterForest(1, [0.7, 0.8]));
		$this->registerBiome(new BetterForest(2, [0.6, 0.4]));
		$this->selector = new BetterBiomeSelector($this->random, self::getBiome(0, 0));

		foreach(self::$biomes as $rain){
			foreach($rain as $biome){
				$this->selector->addBiome($biome);
			}
		}

		$this->selector->recalculate();

		$cover = new GroundCover();
		$this->generationPopulators[] = $cover;

		if(!CommonUtils::in_arrayi("Lakes", self::$options["delStruct"])){
			$lake = new LakePopulator();
			$lake->setBaseAmount(0);
			$lake->setRandomAmount(1);

			$this->generationPopulators[] = $lake;
		}

		if(!CommonUtils::in_arrayi("Caves", self::$options["delStruct"])){
			$cave = new CavePopulator ();
			$cave->setBaseAmount(0);
			$cave->setRandomAmount(2);

			$this->generationPopulators[] = $cave;
		}

		if(!CommonUtils::in_arrayi("Ravines", self::$options["delStruct"])){
			$ravine = new RavinePopulator ();
			$ravine->setBaseAmount(0);
			$ravine->setRandomAmount(51);

			$this->generationPopulators[] = $ravine;
		}

		if(!CommonUtils::in_arrayi("Mineshafts", self::$options["delStruct"])){
			$mineshaft = new MineshaftPopulator ();
			$mineshaft->setBaseAmount(0);
			$mineshaft->setRandomAmount(102);

			$this->populators[] = $mineshaft;
		}

		if(!CommonUtils::in_arrayi("FloatingIslands", self::$options["delStruct"])){
			$fisl = new FloatingIslandPopulator();
			$fisl->setBaseAmount(0);
			$fisl->setRandomAmount(132);

			$this->populators[] = $fisl;
		}

		if(!CommonUtils::in_arrayi("Dungeons", self::$options["delStruct"])){
			$dungeon = new DungeonPopulator();
			$dungeon->setBaseAmount(0);
			$dungeon->setRandomAmount(20);

			$this->populators[] = $dungeon;
		}

		if(!CommonUtils::in_arrayi("Ores", self::$options["delStruct"])){
			$ores = new Ore();
            $stone = VanillaBlocks::STONE();
			$ores->setOreTypes([
				new OreType(VanillaBlocks::COAL_ORE(), $stone, 20, 16, 0, 128),
				new OreType(VanillaBlocks::IRON_ORE(), $stone, 20, 8, 0, 64),
				new OreType(VanillaBlocks::REDSTONE_ORE(), $stone, 8, 7, 0, 16),
				new OreType(VanillaBlocks::LAPIS_LAZULI_ORE(), $stone, 1, 6, 0, 32),
				new OreType(VanillaBlocks::GOLD_ORE(), $stone, 2, 8, 0, 32),
				new OreType(VanillaBlocks::DIAMOND_ORE(), $stone, 1, 7, 0, 16),
				new OreType(VanillaBlocks::EMERALD_ORE(), $stone, 1, 4, 0, 16),
				new OreType(VanillaBlocks::DIRT(), $stone, 20, 32, 0, 128),
				new OreType(VanillaBlocks::GRAVEL(), $stone, 10, 16, 0, 128)
			]);

			$this->populators[] = $ores;
		}
	}

	public static function registerBiome(Biome $biome): bool{
		if(CommonUtils::in_arrayi($biome->getName(), self::$options["delBio"])){
			return false;
		}

		foreach(self::$levels as $lvl){
			if(isset($lvl->selector)){
				$lvl->selector->addBiome($biome);
			}
		}// If no selector created, it would cause errors. These will be added when selectors

		if(!isset(self::$biomes[(string) $biome->getRainfall()])){
			self::$biomes[( string) $biome->getRainfall()] = [];
		}

		self::$biomes[( string) $biome->getRainfall()] [( string) $biome->getTemperature()] = $biome;
		ksort(self::$biomes[( string) $biome->getRainfall()]);
		ksort(self::$biomes);
		self::$biomeById[$biome->getId()] = $biome;

		return true;
	}

	/**
	 * Returns a biome by temperature
	 *
	 * @param $temperature float
	 * @param $rainfall    float
	 *
	 * @return null|Biome
	 */
	public static function getBiome(float $temperature, float $rainfall){
		$ret = null;
		if(!isset(self::$biomes[( string) round($rainfall, 1)])){
			while(!isset(self::$biomes[( string) round($rainfall, 1)])){
				if(abs($rainfall - round($rainfall, 1)) >= 0.05){
					$rainfall += 0.1;
				}
				if(abs($rainfall - round($rainfall, 1)) < 0.05){
					$rainfall -= 0.1;
				}
				if(round($rainfall, 1) < 0){
					$rainfall = 0;
				}
				if(round($rainfall, 1) >= 0.9){
					$rainfall = 0.9;
				}
			}
		}
		$b = self::$biomes[( string) round($rainfall, 1)];
		foreach($b as $t => $biome){
			if($temperature <= (float) $t){
				$ret = $biome;
				break;
			}
		}
		if(is_string($ret)){
			$ret = new $ret ();
		}

		return $ret;
	}

	/**
	 * Generates a chunk.
	 *
	 * Cloning method to make it work with new methods.
	 *
	 * @param int $chunkX
	 * @param int $chunkZ
     *
     * Updated to API 4 [By DemonicDev]
	 */

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void{
        $this->init($world);
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);
        #$noise = $this->noiseBase->getFastNoise3D(Chunk::EDGE_LENGTH, 128, Chunk::EDGE_LENGTH, 4, 8, 4, $chunkX * Chunk::EDGE_LENGTH, 0, $chunkZ * Chunk::EDGE_LENGTH);
		$noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		$chunk = $this->level->getChunk($chunkX, $chunkZ);

		$biomeCache = [];

		for($x = 0; $x < 16; $x++){
			for($z = 0; $z < 16; $z++){
				$minSum = 0;
				$maxSum = 0;
				$weightSum = 0;

				$biome = $this->pickBiome($chunkX * 16 + $x, $chunkZ * 16 + $z);
				$chunk->setBiomeId($x, $z, $biome->getId());

				for($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; $sx++){
					for($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; $sz++){
						$weight = self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] [$sz + self::$SMOOTH_SIZE];

						if($sx === 0 and $sz === 0){
							$adjacent = $biome;
						}else{
							$index = World::chunkHash($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
							if(isset($biomeCache[$index])){
								$adjacent = $biomeCache[$index];
							}else{
								$biomeCache[$index] = $adjacent = $this->pickBiome($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
							}
						}
						$minSum += ($adjacent->getMinElevation() - 1) * $weight;
						$maxSum += $adjacent->getMaxElevation() * $weight;

						$weightSum += $weight;
					}
				}

				$minSum /= $weightSum;
				$maxSum /= $weightSum;

				$smoothHeight = ($maxSum - $minSum) / 2;

                $bedrock = VanillaBlocks::BEDROCK()->getFullId();
                $stillWater = VanillaBlocks::WATER()->getFullId();
                $stone = VanillaBlocks::STONE()->getFullId();

				for($y = 0; $y < 128; $y++){
					if($y < 3 || ($y < 5 && $this->random->nextBoolean())){
                        $chunk->setFullBlock($x, $y, $z, $bedrock);
						continue;
					}
					$noiseValue = $noise[$x] [$z] [$y] - 1 / $smoothHeight * ($y - $smoothHeight - $minSum);

					if($noiseValue > 0){
                        $chunk->setFullBlock($x, $y, $z, $stone);
					}elseif($y <= $this->waterHeight){
                        $chunk->setFullBlock($x, $y, $z, $stillWater);
					}
				}
			}
		}

		foreach($this->generationPopulators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	/**
	 * Picks a biome by X and Z
	 *
	 * @param    $x    int
	 * @param    $z    int
	 * @return Biome
	 */
	public function pickBiome(int $x, int $z): Biome{
		$hash = $x * 2345803 ^ $z * 9236449 ^ $this->seed;
		$hash *= $hash + 223;
		$xNoise = $hash >> 20 & 3;
		$zNoise = $hash >> 22 & 3;
		if($xNoise == 3){
			$xNoise = 1;
		}
		if($zNoise == 3){
			$zNoise = 1;
		}

		$b = $this->selector->pickBiome($x + $xNoise - 1, $z + $zNoise - 1);
		if($b instanceof Mountainable && $this->random->nextBoundedInt(1000) < 3){
			$b = clone $b;
			// $b->setElevation($b->getMinElevation () + (50 * $b->getMinElevation () / 100), $b->getMaxElevation () + (50 * $b->getMinElevation () / 100));
		}

		return $b;
	}

	/**
	 * Populates a chunk
	 *
	 * @param int $chunkX
	 * @param int $chunkZ
	 * @return void
     *
     * Updated to API 4 [By DemonicDev]
	 */
	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void{
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);
		foreach($this->populators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}

		// Filling lava (lakes & rivers underground)...
		for($x = $chunkX; $x < $chunkX + 16; $x++){
			for($z = $chunkZ; $z < $chunkZ + 16; $z++){
				for($y = 1; $y < 11; $y++){
					if(!in_array($this->level->getBlockAt($x, $y, $z)->getId(), self::NOT_OVERWRITABLE)){
                        if(!$x > 16 and !$z > 16) {
                            $this->level->setBlockAt($x, $y, $z, VanillaBlocks::LAVA());
                        }
					}
				}
			}
		}

		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$biome = self::getBiomeById($chunk->getBiomeId(7, 7));
		$biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}

	/**
	 * Returns a biome by its id
	 *
	 * @param int $id
	 * @return    Biome
	 */
	public function getBiomeById(int $id): Biome{
		return self::$biomeById[$id] ?? self::$biomeById[Biome::OCEAN];
	}

	/**
	 * Return the name of the generator
	 *
	 * @return string
	 */
	public function getName(): string{
		return "betternormal";
	}

	/**
	 * Gives the generators settings.
	 *
	 * @return array
	 */
	public function getSettings(): array{
		return self::$options;
	}

	/**
	 * Returns spawn location
	 *
	 * @return Vector3
	 */
	public function getSpawn(): Vector3{
		return new Vector3(127.5, 128, 127.5);
	}

	/**
	 * Returns a safe spawn location
	 *
	 * @return Vector3
	 */
	public function getSafeSpawn(){
		return new Vector3(127.5, $this->getHighestWorkableBlock(127, 127), 127.5);
	}

	/*
	 * Gets the top block (y) on an x and z axes
	 * @param $x int
	 * @param $z int
	 */
	protected function getHighestWorkableBlock(int $x, int $z){
		for($y = Level::Y_MAX - 1; $y > 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b === Block::DIRT or $b === Block::GRASS or $b === Block::PODZOL){
				break;
			}elseif($b !== 0 and $b !== Block::SNOW_LAYER){
				return -1;
			}
		}

		return ++$y;
	}
}
