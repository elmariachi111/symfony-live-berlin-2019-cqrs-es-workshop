#!/usr/bin/env php
<?php

namespace Building\App;

use Building\Domain\Aggregate\Building;
use Building\Domain\Command\RegisterNewBuilding;
use Building\Domain\DomainEvent\UserCheckedIn;
use Building\Domain\DomainEvent\UserCheckedOut;
use Interop\Container\ContainerInterface;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\StreamName;
use function var_dump;

(static function () {
    /** @var ContainerInterface $dic */
    $dic = require __DIR__ . '/../container.php';

    $eventStore = $dic->get(EventStore::class);

    /** @var AggregateChanged[] $history */
    $history = $eventStore->loadEventsByMetadataFrom(new StreamName('event_stream'), [
        'aggregate_type' => Building::class,
    ]);

    $buildings = [];

    foreach ($history as $event) {
        if($event instanceof RegisterNewBuilding) {
            $buildings[$event->aggregateId()] = [];
        } elseif ($event instanceof UserCheckedIn) {
            $buildings[$event->building()->toString()][$event->username()] = 1;
        } elseif ($event instanceof UserCheckedOut) {
            unset ($buildings[$event->building()->toString()][$event->username()]);
        }
    }

    \array_walk($usersInBuildings, static function (array $users, string $buildingId) {
        \file_put_contents(
            __DIR__ . '/../public/users-' . $buildingId . '.json',
            json_encode(array_keys($users))
        );
    });
})();
