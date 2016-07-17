# PHP assessment

This was an assessment for a job, basically it is a "to do" restful API resourse with some validation and custom messages.

##Instalation

First checkout the Lumen instalation requirements on https://lumen.laravel.com/docs/5.2/installation

Apart of that you will need a MySQL server with two databases with the following names: `todo_lumen` and  `todo_lumen_test`.

Edit the `.env` to match your environment details and credentials.

Now just run install the dependencies: `$ composer install`

And you can run it with PHP built in server: `$ php -S localhost:8000 -t ./public/`

##End Points

**GET /tasks** This get all tasks and can be ordered by the GET parameter sort_by, it can be any of the entity attributes, prepend a + or – to change the ordering direction. (accepts only one field as of now) 

**POST /tasks** Pass json data to create the tasks. Example data input:
{
  "type": "work",
  "content": "hard",
  "done": false,
  "sort_order": 7
}

**GET** /tasks/{id} Return the task with full details.

**PATCH** /tasks/{id} Pass json data to update a task, the same kind of input as on the POST /tasks.

**DELETE** /tasks/{id} Deletes the task.

### Tests

There are some functional tests, to run them you must have the `todo_lumen_test` database. `$ phpunit` will run the tests.
