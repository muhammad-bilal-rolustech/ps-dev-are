<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\Bean\Visibility\Strategy\TeamSecurity\Denorm\Listener;

use Sugarcrm\Sugarcrm\Bean\Visibility\Strategy\TeamSecurity\Denorm\Listener;
use Sugarcrm\Sugarcrm\Dbal\Connection;

/**
 * Updates denormalized data set with the changes made to the original data
 */
final class Updater implements Listener
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var string
     */
    private $table;

    /**
     * Constructor
     *
     * @param Connection $conn
     * @param string $table The name of the table to be updated
     */
    public function __construct(Connection $conn, $table)
    {
        $this->conn = $conn;
        $this->table = $table;
    }

    /**
     * {@inheritDoc}
     *
     * For every user which belongs to any of the teams in the team, create a record with team set ID and user ID
     * ignoring already existing records.
     */
    public function teamSetCreated($teamSetId, array $teamIds)
    {
        $query = $this->query(
            <<<'SQL'
INSERT INTO %1$s (team_set_id, user_id)
SELECT DISTINCT ?,
       user_id
  FROM team_memberships tm
 WHERE team_id IN(?)
   AND deleted = 0
   AND NOT EXISTS (
    SELECT NULL
      FROM %1$s
     WHERE team_set_id = ?
       AND user_id = tm.user_id
)
SQL
        );

        $this->conn->executeUpdate($query, [
            $teamSetId,
            $teamIds,
            $teamSetId,
        ], [
            null,
            Connection::PARAM_STR_ARRAY,
            null,
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * Update all records containing the old ID with the new one
     */
    public function teamSetReplaced($teamSetId, $replacementId)
    {
        $query = $this->query(
            <<<'SQL'
DELETE FROM %1$s
 WHERE team_id = ?
   AND EXISTS (
    SELECT NULL
      FROM %1$s
     WHERE team_id = ?
       AND user_id = user_id
)
SQL
        );

        $this->conn->executeUpdate($query, [$teamSetId, $replacementId]);

        $query = $this->query(
            <<<'SQL'
UPDATE %1$s
   SET team_id = ?
 WHERE team_id = ?
SQL
        );

        $this->conn->executeUpdate($query, [$replacementId, $teamSetId]);
    }

    /**
     * {@inheritDoc}
     *
     * Delete all records with the given team set ID.
     */
    public function teamSetDeleted($teamSetId)
    {
        $query = $this->query(
            <<<SQL
DELETE FROM %s WHERE team_set_id = ?
SQL
        );

        $this->conn->executeUpdate($query, [$teamSetId]);
    }

    /**
     * {@inheritDoc}
     *
     * For every team set which the given team belongs to and the given user,
     * create a record ignoring already existing records.
     */
    public function userAddedToTeam($userId, $teamId)
    {
        $query = $this->query(
            <<<'SQL'
INSERT INTO %1$s (team_set_id, user_id)
SELECT tst.team_set_id,
       ?
  FROM team_sets_teams tst
 WHERE tst.team_id = ?
   AND tst.deleted = 0
   AND NOT EXISTS (
    SELECT NULL
      FROM %1$s
     WHERE team_set_id = tst.team_set_id
       AND user_id = ?
)
SQL
        );

        $this->conn->executeUpdate($query, [
            $userId,
            $teamId,
            $userId,
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * For every team set which the given team belongs to only by means of the given team,
     * remove corresponding record.
     *
     * Currently, instead of the above, removes the records corresponding to the team sets
     * which the user doesn't belong to (i.e. w/o using the actual team ID)
     */
    public function userRemovedFromTeam($userId, $teamId)
    {
        $query = $this->query(
            <<<SQL
DELETE FROM %s
 WHERE user_id = ?
   AND team_set_id NOT IN (
    SELECT tst.team_set_id
      FROM team_sets_teams tst
INNER JOIN team_memberships tm
        ON tm.team_id = tst.team_id
     WHERE tm.user_id = user_id
       AND tm.deleted = 0
    )
SQL
        );

        $this->conn->executeUpdate($query, [$userId]);
    }

    /**
     * Builds a query from template by replacing placeholder with the actual table name
     *
     * @param string$template
     * @return string
     */
    private function query($template)
    {
        return sprintf($template, $this->table);
    }
}
