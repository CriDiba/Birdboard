<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Project;
use Facades\Tests\Setup\ProjectTestFactory;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function guest_cannot_add_task_to_projects()
    {
        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks',)->assertRedirect('login'); 
    }



    /** @test */
    public function only_the_owner_of_a_project_may_add_tasks()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);
    }

    /** @test */
    public function only_the_owner_of_a_project_may_update_a_tasks()
    {
        $this->signIn();

        $project = ProjectTestFactory::withTasks(1)->create();

        $this->patch($project->tasks[0]->path(), ['body' => 'changed'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);            
    }


    /** @test */
    public function a_project_can_have_tasks()
    {
        $project = ProjectTestFactory::create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    /** @test */
    public function a_task_can_be_updated()
    {
        //$this->signIn();
        // $project = auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );
        // $task = $project->addTasK('test task');

        // Semplifico i miei test con una ProjectTestFactory
        $project = ProjectTestFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'changed'
            ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed'
        ]);
    }


    /** @test */
    public function a_task_can_be_completed()
    {
        $project = ProjectTestFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'changed',
                'completed' => true
            ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => true
        ]);
    }

    /** @test */
    public function a_task_can_be_marked_as_incompleted()
    {
        $project = ProjectTestFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'changed',
                'completed' => true
            ]);


        $this->patch($project->tasks[0]->path(), [
                'body' => 'changed',
                'completed' => false
        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => false
        ]);
    }


    /** @test */
    public function a_task_requires_a_body()
    {
        $project = ProjectTestFactory::create();

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $attributes)
            ->assertSessionHasErrors('body');
    }

    
}
