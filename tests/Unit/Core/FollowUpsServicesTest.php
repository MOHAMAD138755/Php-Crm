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
            'status' => 'open',
            'creator_id' => 2,
        ];
        $user = [
            'id' => 2,
            'role' => 'agent'
        ];

        $this->followUpModel->expects($this->once())->method('findById')->with($id)->willReturn($data);
        $this->followUpModel->expects($this->once())->method('show')->with($id)->willReturn($data);
        $result = $this->followupService->show($id,$user);

        $this->assertEquals($data,$result);
    }
    public function test_FollowUps_Services_Show_Failure(): void
    {
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];
        $id = 9999;

        $this->followUpModel->expects($this->once())->method('findById')->with($id)->willReturn(null);
        $this->followUpModel->expects($this->never())->method('show')->with($id)->willReturn(null);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Follow up not found');

        $result = $this->followupService->show($id,$user);
    }

    public function test_FollowUps_Services_Index_Successfully(): void
    {
        $pages = [
            'page' => 1,
            'per_page' => 3,
        ];
        $user = [
            'id' => 1,
            'role' => 'admin'
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
        $result = $this->followupService->index($pages,$user);

        $this->assertEquals($data,$result);
    }
    public function test_FollowUps_Services_Index_Priority_Failure(): void
    {
        $pages = [
            'page' => 1,
            'per_page' => 3,
        ];
        $user = [
            'id' => 1,
            'role' => 'admin'
        ];
        $_GET['priority'] = 'failure priority';

        $this->followUpModel->expects($this->never())->method('index');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Priority must be low, normal or high');

        $this->followupService->index($pages,$user);
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

    public function test_FollowUps_Services_Complete_Successfully(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $oldData = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'open'
        ];
        $newData = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'completed'
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);

        $this->followUpModel->expects($this->exactly(2))->method('findById')->with($followUpId)
            ->willReturnOnConsecutiveCalls($oldData,$newData);
        $this->followUpModel->expects($this->once())->method('completed')->with($followUpId);

        $result = $this->followupService->complete($followUpId,$user);

        $this->assertEquals($result,$newData);
    }
    public function test_FollowUps_Services_Complete_Failure_Not_Open(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $data = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'completed'
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)
            ->willReturn($data);
        $this->followUpModel->expects($this->never())->method('completed')->with($followUpId);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Followup Is Not Open');

        $this->followupService->complete($followUpId,$user);
    }
    public function test_FollowUps_Services_Complete_Failure_Not_Found_Customer(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $data = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'open'
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(false);

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)
            ->willReturn($data);
        $this->followUpModel->expects($this->never())->method('completed')->with($followUpId);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Customer Id Not Found');

        $this->followupService->complete($followUpId,$user);
    }
    public function test_FollowUps_Services_Complete_Failure_Not_Found_FollowUp_Id(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)
            ->willReturn(null);

        $this->customerModel->expects($this->never())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);
        $this->followUpModel->expects($this->never())->method('completed')->with($followUpId);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Followup Not Found');

        $this->followupService->complete($followUpId,$user);
    }

    public function test_FollowUps_Services_Reopen_Successfully(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $oldData = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'completed'

        ];
        $newData = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'open'
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);

        $this->followUpModel->expects($this->exactly(2))->method('findById')->with($followUpId)
            ->willReturnOnConsecutiveCalls($oldData,$newData);
        $this->followUpModel->expects($this->once())->method('reopen')->with($followUpId);

        $result = $this->followupService->reopen($followUpId,$user);

        $this->assertEquals($result,$newData);
    }
    public function test_FollowUps_Services_Reopen_Failure_Not_Found_Customer(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $data = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'completed'

        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(false);

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)
            ->willReturn($data);
        $this->followUpModel->expects($this->never())->method('reopen')->with($followUpId);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Customer Id Not Found');

        $this->followupService->reopen($followUpId,$user);

    }
    public function test_FollowUps_Services_Reopen_Failure_Not_Complete(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $data = [
            'id' => 5,
            'customer_id' => 10,
            'status' => 'open'
        ];

        $this->customerModel->expects($this->once())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)
            ->willReturn($data);
        $this->followUpModel->expects($this->never())->method('reopen')->with($followUpId);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Followup Has Open');

        $this->followupService->reopen($followUpId,$user);
    }
    public function test_FollowUps_Services_Reopen_Failure_Not_Found_FollowUp_Id(): void
    {
        $followUpId = 5;
        $user = [
            'id' => 2,
            'role' => 'agent',
        ];

        $this->followUpModel->expects($this->once())->method('findById')->with($followUpId)
            ->willReturn(null);

        $this->customerModel->expects($this->never())->method('isVisibleTo')->with(10, $user)
            ->willReturn(true);
        $this->followUpModel->expects($this->never())->method('reopen')->with($followUpId);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Followup Not Found');

        $this->followupService->reopen($followUpId,$user);
    }


}