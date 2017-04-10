<?php namespace Proxify\ProxifyApi;

/**
 * The Orders status for this Task
 *
 * Class TaskStatus
 * @package Proxify\ProxifyApi
 */
class TaskStatus
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
        $taskStatus = new TaskStatus();
        $taskStatus->id = $response['id'];
        $taskStatus->name = $response['name'];
        $taskStatus->label = $response['label'];

        return $taskStatus;
    }
}