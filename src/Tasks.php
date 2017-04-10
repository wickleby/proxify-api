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


}