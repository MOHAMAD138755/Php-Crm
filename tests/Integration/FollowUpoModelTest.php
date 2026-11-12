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

    public function test_Index_Method_FollowUps_For_Get_List_By_Pagination()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $pages = [
            'page' => 1,
            'per_page' => 3,
        ];

        $followups = $followUpModel->index($pages['page'], $pages['per_page']);
        $this->assertIsArray($followups);
        $this->assertCount(3, $followups);
    }
    public function test_Index_Method_FollowUps_For_Get_List_By_Creator_Id_Filters()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $pages = [
            'page' => 1,
            'per_page' => 3,
        ];
        $filter = ['creator_id' => 1];

        $followups = $followUpModel->index($pages['page'], $pages['per_page'],$filter);

        foreach ($followups as $followup) {
            $this->assertSame(1,(int)$followup['creator_id']);
        }

        $this->assertIsArray($followups);
        $this->assertNotEmpty($followups);
    }
    public function test_Index_Method_FollowUps_For_Get_List_By_Priority_Filters()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $pages = [
            'page' => 1,
            'per_page' => 3,
        ];
        $filter = ['priority' => 'normal'];

        $followups = $followUpModel->index($pages['page'], $pages['per_page'],$filter);

        foreach ($followups as $followup) {
            $this->assertSame('normal',$followup['priority']);
        }

        $this->assertIsArray($followups);
        $this->assertNotEmpty($followups);
    }

    public function test_Show_Method_FollowUps_By_Check_Id()
    {
        $followUpModel = new FollowUpModel($this->pdo);
        $followUpId = 1;

        $followUp =  $followUpModel->show($followUpId);
        $this->assertIsArray($followUp);
        $this->assertNotEmpty($followUp);
        $this->assertSame($followUpId, (int)$followUp[0]['id']);
    }
    public function test_Show_Method_FollowUps_By_Invalid_Id()
    {
        $followUpModel = new FollowUpModel($this->pdo);
        $followUpId = 9999;

        $followUp = $followUpModel->show($followUpId);
        $this->assertEmpty($followUp);
    }

    public function test_Update_a_FollowUp()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $data = [
            'title' => 'Integration Test Followup',
            'description' => 'Testing followup creation',
            'priority' => 'high',
            'due_at' => '2026-12-20 10:00:00',
        ];
        $followUpId = 1;

        $updatedFollowUp = $followUpModel->update($followUpId, $data);
        $this->assertIsArray($updatedFollowUp);
        $this->assertNotEmpty($updatedFollowUp);
        $this->assertSame($followUpId, $updatedFollowUp['id']);
        $this->assertSame($data['title'], $updatedFollowUp['title']);
        $this->assertSame($data['description'], $updatedFollowUp['description']);
        $this->assertSame($data['priority'], $updatedFollowUp['priority']);
    }
    public function test_Update_a_FollowUp_With_Invalid_Id()
    {
        $followUpModel = new FollowUpModel($this->pdo);

        $data = [
            'title' => 'Integration Test Followup',
            'description' => 'Testing followup creation',
            'priority' => 'high',
            'due_at' => '2026-12-20 10:00:00',
        ];
        $followUpId = 9999;

        $updatedFollowUp = $followUpModel->update($followUpId, $data);
        $this->assertEmpty($updatedFollowUp);
    }

    public function test_FindById_Method()
    {
        $followUpModel = new FollowUpModel($this->pdo);
        $followUpId = 1;

        $followUp = $followUpModel->findById($followUpId);
        $this->assertNotEmpty($followUp);
        $this->assertIsArray($followUp);
    }
    public function test_FindById_Method_With_Invalid_Id()
    {
        $followUpModel = new FollowUpModel($this->pdo);
        $followUpId = 9999;

        $followUp = $followUpModel->findById($followUpId);
        $this->assertFalse($followUp);
    }
}