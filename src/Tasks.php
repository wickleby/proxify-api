<?php namespace Proxify\ProxifyApi;

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

    public function all()
    {
        return $this->tasks;
    }

    /**
     * Get task
     *
     * @param $taskName
     * @return bool|Task
     */
    public function get($taskName)
    {
        foreach ($this->tasks as $task) {
            if ($task->name == $taskName) {
                return $task;
            }
        }

        return false;
    }


}