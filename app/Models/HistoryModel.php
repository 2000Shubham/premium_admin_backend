<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryModel extends Model
{
    protected $DBGroup = 'default';

    public function getCalculationHistory($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetCalculationHistory
                @PlanCode = ?,
                @Gender   = ?,
                @FromDate = ?,
                @ToDate   = ?,
                @Page     = ?,
                @PageSize = ?
        ";

        $query = $db->query($sql, [
            $data['PlanCode'] ?? null,
            $data['Gender'] ?? null,
            $data['FromDate'] ?? null,
            $data['ToDate'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch calculation history');
        }

        $summary = null;
        $rows    = [];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classify($row, $summary, $rows);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classify($row, $summary, $rows);
                }
            }
        }

        return [
            'summary' => $summary,
            'history' => $rows,
        ];
    }

    private function classify($row, &$summary, &$rows)
    {
        $row = $this->mapRow(is_array($row) ? $row : []);

        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (! empty($row['Id'])) {
            $rows[] = $row;
        }
    }

    private function mapRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        foreach (['CalculatedOn', 'FromDate', 'ToDate'] as $key) {
            if (isset($row[$key]) && $row[$key] instanceof \DateTimeInterface) {
                $row[$key] = $row[$key]->format('Y-m-d H:i:s');
            }
        }

        return $row;
    }
}
