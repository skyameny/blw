<?php
namespace core\service\interf;

/**
 * 可循环查询的
 * @author keepwin100
 *
 */
interface  Searchable
{
    /**
     * 可以循环
     * @param array $condition
     */
    public function searchInstances(array $condition=[]);
}