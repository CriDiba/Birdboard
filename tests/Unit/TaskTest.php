<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
Use App\Task;
use App\Project;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_has_a_path()
    {
        $task = factory(Task::class)->create();

        $this->assertEquals('/projects/' . $task->project->id . '/tasks/' .  $task->id, $task->path());
    }

    /** @test */
    public function it_belongs_to_an_owner()
    {
        $task = factory(Task::class)->create();

        $this->assertInstanceOf('App\Project', $task->project);
    }


    /** @test */
    function it_can_be_completed()
    {
        $this->withoutExceptionHandling();

        $task = factory(Task::class)->create();

        $this->assertFalse($task->completed);

        $task->complete();

        $this->assertTrue($task->fresh()->completed);
    }


    /** @test */
    function it_can_be_incompleted()
    {
        $task = factory(Task::class)->create(['completed' => true]);

        $this->assertTrue($task->completed);

        $task->incomplete();

        $this->assertFalse($task->fresh()->completed);
    }

}
