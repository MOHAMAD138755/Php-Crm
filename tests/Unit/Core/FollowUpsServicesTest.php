<?php

namespace Assessment\Tests\Unit\Core;

use Assessment\Core\HttpException;
use Assessment\Model\CustomerModel;
use Assessment\Model\FollowupModel;
use Assessment\Service\FollowupService;
use PHPUnit\Framework\TestCase;

class FollowUpsServicesTest extends TestCase
{
    private $followupService;
    private $followUpModel;
    private $customerModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->followUpModel = $this->createMock(FollowupModel::class);
        $this->customerModel = $this->createMock(CustomerModel::class);

        $this->followupService = new FollowupService($this->followUpModel, $this->customerModel);
    }

    public function test_FollowUps_Services_Creation_Successfully(): void
    {
        $data = ['customer_id' => 10, 'title' => 'Call customer','creator_id' => 2,
            'description' => 'Call customer about the new offer',
            'priority' => 'high',
            'due_at' => '2026-11-15 10:30:00'
        ];

        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);
        $this->followUpModel->expects($this->once())->method('create')->with($data)
            ->willReturn($data);

        $result = $this->followupService->create($data,$user);

        $this->assertEquals($result,$data);
    }
    public function test_FollowUps_Services_Creation_Failure(): void
    {
        $data = ['customer_id' => 10, 'title' => 'Call customer','creator_id' => 2,
            'description' => 'Call customer about the new offer',
            'priority' => 'high',
            'due_at' => '2026-11-15 10:30:00'
        ];

        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(false);
        $this->followUpModel->expects($this->never())->method('create');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Customer Not Found');

        $this->followupService->create($data,$user);
    }

    public function test_FollowUps_Services_Show_Successfully(): void
    {
        $id = 1;
        $data = [
            'id' => 1,
            'customer_id' => 10,
            'title' => 'Call customer',
            'status' => 'open'
        ];

        $this->followUpModel->expects($this->once())->method('show')->with($id)->willReturn($data);
        $result = $this->followupService->show($id);

        $this->assertEquals($data,$result);
    }
    public function test_FollowUps_Services_Show_Failure(): void
    {
        $id = 9999;

        $this->followUpModel->expects($this->once())->method('show')->with($id)->willReturn(null);
        $result = $this->followupService->show($id);

        $this->assertNull($result);
    }

    public function test_FollowUps_Services_Index_Successfully(): void
    {
        $pages = [
            'page' => 1,
            'per_page' => 3,
        ];

        $data = [
            [
                'id' => 1,
                'title' => 'Call customer',
                'priority' => 'high'
            ],
            [
                'id' => 2,
                'title' => 'Send email',
                'priority' => 'normal'
            ]
        ];

        $this->followUpModel->expects($this->once())->method('index')->with($pages['page'],$pages['per_page'],[])
            ->willReturn($data);
        $result = $this->followupService->index($pages);

        $this->assertEquals($data,$result);
    }
    public function test_FollowUps_Services_Index_Priority_Failure(): void
    {
        $pages = [
            'page' => 1,
            'per_page' => 3,
        ];
        $_GET['priority'] = 'failure priority';

        $this->followUpModel->expects($this->never())->method('index');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Priority must be low, normal or high');

        $this->followupService->index($pages);
    }

    public function test_FollowUps_Services_Update_Successfully(): void
    {
        $followUpId = 5;
        $data = ['title' => 'Call customer',
            'description' => 'Call customer about the new offer',
            'priority' => 'low',
            'due_at' => '2026-11-15 10:30:00'
        ];

        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)->willReturn([
            'id' => 5,
            'customer_id' => 10,
            'status' => 'open'
        ]);
        $this->followUpModel->expects($this->once())->method('update')->with($followUpId,$data)->willReturn($data);

        $result = $this->followupService->update($followUpId,$data,$user);

        $this->assertEquals($result,$data);
    }
    public function test_FollowUps_Services_Update_Failure(): void
    {
        $followUpId = 5;
        $data = ['title' => 'Call customer',
            'description' => 'Call customer about the new offer',
            'priority' => 'low',
            'due_at' => '2026-11-15 10:30:00'
        ];

        $user = [
            'id' => 2,
            'role' => 'agent',
        ];


        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(false);

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)->willReturn([
            'id' => 5,
            'customer_id' => 10,
            'status' => 'open'
        ]);
        $this->followUpModel->expects($this->never())->method('update');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Customer Id Not Found');

        $this->followupService->update($followUpId,$data,$user);
    }
}