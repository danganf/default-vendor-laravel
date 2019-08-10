<?php

namespace Danganf\Repositories\Contracts;

use Danganf\MyClass\Json\Contracts\JsonAbstract;

interface RepositoryInterface
{
    public function set( $campo, $valor );

    public function get( $campo );

    public function has( $campo );

    public function save();

    public function all();

    public function getLastID();

    public function getModel();

    public function bindModel( $instanceModel );

    public function toArray();

    public function find( $valor );

    public function increment( $campo );

    public function createOrUpdate( JsonAbstract $arrayValores, $id );

    public function fails();

}
