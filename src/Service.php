<?php namespace Proxify\ProxifyApi;

class Service
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
    public $url;

    /**
     * Create object form response
     *
     * @param $response
     * @return Service
     */
    public static function createFromApiResponse($response)
    {
        $service = new self;
        $service->id = $response['id'];
        $service->name = $response['name'];
        $service->label = $response['label'];
        $service->url = $response['url'];

        return $service;
    }
}
