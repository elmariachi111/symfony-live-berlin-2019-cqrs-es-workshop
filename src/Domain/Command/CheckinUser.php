<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

final class CheckinUser extends Command
{
    /**
     * @var string
     */
    private $userName;

    /**
     * @var \Rhumsaa\Uuid\Uuid
     */
    private $building;

    private function __construct(string $userName, Uuid $building)
    {
        $this->init();

        $this->userName = $userName;
        $this->building = $building;
    }

    public static function fromUserAndBuilding(Uuid $buildingUuid, string $userName) {
        return new self($userName, $buildingUuid);
    }

    public function userName() : string
    {
        return $this->userName;
    }

    public function building(): Uuid
    {
        return $this->building;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'userName' => $this->userName,
            'building' => $this->building->toString()
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->userName = $payload['userName'];
        $this->building = Uuid::fromString($payload['building']);
    }
}
