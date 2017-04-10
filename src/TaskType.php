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
     * @var string
     */
    public $icon;

    /**
     * @var string
     */
    public $color;


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
        $taskStatus->icon = $response['icon'];
        $taskStatus->color = $response['color'];

        return $taskStatus;
    }
}