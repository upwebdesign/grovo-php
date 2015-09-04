<?php
/**
 * @copyright Grovo Learning, Inc.
 */

namespace Grovo\Api\Client\Contexts;

/**
 * Trait Users
 *
 * @package Grovo\Api
 * @subpackage Client
 * @version 1.0
 * @author Yitzchok Willroth (yitz@grovo.com)
 */
trait Users
{
    /**
     * @param string $id
     * @return array
     */
    public function getUsers($page = 1)
    {
        return json_decode($this->get("/users?page=$page"), true);
    }

    /**
     * @param string $id
     * @return array
     */
    public function showUser($id)
    {
        return json_decode($this->get("/users/$id"), true);
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function addUser(array $attributes)
    {
        return json_decode($this->post("/users", $attributes), true);
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function removeUser($id)
    {
        return $this->delete("/users/$id");
    }

    /**
     * @param string $id
     * @param array $attributes
     * @return array
     */
    public function updateUser($id, array $attributes)
    {
        return json_decode($this->patch("/users/$id", $attributes), true);
    }

}
