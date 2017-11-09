<?php

namespace go1\util\metric;

use Doctrine\DBAL\Connection;
use go1\clients\MqClient;
use go1\util\DB;
use go1\util\queue\Queue;

class MetricRepository
{
    private $db;
    private $queue;

    public function __construct(Connection $db, MqClient $queue)
    {
        $this->db = $db;
        $this->queue = $queue;
    }

    public function load(int $id)
    {
        return ($metric = $this->loadMultiple([$id]))
            ? $metric[0]
            : null;
    }

    public function loadMultiple(array $ids)
    {
        $metrics = $this->db
            ->executeQuery('SELECT * FROM metric WHERE id IN (?)', [$ids], [DB::INTEGERS])
            ->fetchAll(DB::OBJ);

        return array_map(function($_) {
            return Metric::create($_);
        }, $metrics);
    }

    public function create(Metric &$metric): int
    {
        $this->db->insert('metric', $metric->jsonSerialize());
        $metric->id = $this->db->lastInsertId('metric');
        $this->queue->publish($metric, Queue::METRIC_CREATE);

        return $metric->id;
    }

    public function update(Metric $metric): bool
    {
        if (!$original = $this->load($metric->id)) {
            return false;
        }

        $this->db->update('metric', $metric->jsonSerialize(), ['id' => $metric->id]);
        $metric->original = $original;
        $this->queue->publish($metric, Queue::METRIC_UPDATE);

        return true;
    }

    public function delete(int $id): bool
    {
        if (!$metric = $this->load($id)) {
            return false;
        }

        DB::transactional($this->db, function (Connection $db) use (&$metric) {
            $db->delete('metric', ['id' => $metric->id]);
            $this->queue->publish($metric, Queue::METRIC_DELETE);
        });

        return true;
    }
}
