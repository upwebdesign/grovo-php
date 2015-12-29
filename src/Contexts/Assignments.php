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
    public function getAssignments($page=1)
    {
        return json_decode($this->get("/assignments?page=$page"), true);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getUserAssignments($id=null)
    {
        return json_decode($this->get("/assignments?grovo_id=$id"), true);
    }
}