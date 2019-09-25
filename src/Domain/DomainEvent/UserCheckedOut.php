<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class UserCheckedOut extends AggregateChanged
{
    public static function ofBuilding(Uuid $building, string $username) : self
    {
        return self::occur($building->toString(), ['username' => $username]);
    }

    public function building() : Uuid
    {
        return Uuid::fromString($this->aggregateId());
    }

    public function username() : string
    {
        return $this->payload()['username'];
    }
}