<?php

namespace Araneum\Base\Tests\Admin;

/**
 * Interface AdminTestInterface
 *
 * @package Araneum\Base\Tests\Admin
 */
interface AdminTestInterface
{
    /**
     * Set of arguments for testCreate method
     *
     * @return array
     */
    public function createDataSource();

    /**
     * Set of arguments for testFilter method
     *
     * @return array
     */
    public function filterDataSource();

    /**
     * Set of arguments for testUpdate method
     *
     * @return array
     */
    public function updateDataSource();

    /**
     * Return entity for testDelete method
     *
     * @return mixed
     */
    public function deleteDataSource();
}
