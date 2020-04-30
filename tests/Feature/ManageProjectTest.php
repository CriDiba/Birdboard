<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
Use App\Project;

//Classe dedicata alla creazione di progetti Test
//La direttiva Facades\ mi permette di usare i suoi metodi come fossero statici
use Facades\Tests\Setup\ProjectTestFactory;

//   .\vendor\bin\phpunit .\tests\Feature\ManageProjectTest.php
class ManageProjectTest extends TestCase
{

    use WithFaker, RefreshDatabase;

    /** @test */
    public function guest_cannot_manage_projects()
    {
        $project = factory('App\Project')->create(); 

        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
        $this->get($project->path() . '/edit')->assertRedirect('login');
        $this->post('/projects', $project->toArray())->assertRedirect('login');
        $this->delete($project->path())->assertRedirect('login');
    }

    /** @test */
    public function a_user_can_create_a_project()
    {
        $this->signIn();

        $this->get('/projects/create')->assertStatus(200);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => 'General notes here...'
        ];

        $response = $this->post('/projects', $attributes);

        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    
    /** @test */
    public function task_can_be_included_as_a__part_a_new_project_creation()
    {
        $this->signIn();

        $attributes = factory(Project::class)->raw();
        $attributes['tasks'] = [
            ['body' => 'Task 1'],
            ['body' => 'Task 2']
        ];

        $this->post('/projects', $attributes);

        $this->assertCount(2, Project::first()->tasks);
    }


    /** @test */
    public function a_user_can_see_all_project_they_have_been_invited_to_on_their_dashboard()
    {
        $user = $this->signIn();

        $project = ProjectTestFactory::create();
        $project->invite($user);

        $this->get('/projects')->assertSee($project->title);
    }


    /** @test */
    public function a_user_can_delete_a_project()
    {
        $project = ProjectTestFactory::create();

        $this->actingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', $project->only('id'));

    }

    /** @test */
    public function unauthorized_user_cannot_delete_projects()
    {
        $project = ProjectTestFactory::create();

        $this->delete($project->path())->assertRedirect('/login');

        $user = $this->signIn();
        $this->delete($project->path())->assertStatus(403);
        
        $project->invite($user);
        $this->actingAs($user)->delete($project->path())->assertStatus(403);
    }
    

    /** @test */
    public function a_user_can_update_a_project()
    {
        $project = ProjectTestFactory::create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = ['title' => 'Changed', 'description' => 'Changed', 'notes' => 'changed'])
            ->assertRedirect($project->path());

        $this->get($project->path() . '/edit')->assertOk();
       
        $this->assertDatabaseHas('projects', $attributes);
    }


    /** @test */
    public function a_user_can_update_a_project_general_notes()
    {
        $project = ProjectTestFactory::create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = ['notes' => 'changed'])
            ->assertRedirect($project->path());
       
        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $project = ProjectTestFactory::create();

        $this->actingAs($project->owner)
            ->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }


    /** @test */
    public function an_authenticated_user_cannot_view_the_project_of_others()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }



    /** @test */
    public function an_authenticated_user_cannot_update_the_project_of_others()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->patch($project->path(), [])->assertStatus(403);
    }

    
    /** @test */
    public function an_authenticated_user_cannot_delete_the_project_of_others()
    {
        $this->signIn();

        $project = ProjectTestFactory::create();

        $this->delete($project->path())->assertStatus(403);
    }



    /** @test */
    public function a_project_requires_a_title()
    {
        $this->signIn();

        //$attributes = factory('App\Project')->make(['title' => '']); --> crea e ritorna un oggetto
        $attributes = factory('App\Project')->raw(['title' => '']);    //--> crea e ritona un array
        
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');

    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->signIn();

        $attributes = factory('App\Project')->raw(['description' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');

    }

}
