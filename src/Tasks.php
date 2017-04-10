<?php namespace Proxify\ProxifyApi;

class Tasks
{
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

        return $tasks;
    }
}