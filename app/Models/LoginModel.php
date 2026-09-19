<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $DBGroup = 'default';

    public function login($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_Login
                @Email    = ?,
                @Password = ?
        ";

        $query = $db->query($sql, [
            $data['Email'] ?? null,
            $data['Password'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to login');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapUserRow($row ?: []);
    }

    private function mapUserRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        unset($row['PasswordHash'], $row['PasswordSalt'], $row['passwordHash'], $row['passwordSalt']);

        $row['Id']       = $row['Id'] ?? $row['UserId'] ?? $row['id'] ?? null;
        $row['Email']    = $row['Email'] ?? $row['email'] ?? null;
        $row['Name']     = $row['Name'] ?? $row['name'] ?? null;
        $row['IsActive'] = $row['IsActive'] ?? $row['isActive'] ?? null;
        $row['Status']   = $row['Status'] ?? $row['status'] ?? null;

        foreach (['LastLoginOn', 'CreatedOn', 'lastLoginOn', 'createdOn'] as $key) {
            if (isset($row[$key]) && $row[$key] instanceof \DateTimeInterface) {
                $row[$key] = $row[$key]->format('Y-m-d H:i:s');
            }
        }

        return $row;
    }
}
