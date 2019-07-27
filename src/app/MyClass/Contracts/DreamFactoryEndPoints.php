<?php

namespace IntercaseDefault\MyClass\Contracts;

use Carbon\Carbon;
use Illuminate\Http\Request;
use IntercaseDefault\Facades\ThrowNewExceptionFacades;
use IntercaseDefault\MyClass\Curl;
use IntercaseDefault\MyClass\Json\Contracts\JsonAbstract;

/**
 * Class DreamFactoryEndPoints
 * @package IntercaseDefault\MyClass\Contracts
 */
abstract class DreamFactoryEndPoints
{
    private   $host, $curl, $header, $method, $timeout, $objJson, $serviceName, $bodyPost, $idField, $limit;
    private   $proxyServiceName, $order='', $filter, $offset=null, $countOnly=false, $countInclude=false, $bodyJson=false, $groupBy;
    private   $related=[], $fields=[], $paginator = [], $msgError, $lastId=null;
    protected $request, $fieldsDefault=false, $rollback=false, $dateAts=false, $updtAts=false;
    public    $cache;

    const RESPONSE_INVALID_ARQ = 'InvalidArgument';

    function __construct(Curl $curl, JsonAbstract $jsonBasic, Request $request, $objectCache)
    {
        $this->curl             = $curl;
        $this->objJson          = $jsonBasic;
        $this->proxyServiceName = $request->get('proxy_name_service');
        $this->cache            = $objectCache;
        $this->request          = $request;
        $this->method           = 'GET';
        $this->setVariables();
    }

    public function setFieldsDefault($value){
        $this->fieldsDefault = true;
        $this->fields=[];
        $this->setFields($value);
        return $this;
    }

    public function setHeader($key,$value){
        $this->header[] = "$key: $value";
        return $this;
    }

    public function setMethod($value){
        $this->method = $value;
        return $this;
    }

    public function setLimit($value){
        $this->limit = $value;
        return $this;
    }

    public function setGroupBy($value){
        $this->groupBy = $value;
        return $this;
    }

    public function setBodyJson(){
        $this->bodyJson = true;
        return $this;
    }

    public function setTimeout($value){
        $this->timeout = $value;
        return $this;
    }

    public function setRelated($value){
        $this->related[] = $value;
        return $this;
    }

    public function setRelatedFields($relatedName,$filterValue){
        $this->setRelated($relatedName);
        $this->setRelated($relatedName."_fields='".$filterValue."'");
        return $this;
    }

    public function setFields($value){
        $value = !is_array($value) ? [$value] : $value;
        foreach ($value AS $row) {
            $this->fields[] = $row;
        }
        return $this;
    }

    public function setOrder($value){
        $this->order = $value;
        return $this;
    }

    public function setIdField($value){
        $this->idField = $value;
        return $this;
    }

    public function setCountOnly(){
        $this->countOnly = true;
        return $this;
    }

    public function setCountInclude(){
        $this->countInclude = true;
        return $this;
    }

    public function setOffSet($total){
        $this->offset = $total;
        return $this;
    }

    public function resetFields(){
        $this->fields=[];
        return $this;
    }

    public function resetFilter(){
        $this->filter = null;
        return $this;
    }

    public function setHost($url){
        $this->host = $url;
        return $this;
    }

    public function setFilter($value=null,$operator=''){
        $operator      = empty( $operator ) ? ( empty( trim( $this->filter ) ) ? '' : 'and' ) : $operator;
        $this->filter .= rtrim(" $operator ($value)", ' ');
        return $this;
    }

    public function setMultiFilter( $field, $value, $operator='or' ){
        $values   = !is_array($value) ? [$value] : $value;
        $where    = '';
        foreach ( $values as $row ){$where .= "($field='$row') $operator ";}
        $this->setFilter( rtrim( $where, "$operator " ) );
        return $this;
    }

    public function setServiceName($value){
        $this->serviceName = $value;
        return $this;
    }

    public function setProxyServiceName($value){
        $this->proxyServiceName = $value;
        $this->getUrlService();
        return $this;
    }

    public function setFilterStore($operator='',$field='store_id'){
        $operator = empty( $operator ) ? ( empty( trim( $this->filter ) ) ? '' : 'and' ) : $operator;
        $this->setFilter($field.'='.$this->request->get('store_id'),$operator);
        return $this;
    }

    public function setFilterStoreOrNull($field='store_id'){
        $this->setFilter("($field=".$this->request->get('store_id')." ) or ($field is null)" );
        return $this;
    }

    public function setFilterCompany($operator='',$field='company_id'){
        $operator = empty( $operator ) ? ( empty( trim( $this->filter ) ) ? '' : 'and' ) : $operator;
        $this->setFilter($field.'='.$this->request->get('company_id'),$operator);
        return $this;
    }

    public function setStatusTrue($operator=''){
        $operator = empty( $operator ) ? ( empty( trim( $this->filter ) ) ? '' : 'and' ) : $operator;
        $this->setStatus(1,$operator);
        return $this;
    }

    private function setStatus($status, $operator=''){
        $operator = empty( $operator ) ? ( empty( trim( $this->filter ) ) ? '' : 'and' ) : $operator;
        $this->setFilter("status=$status",$operator);
    }

    public function setValuesPostPut($arrayValues=[],$ids=0,$noIds=false){
        if( !empty( $arrayValues ) ){

            if( !is_int( array_key_first( $arrayValues ) ) ){
                $body['resource'][] = $arrayValues;
            } else {

                $createdAt = array_pull( $arrayValues, 'created_at' );
                $updatedAt = array_pull( $arrayValues, 'updated_at' );

                foreach (  $arrayValues as $row ){
                    if( !empty( $createdAt ) ){$row['created_at'] = $createdAt;}
                    if( !empty( $updatedAt ) ){$row['updated_at'] = $updatedAt;}
                    $body['resource'][] = $row;
                }
            }

            if( !$noIds ) {
                $body['ids'][] = $ids;
            }

            $body['filter'] = 'string';
            $body['params'] = [];
            $this->bodyPost = json_encode( $body );
        }
    }

    public function setBodyPost($arrayValues=[]){
        if( !empty( $arrayValues ) ){
            $this->bodyPost = !$this->bodyJson ? $arrayValues : json_encode( $arrayValues );
        }
    }

    public function getProxyServiceName(){return $this->proxyServiceName;}

    public function getById($valueId,$table){
        $countOnly = $this->getCountOnly();

        $this->setFilter('id='.$valueId)
             ->setServiceName("_table/$table");
        $return = $this->request();
        if( empty( $return ) ) {
            if( !$countOnly ) {
                $return = [];
            }
        } else {
            if( !$countOnly ) {
                $return = $return[0];
            }
        }
        return $return;
    }

    public function getByUkField($field,$value,$table){
        $dados = [];
        $this->setServiceName("_table/$table/$value")
             ->setFields($field)
             ->setIdField($field);
        $return = $this->request();
        if( !empty( $return ) ){
            $dados = $return;
        }
        return $dados;
    }

    public function getByField($field,$value,$table){

        $this->setServiceName("_table/$table")
             ->setFilter("$field = $value");
        $return = $this->request();
        if( empty( $return ) ){
            $return = [];
        }
        return $return;
    }

    /**
     * Update tbl by unique column
     * @param $ukField
     * @param $valueField
     * @param $dataArray
     * @param $table
     * @param $setUptAt
     * @return array
     */
    public function putByUkField($ukField,$valueField,$dataArray,$table,$setUptAt=false,$forceValue=false){
        $dataSend = [];

        #verificando se existe dados com valores para atualizar
        foreach ( $dataArray as $key=>$value ){
            if( !empty( $value ) || is_bool( $valueField ) || $value == '0' || $forceValue ){$dataSend[$key] = $value;}
        }

        $dados = [];
        if( $dataSend ){

            if($setUptAt){$dataSend['updated_at'] = getDateNow('SP');}

            $this->setServiceName('_table/'.$table)
                 ->setFields($ukField)
                 ->setIdField($ukField)
                 ->setMethod('PUT')
                 ->setBodyJson()
                 ->setValuesPostPut($dataSend,$valueField);
            $return = $this->request();
            if( !empty( $return ) ){
                $dados = $return[0];
            }
        }
        return $dados;
    }

    public function putByFields($stringFilter, $dataSend, $table, $fieldStringReturn=null){

        $return = FALSE;
        if( $stringFilter ) {

            if( $this->dateAts || $this->updtAts ) {
                $dataSend['updated_at'] = getDateNow('SP');
                $this->dateAts          = false;
                $this->updtAts          = false;
            }

            $this->resetFields()
                ->setFilter( $stringFilter )
                ->setServiceName('_table/' . $table)
                ->setMethod('PUT')
                ->setBodyJson()
                ->setValuesPostPut($dataSend,[],true);

            if( !empty( $fieldStringReturn ) ){
                $this->setFields($fieldStringReturn);
            }

            $return = $this->request();
            if( empty( $return ) ){
                $return = [];
            }
        }

        return $return;

    }

    public function deleteByFields($stringFilter, $table){

        $return = FALSE;
        if( $stringFilter ) {
            $this->resetFields()
                 ->setFilter( $stringFilter )
                 ->setServiceName('_table/' . $table)->setMethod('DELETE');
            $return = $this->request();
            if( empty( $return ) ){
                $return = [];
            }
        }

        return $return;

    }

    public function createData($dataArray,$table, $dateAts=false){
        $dados = [];
        if( is_array($dataArray) && !empty( $dataArray ) ){

            if( $dateAts || $this->dateAts ) {
                $now                     = getDateNow('SP');
                $dataArray['created_at'] = $now;
                $dataArray['updated_at'] = $now;
                $this->dateAts           = false;
            }

            $this->setServiceName('_table/'.$table)
                ->setMethod('POST')
                ->setBodyJson()
                ->setValuesPostPut($dataArray);

            $return = $this->request();

            if( !empty( $return ) ){
                $dados = $return[0];
            }

        }
        return $dados;
    }

    public function saveOrPut($dataSend, $id=null, $table, $pk='id'){

        if( is_array( $dataSend ) && !empty( $dataSend ) ){
            $id     = (int)$id;
            $result = [];

            if( $this->dateAts ) {
                $now                     = getDateNow('SP');
                $dataSend['created_at'] = $now;
                $dataSend['updated_at'] = $now;
            }
            else if( $this->updtAts ) {
                $dataSend['updated_at'] = getDateNow('SP');
            }

            $this->resetFields()->resetFilter();

            if( empty( $id ) ) {
                $result = $this->createData($dataSend, $table, true);
            }
            else{

                if( $this->dateAts ) {
                    array_pull( $dataSend, 'created_at' );
                }

                if( !empty( $this->getById( $id, $table ) ) ){
                    $result = $this->putByFields($pk.'='.$id, $dataSend, $table);
                    if( is_array( $result ) && count( $result ) > 0 ){
                        $result = current( $result );
                    }
                }
            }

            $flag = false;
            if( array_has( $result, $pk ) ){
                $this->lastId = $result[$pk];
                $flag = true;
            } else {
                $flag = $this->getMsgError();
            }
        } else {
            $flag = true;
        }


        $this->dateAts = false;

        return $flag;

    }

    public function getCountOnly()   {return $this->countOnly;}
    public function getCountInclude(){return $this->countInclude;}
    public function getLastId($forceReset=true){
        $tmp = $this->lastId;
        if( $forceReset ){
            $this->lastId = null;
        }
        return $tmp;
    }

    public function getPaginator($forceHeader=false)   {

        $return = [];
        if( $this->paginator ){
            $return = [
                'count' => array_get( $this->paginator, 'count' , 0 ),
                'next'  => array_get( $this->paginator, 'next'  , 0 ),
                'limit' => array_get( $this->paginator, 'limit' , 0 ),
                'range' => array_get( $this->paginator, 'range' , 0 ),
            ];

            if( $forceHeader ){
                $returnTmp['x-paginator-df'] = implode(';', array_map(
                    function ($v, $k) {
                        return sprintf("%s=%s", $k, $v);
                    },
                    $return,
                    array_keys($return)
                ));
                $return = $returnTmp;
            }
        }

        return $return;
    }

    public function request($params = [], $reset=true){

        $dados             = NULL;
        $options['method'] = $this->method;
        $options['header'] = $this->header;
        $options['data']   = ( !empty( $params ) ? $params : $this->bodyPost );

        if( $this->bodyJson )        {$options['json'] = TRUE;}
        if( $this->method == 'POST' ){$options['post'] = TRUE;}

        $queryHttp = $this->resolveQueryHttp();
        $host      = $this->host . $this->serviceName . $queryHttp;

        \LogDebug::request('',[
            'host'       => $this->host,
            'service'    => $this->serviceName,
            'query_http' => $queryHttp,
            'related'    => $this->related,
            'options'    => $options
        ]);

        //dd( $host, $options, getRouteName() );
        //\LogDebug::error("----## ROUTE: " . getRouteName());

        if( !empty( $this->host ) ) {

            $return = $this->curl->send($host, $options);
            if (is_array($return)) {
                $this->paginator = [];
                $result = json_decode( $return['RESULT'], true );
                if( is_array( $result ) && key_exists('resource', $result) && !empty( $result['resource'] ) ){
                    $this->objJson->setJson(json_decode(json_encode($result['resource'])));
                    $dados = $this->objJson->toArray();

                    if( key_exists('meta', $result) && !empty( $result['meta'] ) ){
                        $this->paginator = $result['meta'];
                    }

                } else if( $this->countOnly === TRUE ){
                    if( is_int( $result ) ){
                        $dados = $result;
                    }
                }
                else {
                    $error = false;

                    if( is_array( $result ) ) {
                        if ( key_exists('error', $result) ) {
                            $dados = [];
                            $error = true;
                            if( array_has( $result, 'error.context.resource' ) ){
                                try{
                                    $resource = current( $result['error']['context']['resource'] );
                                    if( array_has( $resource, 'message' ) ){
                                        $this->msgError = $resource['message'];
                                    }
                                } catch (\Exception $e){

                                }
                            } else if( array_has( $result, 'error.message' ) ){
                                try{
                                    $message = $result['error']['message'];
                                    if( !empty( $message ) ){
                                        $this->msgError = $message;
                                    }
                                } catch (\Exception $e){

                                }
                            }
                        } else if( key_exists('resource', $result) && empty( $result['resource'] ) ){
                            $error = true;
                            $dados=[];
                        } else {
                            $dados = $result;
                        }
                    }
                    if($error){\LogDebug::error($return['RESULT']);}
                }
            }
        }
        if( $reset ) {
            $this->resetVariables();
        }
        return $dados;

    }

    protected function setMsgError($msg){$this->msgError = $msg;}

    public function getMsgError($msgDefault=null){
        $msg = $this->msgError;
        if( strpos( $msg, 'Duplicate entry') !== FALSE )
            $msg = \Lang::get('default.register_exist');
        else if( strpos( $msg, 'SQLSTATE[45000]:') !== FALSE ){
            $msg = str_replace( 'SQLSTATE[45000]: &lt;&lt;Unknown error&gt;&gt;: ', '', $msg );
            $msg = current( explode( '(SQL:', $msg ) );
            $msg = trim( only_string( $msg ) );
        } else {
            $msg = !empty( $msg ) ? $msg : ( $msgDefault ? $msgDefault : \Lang::get('default.internal_server_error') );
        }

        return $msg;
    }

    private function resolveQueryHttp(){
        $queryHttp = '';
        if( !empty( $this->filter ) ){$queryHttp = trim($queryHttp).'?filter='.trim($this->filter);}

        if( !empty( $this->related ) ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'related='.implode(',',$this->related);
        }
        if( !empty( $this->fields ) ) {
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'fields='.implode(',',$this->fields);
        }
        if( !empty( $this->order ) ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'order='.$this->order;
        }
        if( $this->countOnly ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'count_only=true';
        }
        if( $this->rollback ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'rollback=true';
        }
        if( $this->idField ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'id_field='.$this->idField;
        }
        if( $this->limit ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'limit='.$this->limit;
        }
        if( $this->groupBy ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'group='.$this->groupBy;
        }
        if( $this->countInclude ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'include_count=true';
        }
        if( !is_null( $this->offset ) ){
            $operator  = !empty( $queryHttp ) ? '&' : '?';
            $queryHttp = trim($queryHttp).$operator.'offset='.(int)$this->offset;
        }
        return $queryHttp;
    }

    protected function resetVariables(){
        $this->order         = '';
        $this->filter        = '';
        $this->idField       = '';
        $this->related       = [];
        $this->countOnly     = false;
        $this->countInclude  = false;
        $this->offset        = null;
        $this->bodyPost      = null;
        $this->groupBy       = null;
        $this->limit         = null;
        $this->fieldsDefault = false;
        $this->bodyJson      = false;
        $this->rollback      = false;
        $this->dateAts       = false;
        $this->updtAts       = false;
        $this->lastId        = null;
    }

    public function response($return){

        $msg = '';
        switch ( $return ){
            case $this::RESPONSE_INVALID_ARQ:
                $msg = \Lang::get('default.parameters_incorrets');
                break;
        }

        ThrowNewExceptionFacades::{$return}($msg);
    }

    protected function setVariables(){
        $this->setHeader('X-DreamFactory-API-Key', \Request::get('api_key'));
        $this->getUrlService();
    }

    public function setRollBack(){
        $this->rollback=true;
        return $this;
    }

    /**
     * set created_at e updated_at in dataSend post/put/patch
     * @return $this
     */
    public function setDateAts(){
        $this->dateAts=true;
        return $this;
    }

    public function setUpdtAts(){
        $this->updtAts=true;
        return $this;
    }

    public function setRequestFilters($arrayFilter, $limit=25){

        $limit = array_get( $arrayFilter, 'limit', $limit );
        $limit = ( $limit !== 'ALL' ? (int)$limit : null );

        if( !empty($limit) )                                                              {$this->setLimit( $limit );}
        if( !empty( trim( array_get( $arrayFilter, 'offset', '' ) ) ) )       {$this->setOffSet($arrayFilter['offset']);}
        if( !empty( trim( array_get( $arrayFilter, 'count_product', '' ) ) ) ){$this->setFilter("count_product>0");}
        if( !empty( trim( array_get( $arrayFilter, 'role_id', '' ) ) ) )      {$this->setFilter('(role_id='.$arrayFilter['role_id'].')');}
        if( !empty( trim( array_get( $arrayFilter, 'code', '' ) ) ) )         {$this->setFilter("code='".$arrayFilter['code']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'sector_id', '' ) ) ) )    {$this->setFilter("sector_id='".$arrayFilter['sector_id']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'unit_id', '' ) ) ) )      {$this->setFilter("unit_id='".$arrayFilter['unit_id']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'priority', '' ) ) ) )     {$this->setFilter("priority='".$arrayFilter['priority']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'document', '' ) ) ) )     {$this->setFilter("document='".$arrayFilter['document']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'phone', '' ) ) ) )        {$this->setFilter("phone='".$arrayFilter['phone']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'cellphone', '' ) ) ) )    {$this->setFilter("cellphone='".$arrayFilter['cellphone']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'id', '' ) ) ) )           {$this->setFilter("id='".$arrayFilter['id']."'");}
        if( !empty( trim( array_get( $arrayFilter, 'type', '' ) ) ) )         {$this->setFilter("type='".strtoupper( $arrayFilter['type'] )."'");}

        #feito dessa forma, pq o zero deve ser considerado
        $stringFilter = 'status,in_app,is_input,in_home_app,product_status,have_products_active,accept_order';
        foreach ( explode(',',$stringFilter) as $row ){
            $filterValue = convert_sn_bool( array_get( $arrayFilter, $row, '' ) );
            if( !is_null( $filterValue ) ) {$this->setFilter($row.'=' . $filterValue);}
        }

        #feito dessa forma, pq o zero deve ser considerado
        if( !empty( array_get( $arrayFilter, 'categories' ) ) ){
            $where = '';
            foreach ( explode(',', $arrayFilter['categories']) as $id ){$where .= "(categories like '%@$id|%')or";}
            $this->setFilter(rtrim($where,'or'));
        }
        
        #feito dessa forma, pq o zero deve ser considerado
        if( !empty( array_get( $arrayFilter, 'tags' ) ) ){
            $where = '';
            foreach ( explode(',',$arrayFilter['tags']) as $id ){$where .= "(tags like '%@$id|%')and";}
            $this->setFilter(rtrim($where,'and'));
        }

        #feito dessa forma, pq o zero deve ser considerado
        $filterValue = convert_sn_bool( array_get( $arrayFilter, 'in_promo', '' ) );
        if( !is_null( $filterValue ) ) {
            $where = "(has_promo=$filterValue)";
            if( $filterValue ) {$this->setFilter($where.' or (image is not null)');}
            else{$this->setFilter($where.' and (image is null)');}
        }

        if( array_get( $arrayFilter, 'search' ) && !empty( trim( array_get( $arrayFilter, 'search', '' ) ) ) ){
            $search = array_get( $arrayFilter, 'search' );
            if( is_numeric( $search ) ) {
                $where = '(id=' . $search . ') or (' . urlencode("name LIKE '%" . $search . "%'") . ')';
                $this->setFilter($where);
            } else {$arrayFilter['name'] = $search;}
        }

        if( array_get( $arrayFilter, 'search_client' ) && !empty( trim( array_get( $arrayFilter, 'search_client', '' ) ) ) ){
            $search = array_get( $arrayFilter, 'search_client' );
            $where  = urlencode("(document='$search') or (name LIKE '%$search%') or (email LIKE '%$search%') or (phone='$search') or (cellphone='$search')");
            $this->setFilter($where);
        }

        $productStatus = convert_sn_bool(array_get( $arrayFilter, 'product_status', NULL ));
        if( !is_null( $productStatus ) && in_array( $productStatus, [0,1] ) ){$this->setFilter("product_status='".(int)$productStatus."'");}

        if( !empty( trim( array_get( $arrayFilter, 'name', '' ) ) ) ){$this->setFilter('('.urlencode("name LIKE '%".$arrayFilter['name']."%'").')');}
        if( !empty( trim( array_get( $arrayFilter, 'initials', '' ) ) ) ){$this->setFilter('('.urlencode("initials LIKE '%".$arrayFilter['initials']."%'").')');}

        return $this;

    }

    private function getUrlService(){
        $keyCache = 'getUrl_'.$this->proxyServiceName;
        if( !$this->cache->has( $keyCache ) ) {
            $curl              = new \IntercaseDefault\MyClass\Curl();
            $url               = config('magaliapi.proxy_df_url')."services_proxy/_table/services?fields=url&filter=slug='$this->proxyServiceName'";
            $options['header'] = $this->header;
            $return            = $curl->send($url, $options);//dd($return,'priscila',$url,$options);
            $url               = '';
            if( is_array( $return ) ){
                $result = json_decode( $return['RESULT'], true );
                if( is_array( $result ) && key_exists('resource', $result) && !empty( $result['resource'] ) ){
                    $url = $result['resource'][0]['url'];
                    $this->cache->setTime(config('magaliapi.time_cache_url'))->create($keyCache,$url);
                } else {
                    \LogDebug::request('',$return);
                }
            }
        } else {
            $url = $this->cache->get($keyCache);
        }
        $this->host = $url;
    }
}
