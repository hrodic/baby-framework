<?php
namespace model;
use core\model\Mongo as Mongo;
/**
 * 
 */
class Mongoschema extends Mongo
{

    /**
     * 
     */
    public static function prepareIndexes()
    {
        elf::selectDB('db')->selectCollection('collection')
                ->ensureIndex(array(MyModel::TEST=>true));
    }

}
