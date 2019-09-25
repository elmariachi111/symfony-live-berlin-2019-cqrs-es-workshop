<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class UserHasCheckedIn extends AggregateChanged
{
    public static function toBuilding(Uuid $building, string $userName) : self {
        return self::occur($building->toString(), ['username' => $userName]);

    }

    public function building() : Uuid
    {
        return Uuid::fromString($this->aggregateId());
    }

    public function userName(): string
    {
        return "";
    }
}
