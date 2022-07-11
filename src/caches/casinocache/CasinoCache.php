<?php

declare(strict_types=1);

namespace outiserver\casino\caches\casinocache;

use Ken_Cir\EconomyCore\caches\Base\BaseCache;

class CasinoCache extends BaseCache
{
    private string $xuid;

    /**
     * カジノ実行中か
     *
     * @var bool
     */
    private bool $casinoRunning;

    public function __construct(string $xuid)
    {
        $this->xuid = $xuid;
        $this->casinoRunning = false;
    }

    /**
     * @return string
     */
    public function getXuid(): string
    {
        return $this->xuid;
    }

    /**
     * @return bool
     */
    public function getCasinoRunning(): bool
    {
        return $this->casinoRunning;
    }

    /**
     * @param bool $casinoRunning
     */
    public function setCasinoRunning(bool $casinoRunning): void
    {
        $this->casinoRunning = $casinoRunning;
    }
}