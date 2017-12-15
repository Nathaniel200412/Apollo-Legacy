<?php

namespace pocketmine\network\raknet\protocol;

class PONG_DataPacket extends Packet{
	
    public static $ID = 0x03;

    public $pingID;

    public function encode(){
        parent::encode();
        $this->putLong($this->pingID);
    }

    public function decode(){
        parent::decode();
        $this->pingID = $this->getLong();
    }
}