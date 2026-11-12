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
}