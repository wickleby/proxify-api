<?php namespace Proxify\ProxifyApi;

/**
 * Class Task
 * @package Proxify\ProxifyApi
 */
class Task
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var TaskType
     */
    public $type;

    /**
     * @var TaskStatus
     */
    public $status;

    /**
     * @var boolean
     */
    public $completed;

    /**
     * @var string
     */
    public $passCondition;

    /**
     * @var boolean
     */
    public $visibleForClient;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $labelNotCompleted;

    /**
     * @var string
     */
    public $labelActive;

    /**
     * @var string
     */
    public $labelCompleted;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $descriptionNotCompleted;

    /**
     * @var string
     */
    public $descriptionActive;

    /**
     * @var string
     */
    public $descriptionCompleted;

    /**
     * Create from API
     *
     * @param $response array
     * @return Task
     */
    public static function createFromApiResponse($response)
    {
        $task = new self;
        $task->name = $response['name'];
        $task->type = TaskType::createFromApiResponse($response['type']);
        $task->status = TaskStatus::createFromApiResponse($response['status']);
        $task->completed = $response['completed'];
        $task->passCondition = $response['pass_condition'];
        $task->visibleForClient = $response['visible_for_client'];
        $task->label = $response['label'];
        $task->labelNotCompleted = $response['label_not_completed'];
        $task->labelActive = $response['label_active'];
        $task->labelCompleted = $response['label_completed'];
        $task->description = $response['description'];
        $task->descriptionNotCompleted = $response['description_not_completed'];
        $task->descriptionActive = $response['description_active'];
        $task->descriptionCompleted = $response['description_completed'];

        return $task;
    }
}