<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class PlanDropdownModel extends Model
{
    protected $DBGroup = 'default';

    public function getPlanDropdowns($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetIndividualPlanDropdowns
                @Search = ?,
                @Active = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['Active'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch plan dropdowns');
        }

        $rows = [];
        foreach ($query->getResultArray() ?: [] as $row) {
            $rows[] = $this->mapRow($row);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $rows[] = $this->mapRow($row);
                }
            }
        }

        return $rows;
    }

    private function mapRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        if (array_key_exists('Active', $row)) {
            $row['Active'] = ((int) $row['Active']) === 1;
        }

        return $row;
    }
}
