<?php

namespace App\Controllers\Age_band;

use App\Controllers\BaseController;
use App\Models\AgeBandModel;

class AgeBandController extends BaseController
{
    protected $format = 'json';

    public function getAgeBands()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getGet() ?: $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $search   = $this->payloadValue($requestData, ['Search', 'search', 'q']);
            $page     = (int) ($this->payloadValue($requestData, ['Page', 'page'], 1));
            $pageSize = (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5));

            $model  = new AgeBandModel();
            $result = $model->getAgeBands([
                'Search'   => $search,
                'Page'     => $page,
                'PageSize' => $pageSize,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Age bands fetched successfully',
                    'res_massage' => 'Age bands fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['ageBands'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getAgeBandById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id   = $this->payloadValue($requestData, ['AgeBandId', 'ageBandId', 'Id', 'id']);
            $code = $this->payloadValue($requestData, ['Code', 'code', 'AgeBandCode']);

            if (($id === null || $id === '') && ($code === null || $code === '')) {
                return $this->validationError('AgeBandId or Code is required');
            }

            $model  = new AgeBandModel();
            $result = $model->getAgeBandById([
                'AgeBandId' => ($id === null || $id === '') ? null : (int) $id,
                'Code'      => $code,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Age band fetched successfully',
                    'res_massage' => 'Age band fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveAgeBand()
    {
        try {
            $payload = $this->ageBandPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new AgeBandModel();
            $result = $model->saveAgeBand($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Age band created successfully',
                    'res_massage' => 'Age band created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateAgeBand()
    {
        try {
            $payload = $this->ageBandPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new AgeBandModel();
            $result = $model->updateAgeBand($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Age band updated successfully',
                    'res_massage' => 'Age band updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function ageBandPayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $code    = $this->payloadValue($requestData, ['Code', 'code', 'AgeBandCode']);
        $label   = $this->payloadValue($requestData, ['Label', 'label', 'AgeBandLabel']);
        $fromAge = $this->payloadValue($requestData, ['FromAge', 'fromAge', 'AgeFrom', 'min']);
        $toAge   = $this->payloadValue($requestData, ['ToAge', 'toAge', 'AgeTo', 'max']);
        $id      = $this->payloadValue($requestData, ['AgeBandId', 'ageBandId', 'Id', 'id']);

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('AgeBandId is required');
        }
        if ($code === null || $code === '') {
            return $this->validationError('Code is required');
        }
        if ($label === null || $label === '') {
            return $this->validationError('Label is required');
        }
        if ($fromAge === null || $fromAge === '') {
            return $this->validationError('From age is required');
        }

        return [
            'AgeBandId' => $isUpdate ? (int) $id : null,
            'Code'      => $code,
            'Label'     => $label,
            'FromAge'   => (int) $fromAge,
            'ToAge'     => ($toAge === null || $toAge === '') ? null : (int) $toAge,
        ];
    }

    private function payloadValue(array $data, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== '' && $data[$key] !== null) {
                return $data[$key];
            }
        }

        return $default;
    }

    private function validationError(string $message)
    {
        return $this->response->setStatusCode(400)
            ->setJSON([
                'res_code'    => 400,
                'status'      => false,
                'message'     => $message,
                'res_massage' => $message,
            ]);
    }

    private function failResponse(\Throwable $e)
    {
        $message = $e->getMessage();
        if (preg_match('/\[SQL Server\](.*)$/s', $message, $matches)) {
            $message = trim($matches[1]);
        }

        return $this->response->setStatusCode(400)
            ->setJSON([
                'res_code'    => 400,
                'status'      => false,
                'message'     => $message,
                'res_massage' => $message,
            ]);
    }
}
