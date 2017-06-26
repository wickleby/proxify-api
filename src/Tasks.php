<?php namespace Proxify\ProxifyApi;

use Proxify\ProxifyApi\Exceptions\ProxifyFrameworkException;

class Tasks
{

    public $tasks;

    /**
     * Create Tasks from API Response
     *
     * @param $response
     * @return Task[]
     */
    public static function createFromApiResponse($response)
    {
        $tasks = [];
        foreach ($response as $task) {
            $tasks[] = Task::createFromApiResponse($task);
        }

        $obj = new Tasks();
        $obj->tasks = $tasks;

        return $obj;
    }

    /**
     * Return all tasks which has passed the conditions
     *
     * @return Task[]
     */
    public function all()
    {
        return array_filter($this->tasks, function($task){
           return $task->passConditions;
        });
    }

    /**
     * Get task
     *
     * @param $taskName
     * @return bool|Task
     * @throws ProxifyFrameworkException
     */
    public function get($taskName)
    {
        foreach ($this->tasks as $task) {
            if ($task->name == $taskName) {
                return $task;
            }
        }

        throw new ProxifyFrameworkException('Could not find task with name ' . $taskName);
    }


}