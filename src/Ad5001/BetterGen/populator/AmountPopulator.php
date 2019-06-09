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

use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

abstract class AmountPopulator extends Populator{

	/** @var int */
	protected $baseAmount = 0;
	/** @var int */
	protected $randomAmount = 0;

	/**
	 * Cross-software class for random amount
	 */

	/**
	 * Returns the amount based on random
	 *
	 * @param Random $random
	 * @return int
	 */
	public function getAmount(Random $random): int{
		return $this->baseAmount + $random->nextRange(0, $this->randomAmount + 1);
	}

	/**
	 * Returns base amount
	 *
	 * @return int
	 */
	public function getBaseAmount(): int{
		return $this->baseAmount;
	}

	/**
	 * Sets the base addition amount
	 *
	 * @param $amount int
	 */
	public function setBaseAmount(int $amount): void{
		$this->baseAmount = $amount;
	}

	/**
	 * Returns the random additional amount
	 *
	 * @return int
	 */
	public function getRandomAmount(): int{
		return $this->randomAmount;
	}

	/**
	 * Sets the random addition amount
	 *
	 * @param $amount int
	 */
	public function setRandomAmount(int $amount): void{
		$this->randomAmount = $amount;
	}
}