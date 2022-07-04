<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Caches\CasinoCache;

use Ken_Cir\EconomyCore\Caches\Base\BaseCacheManager;

class CasinoCacheManager extends BaseCacheManager
{
    public function create(string $xuid): void
    {
        $this->data[$xuid] = new CasinoCache($xuid);
    }

    public function get(string $xuid): ?CasinoCache
    {
        if (!isset($this->data[$xuid])) return null;

        return $this->data[$xuid];
    }

    public function delete(string $xuid): void
    {
        if (!$this->get($xuid)) return;

        unset($this->data[$xuid]);
    }
}