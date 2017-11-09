<?php
namespace go1\util\metric;

use JsonSerializable;
use stdClass;

class Metric implements JsonSerializable
{
    public $id;
    public $type;
    public $value;
    public $userId;
    public $startDate;
    public $description;
    public $created;
    public $updated;
    public $original;

    public static function create(stdClass $input): Metric
    {
        $location = new Metric;
        $location->id = $input->id ?? null;
        $location->type = $input->type ?? null;
        $location->value = $input->value ?? null;
        $location->userId = $input->user_id ?? null;
        $location->startDate = $input->start_date ?? null;
        $location->description = $input->description ?? null;
        $location->created = $input->created ?? null;
        $location->updated = $input->updated ?? null;

        return $location;
    }

    public function jsonSerialize()
    {
        $array = [
            'id'          => $this->id,
            'type'        => $this->type,
            'value'       => $this->value,
            'user_id'     => $this->userId,
            'start_date'  => $this->startDate,
            'description' => $this->description,
            'created'     => $this->created,
            'updated'     => $this->updated,
        ];

        if ($this->original) {
            $array['original'] = $this->original;
        }

        return $array;
    }
}
