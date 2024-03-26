<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class WorkstationResourceService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function resetTable(): void
    {
        $this->connection->executeStatement(
            'UPDATE workstation_resource wr
                JOIN workstation w ON wr.workstation_id = w.id
                SET wr.free_ram = w.total_ram,
                    wr.free_cpu = w.total_cpu'
        );

        $this->connection->executeStatement(
            'UPDATE process SET workstation_id = NULL'
        );
    }
}
