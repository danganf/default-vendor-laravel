<?php

function msgJson($arrai) {
    return response()->json($arrai, '200', [ 'Content-Type' => 'application/json' ]);
}

function msgErroJson($msg) {
    return ['error'=>1,'messages'=>$msg];
}

function msgSuccessJson( $msg, $dados = [] ) {
    $dados['messages'] = $msg;
    return $dados;
}

function TransformaMaiscula ( &$item ) {
    array_walk_recursive($item, 'Maiscula');
}

function Maiscula(&$item) {

    if ( !is_object($item) ) {
        $LATIN_UC_CHARS = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ°°ª";
        $LATIN_LC_CHARS = "àáâãäåæçèéêëìíîïðñòóôõöøùúûüý°ºª";
        $item           = strtoupper( strtr( $item, $LATIN_LC_CHARS, $LATIN_UC_CHARS ) );
    } else
        array_walk_recursive( $item, 'Maiscula' );

}

function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $d);
    }
    else {
        // Return array
        return $d;
    }
}

function removeAcentos($string, $slug = false) {
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

function getRouteName() {
    return \Illuminate\Support\Facades\Route::currentRouteName();
}

function getRoutePrefixName( $clean = ['/'] ) {
    return str_replace( $clean, '', \Illuminate\Support\Facades\Route::getCurrentRoute()->getPrefix() );
}

function nameMenuAction( $nivel = 1 ){
    $name      = getRoutePrefixName();
    $name      = ( !empty( $name ) ? $name : getRouteName() );
    $name      = ltrim( str_replace('/','.', $name ), '.' );
    $name      = explode('.', $name);
    $prefxName = ucfirst( $name[0] );
    return $prefxName;
}

function isProduction() {
    return ( config('app.env') == 'production' ? TRUE : FALSE );
}

function isJson($string) {
    return ((is_string($string) &&
        (is_object(json_decode($string)) ||
            is_array(json_decode($string))))) ? true : false;
}

function isOfValidClass($obj) {
    $classNames = [
        '\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException',
        '\InvalidArgumentException',
        'RouteNotFoundException',
        'ModelNotFoundException',
        'QueryException',
        '\ErrorException'
    ];

    foreach ($classNames as $className) {
        if (is_a($obj, $className)) {
            return true;
        }

        return false;
    }

    return false;
}

function convertData($data, $formatIn = 'DDMMYYY' ) {
    $formatIn = strtoupper( $formatIn );
    $retorno  = $data;
    switch( $formatIn ) {

        case 'DDMMYYY':
            $retorno = \Carbon\Carbon::createFromFormat('d/m/Y', $data)->format('Y-m-d');
            break;
        case 'YYYMMDD':
            $retorno = \Carbon\Carbon::createFromFormat('Y-m-d', $data)->format('d/m/Y');
            break;

        case 'YYYMMDDHHMMSS':
            $retorno = \Carbon\Carbon::parse( $data )->format('d/m/Y H:m:i');
            break;

    }

    return $retorno;
}

function utf8Ansi($valor='') {
    $utf8_ansi2 = array(
        "\u00c0" =>"À", "\u00c1" =>"Á", "\u00c2" =>"Â", "\u00c3" =>"Ã", "\u00c4" =>"Ä", "\u00c5" =>"Å", "\u00c6" =>"Æ", "\u00c7" =>"Ç", "\u00c8" =>"È", "\u00c9" =>"É",
        "\u00ca" =>"Ê", "\u00cb" =>"Ë", "\u00cc" =>"Ì", "\u00cd" =>"Í", "\u00ce" =>"Î", "\u00cf" =>"Ï", "\u00d1" =>"Ñ", "\u00d2" =>"Ò", "\u00d3" =>"Ó", "\u00d4" =>"Ô",
        "\u00d5" =>"Õ", "\u00d6" =>"Ö", "\u00d8" =>"Ø", "\u00d9" =>"Ù", "\u00da" =>"Ú", "\u00db" =>"Û", "\u00dc" =>"Ü", "\u00dd" =>"Ý", "\u00df" =>"ß", "\u00e0" =>"à",
        "\u00e1" =>"á", "\u00e2" =>"â", "\u00e3" =>"ã", "\u00e4" =>"ä", "\u00e5" =>"å", "\u00e6" =>"æ", "\u00e7" =>"ç", "\u00e8" =>"è", "\u00e9" =>"é", "\u00ea" =>"ê",
        "\u00eb" =>"ë", "\u00ec" =>"ì", "\u00ed" =>"í", "\u00ee" =>"î", "\u00ef" =>"ï", "\u00f0" =>"ð", "\u00f1" =>"ñ", "\u00f2" =>"ò", "\u00f3" =>"ó", "\u00f4" =>"ô",
        "\u00f5" =>"õ", "\u00f6" =>"ö", "\u00f8" =>"ø", "\u00f9" =>"ù", "\u00fa" =>"ú", "\u00fb" =>"û", "\u00fc" =>"ü", "\u00fd" =>"ý", "\u00ff" =>"ÿ", "\u00bf" =>"é"
    );

    $valor = str_replace('u00','\u00',$valor);

    return strtr(utf8_encode($valor), $utf8_ansi2);

}

function getEstados( $soState = FALSE ){
    $lista = [
        'AC' => 'ACRE', 'AL' => 'ALAGOAS', 'AP' => 'AMAPÁ', 'AM' => 'AMAZONAS', 'BA' => 'BAHIA', 'CE' => 'CEARÁ', 'DF' => 'DISTRITO FEDERAL', 'ES' => 'ESPÍRITO SANTO',
        'GO' => 'GOIÁS', 'MA' => 'MARANHÃO', 'MG' => 'MINAS GERAIS', 'MS' => 'MATO GROSSO DO SUL', 'MT' => 'MATO GROSSO', 'PA' => 'PARÁ', 'PB' => 'PARAÍBA',
        'PE' => 'PERNAMBUCO', 'PI' => 'PIAUÍ', 'PR' => 'PARANÁ', 'RJ' => 'RIO DE JANEIRO', 'RN' => 'RIO GRANDE DO NORTE', 'RO' => 'RONDÔNIA', 'RR' => 'RORAIMA',
        'RS' => 'RIO GRANDE DO SUL', 'SC' => 'SANTA CATARINA', 'SE' => 'SERGIPE', 'SP' => 'SÃO PAULO', 'TO' => 'TOCANTINS'
    ];

    if( $soState ){
        list($lista, $nome) = array_divide( $lista );
    }

    return $lista;
}

/**
 * Função que coleta o ip do visitante
 * @return string IP do visitante
 */
function getIp() {

    $ip = null;
    if ( isProduction() ) {

        $ip = array_get( $_SERVER, 'REMOTE_ADDR', NULL);

        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if (empty ($ip)) {
            if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }

        // Corrigindo situação onde IP retornar Proxy e IP
        if ( !empty($ip) && strpos($ip, ',') !== false ) {

            list($proxy, $addr) = explode(',', $ip);
            $ip = trim ( $proxy );
        }

        // Corrigindo situação onde IP retornar MAC Address
        if ( !empty ($ip) && preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $ip) === true ) {
            $ip = null;
        }
    }

    // Corrigindo situação onde IP seja localhost
    if ( $ip == "::1" || empty( $ip ) ) {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function getUserSession( $campo = null ){
    return \Facades\App\MyClass\SessionOpen::get($campo);
}

/**
 * Arrange for a flash message.
 *
 * @param  string           $metodo
 * @param  string|null      $param
 * @return string|array|bool
 */
function sessionOpen($metodo,$param=null)
{
    $app = app('SessionOpen');
    return $app->{$metodo}($param);
}

function getNameStatus( $status, $badge = FALSE ){
    $dados['L']    = ['title'=>'Liberado'      , 'badge' => 'success'];
    $dados['B']    = ['title'=>'Bloqueado'     , 'badge' => 'warning'];
    $dados['P']    = ['title'=>'Pendente'      , 'badge' => 'danger'];
    $dados['ROOT'] = ['title'=>'Administrador' , 'badge' => 'default'];
    $dados['USER'] = ['title'=>'Usuário' ];

    $texto = array_get( $dados, $status, $status );

    if( $status != $texto ) {
        return (!$badge ? $texto['title'] : (empty($texto['badge']) ? $texto['title'] : '<span class="badge badge-' . $texto['badge'] . '">' . $texto['title'] . '</span>'));
    } else {
        return $status;
    }
}

function getDDDs(){
    return [
        11, 12, 13, 14, 15, 16, 17, 18, 19,
        21, 22, 24, 27, 28,
        31, 32, 33, 34, 35, 37, 38,
        41, 42, 43, 44, 45, 46, 47, 48, 49,
        51, 53, 54, 55,
        61, 62, 63, 64, 65, 66, 67, 68, 69,
        71, 73, 74, 75, 77, 79,
        81, 82, 83, 84, 85, 86, 87, 88, 89,
        91, 92, 93, 94, 95, 96, 97, 98, 99
    ];
}

function escapeString($inp)
{
    if(is_array($inp)) return array_map(__METHOD__, $inp);

    if(!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }

    return $inp;
}

function formatBytes($size, $precision = 2)
{
    if ($size > 0) {
        $size = (int) $size;
        $base = log($size) / log(1024);
        $suffixes = array('bytes', 'KB', 'MB', 'GB', 'TB');

        return $suffixes[floor($base)];
    } else {
        return $size;
    }
}

function convertSize( $size, $for='MB' ){

    $origem = formatBytes( $size );
    if( $origem == 'KB' && $for == 'MB' ){
        return round($size/1024/1024,2);
    }

}

function serializeObj( $obj ){

    if ($obj instanceof \Illuminate\Http\Request){
        $all = $obj->all();
    } else {
        $all = objectToArray($obj);
    }

    $obj = new \StdClass();

    foreach ( $all AS $label => $valor ) {
        $obj->$label = $valor;
    }

    return base64_encode( serialize( $obj ) );
}

function unSerializeObj( $serialize ){
    return ( !is_array( $serialize ) ? objectToArray( unserialize( base64_decode( $serialize ) ) ) : $serialize );
}

#extrair parte de uma matriz com base num determinado idx
function pluckMatriz( $array, $idx, $setup=[] ){
    $dados = [];
    foreach ( $array AS $row ){
        $valor = array_get( $row, $idx, null );
        if( !empty( $valor ) ) {
            $dados[] = $valor;
        }
    }

    return $dados;
}

function loadFiles( $fileName ){

    $cacheFile = "loadFiles_" . $fileName;

    if( \Cache::has( $cacheFile ) ){
        $value = \Cache::get( $cacheFile );
    } else {
        $value = \File::getRequire(base_path().'/files/'.$fileName.'.php');
        \Cache::forever($cacheFile, $value);
    }

    return $value;
}

function getDiasSemanas(){
    return [
        'SEG' => 'Segunda',
        'TER' => 'Terça',
        'QUA' => 'Quarta',
        'QUI' => 'Quinta',
        'SEX' => 'Sexta',
        'SAB' => 'Sábado',
        'DOM' => 'Domingo',
    ];
}