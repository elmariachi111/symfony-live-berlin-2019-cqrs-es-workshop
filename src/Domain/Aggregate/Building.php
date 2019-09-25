<?php


namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Building\Domain\DomainEvent\UserCheckedOut;
use Exception;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;
use function var_dump;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $checkins = [];

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkins() : array {
        return $this->checkins;
    }

    public function checkInUser(string $username)
    {
        $this->recordThat(UserCheckedIn::toBuilding($this->uuid, $username));
    }

    public function checkOutUser(string $username)
    {
        if ( !isset($this->checkins[$username]) ) {
            throw new Exception("you're not checked in");
        }

        $this->recordThat(UserCheckedOut::ofBuilding($this->uuid, $username));
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = Uuid::fromString($event->aggregateId());
        $this->name = $event->name();
    }

    protected function whenUserCheckedIn(UserCheckedIn $event)
    {
      $this->checkins[$event->username()] = 1;
    }

    protected function whenUserCheckedout(UserCheckedOut $event) {
        unset ($this->checkins[$event->username()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }
}
