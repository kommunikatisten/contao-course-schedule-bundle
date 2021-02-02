<?php


namespace Kommunikatisten\ContaoScheduleBundle\Entity;


class Room extends AbstractEntity {

    private string $name = '';

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }


    public function serialize(bool $encode) {
        $serialized = [
            'room_id' => $this->getId(),
            'room_name' => $this->name,
            'room_last_modified' => $this->getLastModified()
        ];
        return $encode ? json_encode($serialized) : $serialized;
    }

    static function apply(array $values, bool $cascade = true): Room {
        $room = new Room();
        if(!empty($values['room_id'])) {
            $room->id = intval($values['room_id']);
        }
        if (!empty($values['room_last_modified'])) {
            $room->lastModified = $values['room_last_modified'];
        }
        if (!empty($values['room_name'])) {
            $room->name = $values['room_name'];
        }
        return $room;
    }

    function merge(array $array, bool $cascade = true): void {
        ; // nothing to do
    }

    static function merging(array $values, array &$target, bool $cascade) {
        if (!empty($values['room_id'])) {
            $found = array_filter($target, function (string $entity) use ($values): bool {
                return $values['room_id'] === $entity->getId();
            });
            if (empty($found)) {
                $target[] = Room::apply($values, $cascade);
            } elseif ($cascade) {
                // should by this room => do nothing
                $idx = array_keys($found)[0];
                $found[$idx]->merge($values, $cascade);
            }
        }
    }
}
