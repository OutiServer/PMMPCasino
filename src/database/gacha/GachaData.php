<?php

declare(strict_types=1);

namespace outiserver\casino\database\gacha;

use outiserver\economycore\Database\Base\BaseData;
use poggit\libasynql\DataConnector;

class GachaData extends BaseData
{
    private int $id;

    private string $name;

    private int $price;

    public function __construct(DataConnector $dataConnector, int $id, string $name, int $price)
    {
        parent::__construct($dataConnector);

        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    protected function update(): void
    {
        $this->dataConnector->executeChange("economy.casino.gachas.update",
            [
                "name" => $this->name,
                "price" => $this->price,
                "id" => $this->id
            ]);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->update();
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
        $this->update();
    }
}