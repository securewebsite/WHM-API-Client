<?php
namespace RCrowt\WHM\ApiClient;

use RCrowt\WHM\ApiClient;

class CPanelObject implements \JsonSerializable
{

    /**
     * @var ApiClient WHM API Client Instance
     */
    protected $client;

    /**
     * @var \stdClass Raw data from the WHM Api
     */
    protected $data;

    /**
     * @param \stdClass $data
     * @param ApiClient|null $client
     */
    function __construct(\stdClass $data, ApiClient $client = null)
    {
        $this->client = $client;
        $this->data = $data;
    }

    /**
     * Get an item from the raw WHM Api data.
     * @param $index string
     * @param null $default Optional default value if not found.
     * @return mixed
     */
    public function get($index, $default = null)
    {
        if (property_exists($this->data, $index)) return $this->data->$index;
        else return $default;
    }

    // ---------------- //
    // JsonSerializable //
    // ---------------- //

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->data;
    }


}