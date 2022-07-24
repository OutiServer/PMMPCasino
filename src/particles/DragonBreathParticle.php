<?php

declare(strict_types=1);

namespace outiserver\casino\particles;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\ParticleIds;
use pocketmine\world\particle\Particle;

final class DragonBreathParticle implements Particle
{
    public function encode(Vector3 $pos): array
    {
        return [LevelEventPacket::standardParticle(ParticleIds::DRAGON_BREATH_FIRE, 0, $pos)];
    }
}