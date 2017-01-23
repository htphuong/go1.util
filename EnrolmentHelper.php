<?php

namespace go1\util;

use Doctrine\DBAL\Connection;

/**
 * @TODO We're going to load & attach edges into enrolment.
 *
 *  - assessor
 *  - expiration
 *  - ...
 *
 * Format will like:
 *  $enrolment->edges[edge-type][] = edge
 */
class EnrolmentHelper
{
    public static function load(Connection $db, int $id, bool $loadEdges = false)
    {
        return ($enrolments = static::loadMultiple($db, [$id])) ? $enrolments[0] : false;
    }

    public static function loadMultiple(Connection $db, array $ids, bool $loadEdges = false): array
    {
        return $db
            ->executeQuery('SELECT * FROM gc_enrolment WHERE id IN (?)', [$ids], [Connection::PARAM_INT_ARRAY])
            ->fetchAll(DB::OBJ);
    }

    public static function loadByParentLo(Connection $db, int $parentLoId, bool $loadEdges = false): array
    {
        return $db
            ->executeQuery('SELECT * FROM gc_enrolment WHERE parent_lo_id = ?', [$parentLoId])
            ->fetchAll(DB::OBJ);
    }

    public static function loadByLo(Connection $db, int $loId, bool $loadEdges = false): array
    {
        return $db
            ->executeQuery('SELECT * FROM gc_enrolment WHERE lo_id = ?', [$loId])
            ->fetchAll(DB::OBJ);
    }

    public static function loadByLoAndProfileId(Connection $db, int $loId, int $instanceId, int $profileId, bool $loadEdges = false)
    {
        return $db
            ->executeQuery('SELECT * FROM gc_enrolment WHERE lo_id = ? AND instance_id = ? AND profile_id = ?', [$loId, $instanceId, $profileId])
            ->fetch(DB::OBJ);
    }

    public static function becomeCompleted($enrolment, $original, $passAware = true): bool
    {
        $status = $enrolment->status;
        $previousStatus = $original->status;

        if ($status != $previousStatus) {
            if (EnrolmentStatuses::COMPLETED === $status) {
                return $passAware ? (1 == $enrolment->pass) : true;
            }
        }

        return false;
    }
}
