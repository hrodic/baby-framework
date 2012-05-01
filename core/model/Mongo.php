<?php
namespace core\model;
/**
 * Base model for mongo db
 * * geospatial
 * ----------
 * db.cities.ensureIndex({x:1, y:1});
 * db.cities.insert({location:{lon:0,lat:0}})
 *
 * @author rodrigo
 */
class Mongo extends \core\db\Mongo
{
    
    
}