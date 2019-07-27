<?php

namespace IntercaseDefault\MyClass\Contracts;

use Illuminate\Support\Facades\Route;

class Paginator{

    /**
     * set the number of items per page.
     *
     * @var numeric
     */
    private $_perPage;

    /**
     * set get parameter for fetching the page number
     *
     * @var string
     */
    private $_instance;

    /**
     * sets the page number.
     *
     * @var numeric
     */
    private $_page;

    /**
     * set the limit for the data source
     *
     * @var string
     */
    private $_limit;

    /**
     * set the total number of records/items.
     *
     * @var numeric
     */
    private $_totalRows = 0;

    private $baseURL = '';


    /**
     *  __construct
     *
     *  pass values when class is istantiated
     *
     * @param numeric  $_perPage  sets the number of iteems per page
     * @param numeric  $_instance sets the instance for the GET parameter
     */
    public function __construct($perPage=25){

        $routeName = Route::currentRouteName();

        $this->baseURL    = ( !empty( $routeName ) ? route( $routeName ) : base_path() );
        $this->_instance  = '{p}';
        $this->_perPage   = $perPage;
        $this->set_instance();
    }

    /**
     * get_start
     *
     * creates the starting point for limiting the dataset
     * @return numeric
     */
    public function get_start(){
        return ($this->_page * $this->_perPage) - $this->_perPage;
    }

    /**
     * set_instance
     *
     * sets the instance parameter, if numeric value is 0 then set to 1
     *
     * @var numeric
     */
    //get_instance() added my me//////////////////////////////
    public function set_page($page)
    {
        $this->_page = $page;
    }

    public function set_PerPage($num)
    {
        $this->_perPage = $num;
    }

    public function get_instance()
    {
        return $this->_page;
    }


    private function set_instance(){
        $this->_page = ($this->_page == 0 ? 1 : $this->_page);
    }

    /**
     * set_total
     *
     * collect a numberic value and assigns it to the totalRows
     *
     * @var numeric
     */
    public function set_total($_totalRows){
        $this->_totalRows = $_totalRows;
    }

    private function getURL(){
        return $this->baseURL;
    }

    /**
     * get_limit
     *
     * returns the limit for the data source, calling the get_start method and passing in the number of items perp page
     *
     * @return string
     */
    public function get_limit(){
        return "LIMIT ".$this->get_start().",$this->_perPage";
    }
    //modified for eloquent dbal=====================================================================
    public function get_limit2(){
        //return "LIMIT ".$this->get_start().",$this->_perPage";
        return $this->get_start();
    }
    public function get_perpage(){
        //return "LIMIT ".$this->get_start().",$this->_perPage";
        return $this->_perPage;
    }
    public function get_totalRows(){
        return $this->_totalRows;
    }
    public function checkMorePages(){
        return ( ( $this->_perPage * $this->_page ) < $this->_totalRows ? TRUE : FALSE );
    }

    //modified for eloquent dbal=====================================================================
    /**
     * page_links
     *
     * create the html links for navigating through the dataset
     *
     * @var sting $path optionally set the path for the link
     * @var sting $ext optionally pass in extra parameters to the GET
     * @return string returns the html menu
     */
    public function page_links( $viewTotal=FALSE, $ext='' )
    {
        $path = $this->getURL().'/';
        $adjacents = "2";
        $prev = $this->_page - 1;
        $next = $this->_page + 1;
        $lastpage = ceil($this->_totalRows/$this->_perPage);
        $lpm1 = $lastpage - 1;

        $pagination = "";
        if($lastpage > 1)
        {
            $pagination .= "<div align='right'><div class=\"btn-group\">";
            if ($this->_page > 1) {
                $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.$prev.$ext.'"><i class="fa fa-chevron-left"></i></button>';
            } else {
                $pagination .= '<button type="button" class="btn btn-white disabled"><i class="fa fa-chevron-left"></i></button>';
            }

            $pagination .= chr(13);

            if ($lastpage < 7 + ($adjacents * 2))
            {
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $this->_page) {
                        $pagination .= "<button class=\"btn btn-white active\">$counter</button>";
                    } else {
                        $pagination .= "<button class=\"btn btn-white paginator common-redirect\" data-route=\"".$path.$counter.$ext."\">$counter</button>";
                    }
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))
            {
                if($this->_page < 1 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                        /*<i class="fa fa-circle pull-right" style="font-size:6px;left:20px;color:#face9e"></i>*/
                    {
                        if ($counter == $this->_page) {
                            $pagination .= "<button class=\"btn btn-white active\">$counter</button>";
                        } else {
                            $pagination .= "<button class=\"btn btn-white small paginator common-redirect\" data-route=\"".$path.$counter.$ext."\">$counter</button>";
                        }
                    }
                    $pagination .= "";
                    $pagination .= "<button class=\"btn btn-white paginator common-redirect\" data-route=\"".$path.$lpm1.$ext."\">$lpm1</button>";
                    $pagination .= "<button class=\"btn btn-white paginator common-redirect\" data-route=\"".$path.$lastpage.$ext."\">$lastpage</button>";
                }
                elseif($lastpage - ($adjacents * 2) > $this->_page && $this->_page > ($adjacents * 2))
                {
                    $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.'1'.$ext.'">1</button>';
                    $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.'2'.$ext.'">2</button>';
                    $pagination .= '<button type="button" class="btn btn-white disabled">...</button>';

                    for ($counter = $this->_page - $adjacents; $counter <= $this->_page + $adjacents; $counter++)
                    {
                        if ($counter == $this->_page) {
                            $pagination .= '<button class="btn btn-white active">'.$counter.'</button>';
                        } else {
                            $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.$counter.$ext.'">'.$counter.'</button>';
                        }
                    }

                    $pagination .= '<button type="button" class="btn btn-white disabled">..</button>';
                    $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.$lpm1.$ext.'">'.$lpm1.'</button>';
                    $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.$lastpage.$ext.'">'.$lastpage.'</button>';
                }
                else
                {
                    $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.'1'.$ext.'">1</button>';
                    $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.'2'.$ext.'">2</button>';
                    $pagination .= '<button type="button" class="btn btn-white disabled">..</button>';

                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $this->_page) {
                            $pagination .= '<button type="button" class="btn btn-white active">'.$counter.'</button>';
                        } else {
                            $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.$counter.$ext.'">'.$counter.'</button>';
                        }
                    }
                }
            }

            //$pagination .= chr(13);

            if ($this->_page < $counter - 1) {
                $pagination .= '<button type="button" class="btn btn-white paginator common-redirect" data-route="'.$path.$next.$ext.'"><i class="fa fa-chevron-right"></i></button>';
            } else {
                $pagination .= '<button type="button" class="btn btn-white disabled"><i class="fa fa-chevron-right"></i></button>';
            }
            $pagination.= "</div></div>\n";
        }


        $pagination = str_replace('{p}=','/',$pagination);

        if( $viewTotal && !empty( $pagination ) ){
            $pagination .= '<p class="text-right"><small>Total de <strong>'.$this->_totalRows.'</strong> registros encontrados.</small></p>';
        }

        return $pagination;
    }
}