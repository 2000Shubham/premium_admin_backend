<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class QuestionnaireModel extends Model
{
    protected $DBGroup = 'default';

    public function getQuestionnaires($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetQuestionnaires
                @Search    = ?,
                @SectionId = ?,
                @Active    = ?,
                @Page      = ?,
                @PageSize  = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['SectionId'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch questionnaires');
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
            'summary'        => $summary,
            'questionnaires' => $rows,
        ];
    }

    public function getSectionDropdowns($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetQuestionnaireSectionDropdowns
                @Search = ?,
                @Active = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['Active'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch section dropdowns');
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

    public function getQuestionnaireById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetQuestionnaireById
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch questionnaire');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapRow($row ?: []);
    }

    public function saveQuestionnaire($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveQuestionnaire
                @Id                = ?,
                @SectionId         = ?,
                @QuestionText      = ?,
                @FieldFlag         = ?,
                @RelatedQuestionId = ?,
                @DisplayOrder      = ?,
                @Active            = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['SectionId'] ?? null,
            $data['QuestionText'] ?? null,
            $data['FieldFlag'] ?? null,
            $data['RelatedQuestionId'] ?? null,
            $data['DisplayOrder'] ?? 1,
            $data['Active'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save questionnaire');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapRow($row ?: []);
    }

    public function updateQuestionnaire($data)
    {
        return $this->saveQuestionnaire($data);
    }

    public function deleteQuestionnaire($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteQuestionnaire
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete questionnaire');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapRow($row ?: []);
    }

    private function classify($row, &$summary, &$rows)
    {
        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (! empty($row['Id'])) {
            $rows[] = $this->mapRow($row);
        }
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
