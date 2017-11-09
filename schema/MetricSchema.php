<?php

namespace go1\util\schema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

class MetricSchema
{
    public static function install(Schema $schema)
    {
        if (!$schema->hasTable('metric')) {
            $activity = $schema->createTable('metric');
            $activity->addColumn('id', Type::INTEGER, ['unsigned' => true, 'autoincrement' => true]);
            $activity->addColumn('type', Type::SMALLINT);
            $activity->addColumn('value', Type::FLOAT);
            $activity->addColumn('user_id', Type::INTEGER, ['unsigned' => true, 'notnull' => false]);
            $activity->addColumn('start_date', Type::DATETIME);
            $activity->addColumn('description', Type::TEXT);
            $activity->addColumn('created', Type::INTEGER, ['unsigned' => true]);
            $activity->addColumn('updated', Type::INTEGER, ['unsigned' => true]);
            $activity->setPrimaryKey(['id']);
            $activity->addIndex(['type']);
            $activity->addIndex(['value']);
            $activity->addIndex(['user_id']);
            $activity->addIndex(['start_date']);
            $activity->addIndex(['created']);
            $activity->addIndex(['updated']);
        }
    }
}
