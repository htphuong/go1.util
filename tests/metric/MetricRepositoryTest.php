<?php

namespace go1\util\tests\location;

use go1\util\metric\Metric;
use go1\util\metric\MetricRepository;
use go1\util\metric\MetricType;
use go1\util\tests\UtilTestCase;
use go1\util\location\Location;
use go1\util\queue\Queue;

class MetricRepositoryTest extends UtilTestCase
{
    protected $repo;
    protected $metric;

    public function setUp()
    {
        parent::setUp();

        $this->repo = new MetricRepository($this->db, $this->queue);

        $this->metric = [
            'type'        => MetricType::NEW_ARR,
            'value'       => 78.99,
            'user_id'     => 10,
            'start_date'  => strtotime('-1 week'),
            'description' => 'Foo',
            'created'     => time(),
            'updated'     => time(),
        ];
    }

    public function testCreate()
    {
        $metricOriginal = Metric::create((object)$this->metric);
        $id = $this->repo->create($metricOriginal);

        $metric = $this->repo->load($id)->jsonSerialize();
        foreach ($this->metric as $k => $v) {
            $this->assertEquals($metric[$k], $v);
        }

        $this->assertCount(1, $this->queueMessages[Queue::METRIC_CREATE]);
        $this->messageAware($this->queueMessages[Queue::METRIC_CREATE][0]);

        return $id;
    }

    public function testUpdate()
    {
        $metric = $this->repo->load($id = $this->testCreate());
        $metric->value = 900;

        $this->assertTrue($this->repo->update($metric));

        $metric = $this->repo->load($id);
        $this->assertEquals(900, $metric->value);
        $this->assertCount(1, $this->queueMessages[Queue::METRIC_UPDATE]);
        $this->messageAware($this->queueMessages[Queue::METRIC_UPDATE][0]);
        $this->messageAware($this->queueMessages[Queue::METRIC_UPDATE][0]->original);
    }

    public function testDelete()
    {
        $metric = $this->repo->load($id = $this->testCreate());
        $this->assertTrue($metric instanceof Metric);

        $this->repo->delete($id);
        $this->assertNull($this->repo->load($id));
        $this->assertCount(1, $this->queueMessages[Queue::METRIC_DELETE]);
        $this->messageAware($this->queueMessages[Queue::METRIC_DELETE][0]);
    }

    protected function messageAware(Metric $message)
    {
        $message = $message->jsonSerialize();
        foreach ($this->metric as $k => $v) {
            $this->assertArrayHasKey($k, $message);
        }
    }
}
