<?php

namespace App\Repositories\Contracts;

abstract class RepositoryAbstract implements RepositoryInterface
{
    private $model;

    function __construct( $modelBind, $model=null )
    {
        if ( !$model instanceof $modelBind ) {
            $model = new $modelBind();
        }

        if ( is_object( $model ) )
            $this->model = $model;
    }

    public function bindModel( $instanceModel )
    {
        if ( $instanceModel instanceof $this->model ) {
            $this->model = $instanceModel;
        }
    }

    public function set( $campo, $valor )
    {
        $this->model->$campo = $valor;

        return $this;
    }

    public function get( $campo )
    {
        $valor = ( isset ( $this->model->$campo ) ? $this->model->$campo : FALSE );

        return $valor;
    }

    public function has($campo)
    {
        return ( !empty( $this->get($campo) ) ? true : false );
    }

    public function save()
    {
        return $this->model->save();
    }

    public function update( $lista = [ ] )
    {
        return $this->model->update( $lista );
    }

    public function delete()
    {
        return $this->model->delete();
    }

    public function getLastID()
    {
        return ( isset ( $this->model->id ) ? $this->model->id : FALSE );
    }

    public function getModel()
    {
        return $this->model;
    }

    public function find( $valor )
    {
        $result = $this->model->find( $valor );
        if( !is_null($result ) ) {
            $this->model = $result;
        }
        return $this;

    }

    public function increment( $campo )
    {
        return $this->model->increment( $campo );
    }

    public function findBy( $campoUnico, $documento ) {

        $result = $this->getModel()->where($campoUnico,$documento)->first();
        if( !is_null($result ) ) {
            $this->model = $result;
        }
        return $this;

    }

    public function findAll( $campo, $documento ) {

        return $this->getModel()->where($campo,$documento)->get()->toArray();

    }

    public function all(){
        return $this->getModel()->all();
    }

    public function toArray()
    {
        return $this->getModel()->toArray();
    }

    public function fails(){
        return ( empty( $this->getModel()->getKey() ) ? TRUE : FALSE );
    }
}