<?php

namespace Danganf\Repositories\Contracts;

use Illuminate\Support\Facades\App;

abstract class RepositoryAbstract implements RepositoryInterface
{
    private $model, $msgError;
    private $selectFields=[],$withArray=[];

    function __construct( $modelBind, $model=null )
    {
        if( !is_object( $modelBind ) ) {
            $modelBind = App::make('App\Model\\' . str_replace('Repository', '', last(explode('\\', $modelBind))));
        }

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

    public function setFields($values){
        $values = !is_array($values) ? explode(',',$values) : $values;
        foreach ($values AS $row) {
            $this->selectFields[] = $row;
        }

        return $this;
    }

    public function setwith($values){
        $this->withArray[] = $values;
        return $this;
    }

    public function get( $campo, $returnDefault=FALSE )
    {
        $valor = ( isset ( $this->model->$campo ) ? $this->model->$campo : $returnDefault );

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

    public function delete( $value, $uk='id' )
    {
        $return     = FALSE;
        $collection = $this->getModel()->where( $uk, $value )->first();
        if( !empty( $collection ) ){
            $return = $collection->delete();
        }
        return $return;
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
        $model  = $this->model;

        if( !empty( $this->withArray ) ){
            $model = $model->with( $this->withArray );
            $this->withArray = [];
        }

        $result = $model->find( $valor );
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

        $model = $this->getModel();

        if( !empty( $this->withArray ) ){
            $model = $model->with( $this->withArray );
            $this->withArray = [];
        }

        $buildQuery = $model->where($campoUnico,$documento);
        $this->processSelectFields( $buildQuery );
        $result     = $buildQuery->first();

        if( !is_null($result ) ) {
            $this->model = $result;
        }

        return $this;

    }

    public function findAll( $campo, $documento ) {

        $model = $this->getModel();

        if( !empty( $this->withArray ) ){
            $model = $model->with( $this->withArray );
            $this->withArray = [];
        }

        $buildQuery = $model->where($campo,$documento);
        $this->processSelectFields( $buildQuery );
        $return     = $buildQuery->get()->toArray();
        return $return;

    }

    public function firstOrNew( $value, $field='id' ){
        $this->bindModel( $this->getModel()->firstOrNew( [ $id => $value ] ) );
        return $this;
    }


    private function processSelectFields(&$buildQuery){
        $buildQuery->select( empty( $this->selectFields ) ? '*' : $this->selectFields );
        $this->selectFields = [];
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

    public function setMsgError( $msg ){
        $this->msgError = $msg;
    }

    public function getMsgError(){
        return $this->msgError;
    }

    public function setFilter(&$where, $value=null,$operator=''){
        $operator = empty( $operator ) ? ( empty( trim( $where ) ) ? '' : 'and' ) : $operator;
        $where   .= rtrim(" $operator ( $value )", ' ');
        //dd( $where, $value );
        return $this;
    }

    public function startLog(){
        \DB::enableQueryLog();
    }

    public function showLog(){
        return \DB::getQueryLog();
    }


}
