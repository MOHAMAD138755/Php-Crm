<?php

namespace Assessment\Service;

use Assessment\Model\CustomerModel;
use Assessment\Model\FollowupModel;
use Assessment\Core\HttpException;
use DateTime;

class FollowupService
{
    protected FollowupModel $followupModel; protected CustomerModel $customerModel;

    public function __construct(FollowupModel $followupModel, CustomerModel $customerModel)
    {
        $this->followupModel = $followupModel;
        $this->customerModel = $customerModel;
    }

    public function create(array $data,array $user)
    {
        $fields = [
          'customer_id','title','description','priority','due_at'
        ];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new HttpException(422,"Field '$field' is required");
            }
        }

        $checkCustomerId = filter_var($data["customer_id"], FILTER_VALIDATE_INT);
        if ($checkCustomerId === false || $checkCustomerId < 1) {
            throw new HttpException(422,"Customer id must be an integer");
        }

        if (!$this->customerModel->isVisibleTo((int)$checkCustomerId,$user)){
            throw new HttpException(422,"Customer Not Found");
        }

        if (strlen($data['title']) > 150) {
            throw new HttpException(422,"Title must be less than 150 characters");
        }

        if (!is_string($data["title"]) || !is_string($data["description"]) || !is_string($data["priority"])) {
            throw new HttpException(422,"Field 'title' or 'description' must be a string");
        }

        if ($data["priority"] !== 'low' && $data["priority"] !== 'high' && $data["priority"] !== 'normal') {
            throw new HttpException(422,"Field 'priority' or 'normal' must be a string");
        }

        $checkDateTime = DateTime::createFromFormat("Y-m-d H:i:s", $data["due_at"]);
        if (!$checkDateTime) {
            throw new HttpException(422,"Field 'due_at' must be a valid date");
        }

        return $this->followupModel->create([
            'customer_id' => $checkCustomerId,
            'creator_id' => (int) $user['id'],
            'title' => $data["title"],
            'description' => $data["description"],
            'priority' => $data["priority"],
            'due_at' => $data["due_at"]
        ]);
    }

    public function index(array $data)
    {
        if (!isset($data['page']) && !isset($data['per_page'])) {
            return $this->followupModel->getAll();
        }

        $page = max(1,(int)$data['page'] ?? 1);
        $perPage = max(1,(int)$data['per_page'] ?? 10);
        return $this->followupModel->index($page,$perPage);
    }

    public function show(int $id)
    {
        return $this->followupModel->show($id);
    }
}