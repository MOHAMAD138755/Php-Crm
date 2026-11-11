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
}