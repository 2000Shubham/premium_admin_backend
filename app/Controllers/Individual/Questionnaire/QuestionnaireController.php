<?php

namespace App\Controllers\Individual\Questionnaire;

use App\Controllers\BaseController;
use App\Models\Individual\QuestionnaireModel;

class QuestionnaireController extends BaseController
{
    protected $format = 'json';

    public function getQuestionnaires()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $sectionId = $this->payloadValue($requestData, ['SectionId', 'sectionId']);

            $result = (new QuestionnaireModel())->getQuestionnaires([
                'Search'    => $this->payloadValue($requestData, ['Search', 'search']),
                'SectionId' => ($sectionId === null || $sectionId === '') ? null : (int) $sectionId,
                'Active'    => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'      => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'  => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'MAF questions fetched successfully',
                    'res_massage' => 'MAF questions fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['questionnaires'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getSectionDropdowns()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new QuestionnaireModel())->getSectionDropdowns([
                'Search' => $this->payloadValue($requestData, ['Search', 'search']),
                'Active' => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'MAF section dropdowns fetched successfully',
                    'res_massage' => 'MAF section dropdowns fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getQuestionnaireById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id = $this->payloadValue($requestData, ['Id', 'id']);
            if ($id === null || $id === '' || (int) $id <= 0) {
                return $this->validationError('Id is required');
            }

            $result = (new QuestionnaireModel())->getQuestionnaireById([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'MAF question fetched successfully',
                    'res_massage' => 'MAF question fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveQuestionnaire()
    {
        try {
            $payload = $this->questionPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new QuestionnaireModel())->saveQuestionnaire($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'MAF question created successfully',
                    'res_massage' => 'MAF question created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateQuestionnaire()
    {
        try {
            $payload = $this->questionPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new QuestionnaireModel())->updateQuestionnaire($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'MAF question updated successfully',
                    'res_massage' => 'MAF question updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deleteQuestionnaire()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id = $this->payloadValue($requestData, ['Id', 'id']);
            if ($id === null || $id === '' || (int) $id <= 0) {
                return $this->validationError('Id is required');
            }

            $result = (new QuestionnaireModel())->deleteQuestionnaire([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'MAF question deactivated successfully',
                    'res_massage' => 'MAF question deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function questionPayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id       = $this->payloadValue($requestData, ['Id', 'id']);
        $section  = $this->payloadValue($requestData, ['SectionId', 'sectionId']);
        $question = $this->payloadValue($requestData, ['QuestionText', 'questionText']);
        $flag     = $this->payloadValue($requestData, ['FieldFlag', 'fieldFlag']);
        $related  = $this->payloadValue($requestData, ['RelatedQuestionId', 'relatedQuestionId']);
        $order    = $this->payloadValue($requestData, ['DisplayOrder', 'displayOrder', 'Order', 'order'], 1);
        $active   = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('Id is required');
        }
        if ($section === null || $section === '' || (int) $section <= 0) {
            return $this->validationError('Section is required');
        }
        if ($question === null || $question === '') {
            return $this->validationError('Question text is required');
        }
        if ($flag === null || $flag === '') {
            return $this->validationError('Field flag is required');
        }

        return [
            'Id'                => $isUpdate ? (int) $id : null,
            'SectionId'         => (int) $section,
            'QuestionText'      => $question,
            'FieldFlag'         => $flag,
            'RelatedQuestionId' => ($related === null || $related === '') ? null : (int) $related,
            'DisplayOrder'      => (int) $order,
            'Active'            => $active === null ? 1 : $active,
        ];
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

    private function failResponse(\Exception $e)
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

    private function payloadValue(array $data, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== '' && $data[$key] !== null) {
                return $data[$key];
            }
        }

        return $default;
    }

    private function optionalBit($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value === true || $value === 1 || $value === '1') {
            return 1;
        }

        if ($value === false || $value === 0 || $value === '0') {
            return 0;
        }

        return null;
    }
}
