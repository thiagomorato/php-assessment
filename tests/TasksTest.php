<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TasksTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $this->get('/tasks');

        $this->assertEquals($this->response->status(), 404);
        $text = '"Wow. You have nothing else to do. Enjoy the rest of your day!"';
        $this->assertEquals($this->response->getContent(), $text);

        $task = factory('App\Task')->create();
        $task = factory('App\Task')->create();
        $task = factory('App\Task')->create();

        $this->get('/tasks');

        $this->assertEquals($this->response->status(), 200);

        $response = json_decode($this->response->getContent());
        $this->assertEquals(count($response), 3);
    }

    public function testShow()
    {
        $task = factory('App\Task')->create();
        $this->get('/tasks/'.$task->id);

        $this->assertEquals($this->response->status(), 200);

        $this->get('/tasks/'.($task->id+1));
        $text = '"Are you a hacker or something? The task you were trying to see doesn\'t exist."';

        $this->assertEquals($this->response->status(), 404);
        $this->assertEquals($this->response->getContent(), $text);
    }

    public function testCreate()
    {
        $task = factory('App\Task')->create();
        $nextId = $task->id+1;

        $this->post('/tasks', ['type' => 'asdf', 'content' => 'asdf']);
        $text = '"The task type you provided is not supported. You can only use shopping or work."';

        $this->assertEquals($this->response->status(), 400);
        $this->assertEquals($this->response->getContent(), $text);

        $this->post('/tasks', ['type' => 'work', 'content' => '']);
        $text = '"Bad move! Try removing the task instead of deleting its content."';

        $this->assertEquals($this->response->status(), 400);
        $this->assertEquals($this->response->getContent(), $text);

        $task = App\Task::find($nextId);
        $this->assertNull($task);

        $this->post('/tasks', ['type' => 'work', 'content' => 'hard']);

        $this->assertEquals($this->response->status(), 200);
        $response = json_decode($this->response->getContent());

        $task = App\Task::find($nextId);
        $this->assertNotNull($task);

        $this->assertEquals($response->uuid, $task->id);
    }

    public function testUpdate()
    {
        $task = factory('App\Task')->create(['type' => 'shopping', 'content' => 'meat']);
        $nextId = $task->id+1;

        $this->patch('/tasks/'.$nextId, ['type' => 'work', 'content' => 'hard']);
        $text = '"Are you a hacker or something? The task you were trying to edit doesn\'t exist."';

        $this->assertEquals($this->response->status(), 404);
        $this->assertEquals($this->response->getContent(), $text);

        $this->patch('/tasks/'.$task->id, ['type' => 'asdf', 'content' => 'asdf']);
        $text = '"The task type you provided is not supported. You can only use shopping or work."';

        $this->assertEquals($this->response->status(), 400);
        $this->assertEquals($this->response->getContent(), $text);

        $this->patch('/tasks/'.$task->id, ['type' => 'work', 'content' => '']);
        $text = '"Bad move! Try removing the task instead of deleting its content."';

        $this->assertEquals($this->response->status(), 400);
        $this->assertEquals($this->response->getContent(), $text);

        $this->patch('/tasks/'.$task->id, ['type' => 'work', 'content' => 'hard']);

        $this->assertEquals($this->response->status(), 200);
        $response = json_decode($this->response->getContent());

        $task = App\Task::find($task->id);

        $this->assertEquals($response->type, 'work');
        $this->assertEquals($response->content, 'hard');
    }

    public function testDelete()
    {
        $task = factory('App\Task')->create();
        $taskId = $task->id;

        $this->delete('/tasks/'.$taskId);
        $task = App\Task::find($taskId);

        $this->assertEquals($this->response->status(), 200);
        $this->assertNull($task);

        $this->delete('/tasks/'.$taskId);
        $text = '"Good news! The task you were trying to delete didn\'t even exist."';

        $this->assertEquals($this->response->status(), 404);
        $this->assertEquals($this->response->getContent(), $text);
    }
}
