<?php

namespace Assessment\Tests\Integration;

use Assessment\Model\FollowupModel;
use PDOException;

class FollowUpoModelTest extends DataBaseConnection
{
    public function test_Creation_a_FollowUps()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $data = [
            'customer_id' => 1,
            'creator_id' => 1,
            'title' => 'Integration Test Followup',
            'description' => 'Testing followup creation',
            'priority' => 'normal',
            'due_at' => '2026-12-20 10:00:00',
        ];

        $id = $followUpModel->create($data);
        $this->assertNotEmpty($id);

        $followUp = $followUpModel->findById($id);
        $this->assertIsArray($followUp);
        $this->assertSame('Integration Test Followup', $followUp['title']);
    }
    public function test_Not_Creation_For_Null_Customer_Id_FollowUps()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $data = [
            'customer_id' => null,
            'creator_id' => 1,
            'title' => 'Integration Test Followup',
            'description' => 'Testing followup creation',
            'priority' => 'normal',
            'due_at' => '2026-12-20 10:00:00',
        ];

        $this->expectException(PDOException::class);

        $followUpModel->create($data);
    }
    public function test_Not_Creation_For_Null_Creator_Id_FollowUps()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $data = [
            'customer_id' => 1,
            'creator_id' => null,
            'title' => 'Integration Test Followup',
            'description' => 'Testing followup creation',
            'priority' => 'normal',
            'due_at' => '2026-12-20 10:00:00',
        ];

        $this->expectException(PDOException::class);

        $followUpModel->create($data);
    }
    public function test_Not_Creation_FollowUps_With_Invalid_Creator_Id()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $data = [
            'customer_id' => 1,
            'creator_id' => 99999,
            'title' => 'Integration Test Followup',
            'description' => 'Testing followup creation',
            'priority' => 'normal',
            'due_at' => '2026-12-20 10:00:00',
        ];

        $this->expectException(PDOException::class);

        $followUpModel->create($data);
    }
}