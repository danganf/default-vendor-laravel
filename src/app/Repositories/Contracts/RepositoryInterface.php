<?php

namespace Danganf\Repositories\Contracts;

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

    public function createOrUpdate( $arrayValores );

    public function fails();

}
