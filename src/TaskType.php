<?php namespace Proxify\ProxifyApi;

class TaskType
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $label;

    /**
     * Create from API
     *
     * @param $response
     * @return TaskStatus
     */
    public static function createFromApiResponse($response)
    {
        $taskStatus = new TaskType();
        $taskStatus->id = $response['id'];
        $taskStatus->name = $response['name'];
        $taskStatus->label = $response['label'];

        return $taskStatus;
    }
}