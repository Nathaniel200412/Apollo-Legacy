<?php

namespace net\daporkchop\world;

use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\biome\BiomeSelector;
use pocketmine\utils\Random;

class PorkBiomeSelector extends BiomeSelector   {
    public $fallback;
    
    public function __construct(Random $random, Biome $fallback)  {
        parent::__construct($random, function($temp, $rain) {}, $fallback);
        
        $this->fallback = $fallback;
		$this->lookup = $lookup;
		
    }

	/**
	 * Lookup function called by recalculate() to determine the biome to use for this temperature and rainfall.
	 *
	 * @param float $temperature
	 * @param float $rainfall
	 *
	 * @return int biome ID 0-255
	 */
	abstract protected function lookup(float $temperature, float $rainfall) : int;
	public function recalculate() : void{
		$this->map = new \SplFixedArray(64 * 64);
		for($i = 0; $i < 64; ++$i){
			for($j = 0; $j < 64; ++$j){
				$this->map[$i + ($j << 6)] = call_user_func($this->lookup, $i / 63, $j / 63);
				
		//		$biome = Biome::getBiome($this->lookup($i / 63, $j / 63));
				if($biome instanceof UnknownBiome){
					throw new \RuntimeException("Unknown biome returned by selector with ID " . $biome->getId());
				}
				$this->map[$i + ($j << 6)] = $biome;
			}
		}
	}
    
    public function pickBiomeNew($x, $z, $height){
        $temperature = $this->getTemperature($x, $z);
        $rainfall = $this->getRainfall($x, $z);
        
        $biomeId = 0;
        
        if ($height == 1)    {
            $biomeId = Biome::OCEAN;
        } elseif ($height <= 64){
            $biomeId = Biome::BEACH;
        } else {
            if ($temperature > 0.8) {
                if ($rainfall > 0.85){
                    $biomeId = Biome::JUNGLE;
                } elseif ($rainfall > 0.7)  {
                    $biomeId = Biome::SWAMP;
                } elseif ($rainfall > 0.55)  {
                    $biomeId = Biome::SAVANNA;
                } elseif ($rainfall > 0.4) {
                    $biomeId = Biome::MESA;
                } else {
                    $biomeId = Biome::DESERT;
                }
            } elseif ($temperature > 0.6)   {
                if ($rainfall > 0.5){
                    if ($rainfall > 0.75){
                        $biomeId = Biome::BIRCH_FOREST; //recalculate
                    } else {
                        $biomeId = Biome::FOREST;
                    }
                } else {
                    $biomeId = Biome::PLAINS;
                }
            } else {
                if ($rainfall > 0.75){
                    $biomeId = Biome::TAIGA;
                } elseif ($rainfall < 0.5){
                    $biomeId = Biome::MOUNTAINS;
                } else {
                    $biomeId = Biome::ICE_PLAINS;
                }
            }
        }
        
        return new BiomeSelectorOutput(Biome::getBiome($biomeId), $temperature, $rainfall);
    }
}
