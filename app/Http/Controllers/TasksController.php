<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    protected $task;

    /**
     * Class constructor.
     *
     * @param  Task $task    HTTP inputs
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Returns all tasks and it can be ordered by the 'sort_by' GET parameter
     *
     * @param  Illuminate\Http\Request $request    HTTP inputs
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->executeAndRespond(function () use ($request) {
            $sortBy = $request->get('sort_by', false);

            $tasks = $this->task->getAll($sortBy);
            if (count($tasks)) {
                $response = [];

                foreach ($tasks as $task) {
                    /**
                     * Not sure if the criterion "The user must be able to get
                     * the details of a task" means that there must be a end
                     * point to get the details or if this endpoint should
                     * retutung the details. Assuming the other endpoing should
                     * return all the details.
                     *
                     * This would return with full details:
                     * $response[] = $this->parseTask($task);
                     */
                    $response[] = [
                        'uuid' => $task->id,
                        'content' => $task->content
                    ];
                }

                return $this->respond($response, 200);
            } else {
                $response = 'Wow. You have nothing else to do. Enjoy the rest of your day!';
                return $this->respond($response, 404);
            }
        });
    }

    /**
     * Grabs details of a single task.
     *
     * @param  Integer $id    Task id
     * @return Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->executeAndRespond(function () use ($id) {
            $task = Task::find($id);
            if ($task) {
                return $this->respond($this->parseTask($task), 200);
            } else {
                $response = "Are you a hacker or something? The task you were "
                    ."trying to see doesn't exist.";
                return $this->respond($response, 404);
            }
        });
    }

    /**
     * Creates a task.
     *
     * @param  Illuminate\Http\Request $request    HTTP inputs
     * @return Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->executeAndRespond(function () use ($request) {
            $input = $this->validateRequest($request);

            if (empty($input->errorMessage)) {
                $task = new Task($input->properties);
                $task->save();

                return $this->respond($this->parseTask($task), 200);
            } else {
                return $this->respond($input->errorMessage, 400);
            }
        });
    }

    /**
     * Updates a task.
     *
     * @param  Integer                 $id         Task id
     * @param  Illuminate\Http\Request $request    HTTP inputs
     * @return Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        return $this->executeAndRespond(function () use ($id, $request) {
            $task = Task::find($id);

            if ($task) {
                $input = $this->validateRequest($request);
                $input->properties['id'] = $id;

                if (empty($input->errorMessage)) {
                    $task->update($input->properties);
                    return $this->respond($this->parseTask($task), 200);
                } else {
                    return $this->respond($input->errorMessage, 400);
                }
            } else {
                $response = "Are you a hacker or something? The task you were "
                    ."trying to edit doesn't exist.";
                return $this->respond($response, 404);
            }
        });
    }

    /**
     * Deletes a task.
     *
     * @param  Integer $id Task id
     * @return Illuminate\Http\Response
     */
    public function delete($id)
    {
        return $this->executeAndRespond(function () use ($id) {
            $task = Task::find($id);

            if ($task) {
                $task->delete();
                return $this->respond(null, 200);
            } else {
                $response = "Good news! The task you were trying to delete "
                    ."didn't even exist.";
                return $this->respond($response, 404);
            }
        });
    }

    /**
     * Checks if the incomming request is constent and return it as an object
     * that contains a 'properties' property to be digested by the model. It may
     * also contain a property 'errorMessage' in case the request is not valid.
     *
     * @param  Illuminate\Http\Request $request    HTTP inputs
     * @return StdClass
     */
    private function validateRequest($request)
    {
        $allowedTypes = ['shopping', 'work'];

        $type = $request->input('type', null);
        $content = $request->input('content', null);
        $done = $request->input('done', false);
        $sortOrder = $request->input('sort_order', 0);

        $isTypeAllowed = in_array($type, $allowedTypes);

        $result = (object) [
            'properties' => [
                'type' => $type,
                'content' => $content,
                'sort_order' => $sortOrder,
                'done' => $done
            ]
        ];

        if (!$content) {
            $result->errorMessage = 'Bad move! Try removing the task '
                .'instead of deleting its content.';
        }

        if (!$isTypeAllowed) {
            $result->errorMessage = 'The task type you provided is not '
                .'supported. You can only use shopping or work.';
        }

        return $result;
    }

    /**
     * Parses the task model into array format to be returned as json and
     * conformant with the criteria.
     *
     * @param  Task $task    Task model object
     * @return Array
     */
    private function parseTask(Task $task)
    {
        return [
            'uuid' => $task->id,
            'type' => $task->type,
            'content' => $task->content,
            'done' => (bool) $task->done,
            'sort_order' => $task->sort_order,
            'date_created' => $task->created_at->format('Y-m-d H:i:s')
        ];
    }
}
