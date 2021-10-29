<?php
include_once($_SERVER['DOCUMENT_ROOT']. "/databaseconnectie/conn.php");
include_once($_SERVER['DOCUMENT_ROOT']. "/databaseconnectie/debug.php");
class CRUD{
    public $tabellen;
    public $TABLE_SCHEMA;
    public $link;
    public $href;
    public $chevron_double_right = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-double-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-chevron-double-right fa-w-16 fa-3x"><path fill="currentColor" d="M477.5 273L283.1 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.7-22.7c-9.4-9.4-9.4-24.5 0-33.9l154-154.7-154-154.7c-9.3-9.4-9.3-24.5 0-33.9l22.7-22.7c9.4-9.4 24.6-9.4 33.9 0L477.5 239c9.3 9.4 9.3 24.6 0 34zm-192-34L91.1 44.7c-9.4-9.4-24.6-9.4-33.9 0L34.5 67.4c-9.4 9.4-9.4 24.5 0 33.9l154 154.7-154 154.7c-9.3 9.4-9.3 24.5 0 33.9l22.7 22.7c9.4 9.4 24.6 9.4 33.9 0L285.5 273c9.3-9.4 9.3-24.6 0-34z" class=""></path></svg>';
    public $search = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-search fa-w-16 fa-3x"><path fill="currentColor" d="M508.5 481.6l-129-129c-2.3-2.3-5.3-3.5-8.5-3.5h-10.3C395 312 416 262.5 416 208 416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c54.5 0 104-21 141.1-55.2V371c0 3.2 1.3 6.2 3.5 8.5l129 129c4.7 4.7 12.3 4.7 17 0l9.9-9.9c4.7-4.7 4.7-12.3 0-17zM208 384c-97.3 0-176-78.7-176-176S110.7 32 208 32s176 78.7 176 176-78.7 176-176 176z" class=""></path></svg>';
    public $filter = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="filter" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-filter fa-w-16 fa-3x"><path fill="currentColor" d="M479.968 0H32.038C3.613 0-10.729 34.487 9.41 54.627L192 237.255V424a31.996 31.996 0 0 0 10.928 24.082l64 55.983c20.438 17.883 53.072 3.68 53.072-24.082V237.255L502.595 54.627C522.695 34.528 508.45 0 479.968 0zM288 224v256l-64-56V224L32 32h448L288 224z" class=""></path></svg>';
    public function __construct($tabellen,$link,$href,$TABLE_SCHEMA){
        //check if is array
        if(!is_array($tabellen)){
            throw new Exception("Lijst met tabellen moeten als Array worden opgegeven");
        }
        $this->link = $link;
        $this->href = $href;
        $tabellen_as_objects = array();
        foreach($tabellen as $tabel){
            //check if is string
            if(!is_string($tabel)){
                throw new Exception("Tabel moet is niet als tekst meegeven.");
            }
            $tabel_as_object = new Tabel($tabel,$TABLE_SCHEMA,$link,$this->href);
            array_push($tabellen_as_objects,$tabel_as_object);
        }
        $this->tabellen = $tabellen_as_objects;
        $this->TABLE_SCHEMA = $TABLE_SCHEMA;
    }

    public function __get($TABLE_NAME){
        foreach($this->tabellen as $tabel){
            if($tabel->TABLE_NAME == $TABLE_NAME){
                return $tabel;
            }
        }
        throw new Exception("tabel bestaat niet, gelieve een andere tabel te kiezen.");
    }

    public function renderNavbar($active_TABLE_NAME=null,$search=null,$update_or_delete=null,$create=null){
        $navbar = "";
        $navbar .='<nav class="navbar navbar-expand-lg navbar-light bg-light">';
        // $navbar .='<div class="navbar-brand" href="#">CRUD paneel</div>';
        $navbar .='<ul class="navbar-nav mr-auto row">';
        $navbar .='<li class="nav-item dropdown col" style="list-style-type:none;">';
        $navbar .='<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        if(isset($active_TABLE_NAME)){
            $navbar .= $active_TABLE_NAME;
        }else{
            $navbar .= "Selecteer een tabel";
        }
        $navbar .='</a>';
        $navbar .='<div class="dropdown-menu list-group" aria-labelledby="navbarDropdown">';
        foreach($this->tabellen as $tabel){
            //current path plus table query string
            $navbar .='<a class="dropdown-item text-reset list-group-item list-group-item-action '.($tabel->TABLE_NAME == $active_TABLE_NAME?"active text-white":"text-muted").'" href="'.$this->href.'?table='.$tabel->TABLE_NAME.'">';
            $navbar .= $tabel->TABLE_NAME;
            $navbar .='</a>';
        }
        $navbar .='</div>';
        $navbar .='</li>';
        if(isset($active_TABLE_NAME)){
            if(isset($update_or_delete) || isset($create)){
                $navbar .='<li class="nav-item col" style="list-style-type:none;">';
                $navbar .='<p class="nav-link active">';
                $navbar .='<b>Verwijder of wijzig entry</b>';
                $navbar .='</p>';
                $navbar .='</li>';
            } else if(isset($active_TABLE_NAME)){
                $navbar .='<li class="nav-item col" style="list-style-type:none;">';
                $navbar .='<a href="'.$this->href.'?table='.$active_TABLE_NAME.'&create=1"'.(isset($search) ? '&search='.$search : '').' class="nav-link">';
                $navbar .='<button type="button" class="btn btn-primary">Nieuw record</button>';
                $navbar .='</a>';
                $navbar .='</li>';

                $navbar .='<li class="nav-item col" style="list-style-type:none;">';
                $navbar .='<form class="form-inline my-2 my-lg-0" action="'.$this->href.'?table='.$active_TABLE_NAME;
                $navbar .=isset($search) ? '&search='.$search : '';
                $navbar .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
                $navbar .=isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
                $navbar .=isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
                $navbar .=isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
                $navbar .='" method="get">';
                $navbar .='<input class="form-control mr-sm-2" type="search" placeholder="Zoeken" aria-label="Search" name="search"';
                if(isset($search)){
                    $navbar .='value="'.$search.'"';
                }
                $navbar .='>';
                if(isset($_REQUEST["table"])){$navbar .='<input type="hidden" name="table" value="'.$active_TABLE_NAME.'">';}
                if(isset($_REQUEST["sort_direction"])){$navbar .='<input type="hidden" name="sort_direction" value="'.$_REQUEST["sort_direction"].'">';}
                if(isset($_REQUEST["page"])){$navbar .='<input type="hidden" name="page" value="'.$_REQUEST["page"].'">';}
                if(isset($_REQUEST["sort_column_name"])){$navbar .='<input type="hidden" name="sort_column_name" value="'.$_REQUEST["sort_column_name"].'">';}
                if(isset($_REQUEST["limit"])){$navbar .='<input type="hidden" name="limit" value="'.$_REQUEST["limit"].'">';}
                $navbar .='<button class="btn btn-outline-success my-2 my-sm-0" type="submit">'.$this->search.'</button>';
                $navbar .='</form>';
                $navbar .='</li>';

                $navbar .='<li class="nav-item col" style="list-style-type:none;">';
                $navbar .='<form class="nav-link form-inline my-2 my-lg-0" action="'.$this->href.'?table='.$active_TABLE_NAME;
                $navbar .=isset($search) ? '&search='.$search : '';
                $navbar .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
                $navbar .=isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
                $navbar .=isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
                $navbar .=isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
                $navbar .='" method="get">';
                if(isset($_REQUEST["search"])){
                    $navbar .='<input type="hidden" name="search" value="'.$_REQUEST["search"].'">';
                }
                if(isset($_REQUEST["table"])){$navbar .='<input type="hidden" name="table" value="'.$active_TABLE_NAME.'">';}
                if(isset($_REQUEST["sort_column_name"])){$navbar .='<input type="hidden" name="sort_column_name" value="'.$_REQUEST["sort_column_name"].'">';}
                if(isset($_REQUEST["sort_direction"])){$navbar .='<input type="hidden" name="sort_direction" value="'.$_REQUEST["sort_direction"].'">';}
                if(isset($_REQUEST["limit"])){$navbar .='<input type="hidden" name="limit" value="'.$_REQUEST["limit"].'">';}
                if(isset($_REQUEST["search"])){
                    $navbar .='<input type="hidden" name="search" value="'.$_REQUEST["search"].'">';
                }
                $navbar .='<input class="form-control mr-sm-2" type="number" step=1 placeholder="Pagina" aria-label="Pagina" name="page"';
                if(isset($_REQUEST["page"])){
                    $navbar .='value="'.$_REQUEST["page"].'"';
                }else{
                    $navbar .='value="1"';
                    $_REQUEST["page"] = 1;
                }
                $navbar .='>';
                $navbar .='<input type="hidden" name="table" value="'.$active_TABLE_NAME.'">';
                $navbar .='<button class="btn btn-outline-success my-2 my-sm-0" type="submit">'."Pagina".'</button>';
                $navbar .='</form>';
                $navbar .='</li>';

                $navbar .='<li class="nav-item col" style="list-style-type:none;">';
                $navbar .='<form class="nav-link form-inline my-2 my-lg-0" action="'.$this->href.'?table='.$active_TABLE_NAME;
                $navbar .=isset($search) ? '&search='.$search : '';
                $navbar .=isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
                $navbar .=isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
                $navbar .=isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
                $navbar .=isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
                $navbar .='" method="get">';
                if(isset($_REQUEST["table"])){$navbar .='<input type="hidden" name="table" value="'.$active_TABLE_NAME.'">';}
                if(isset($_REQUEST["sort_direction"])){$navbar .='<input type="hidden" name="sort_direction" value="'.$_REQUEST["sort_direction"].'">';}
                if(isset($_REQUEST["sort_column_name"])){$navbar .='<input type="hidden" name="sort_column_name" value="'.$_REQUEST["sort_column_name"].'">';}
                if(isset($_REQUEST["page"])){$navbar .='<input type="hidden" name="page" value="'.$_REQUEST["page"].'">';}
                if(isset($_REQUEST["search"])){
                    $navbar .='<input type="hidden" name="search" value="'.$_REQUEST["search"].'">';
                }
                $navbar .='<input class="form-control mr-sm-2" type="number" step=1 placeholder="Aantal records" aria-label="Aantal" name="limit"';
                if(isset($_REQUEST["limit"])){
                    $navbar .='value="'.$_REQUEST["limit"].'"';
                }else{
                    $navbar .='value="50"';
                    $_REQUEST["limit"] = 50;
                }
                $navbar .='>';
                $navbar .='<input type="hidden" name="table" value="'.$active_TABLE_NAME.'">';
                $navbar .='<button class="btn btn-outline-success my-2 my-sm-0" type="submit">'."Aantal per pagina".'</button>';
                $navbar .='</form>';
                $navbar .='</li>';
            }
        }
        $navbar .='</ul>';
        $navbar .='</div>';
        $navbar .='</nav>';
        return $navbar;
    }
    public function renderTable($TABLE_NAME, $search=null){
        return $this->__get($TABLE_NAME)->renderTable($search,
            isset($_REQUEST['sort_column_name']) ? $_REQUEST['sort_column_name'] : null,
            isset($_REQUEST['sort_direction']) ? $_REQUEST['sort_direction'] : null,
            isset($_REQUEST['limit'] ) ? $_REQUEST['limit'] : null,
            isset($_REQUEST['page'] ) ? $_REQUEST['page'] : null
        );
    }

    public function renderPaneel(){
        try{
            if($_SERVER['REQUEST_METHOD'] == "GET"){
                if(!is_string($this->href) || empty($this->href)){
                    throw new Exception("Parameter 'href' moet een string zijn en niet leeg zijn, dit zou de default locatie moeten zijn waar het paneel wordt getoont.");
                }
                if(!isset($_REQUEST["page"]) && !isset($_REQUEST["limit"]) && !isset($_REQUEST["sort_direction"]) && isset($_REQUEST["table"])
                    && !isset($_REQUEST["create"])
                    && !isset($_REQUEST["update"])
                    && !isset($_REQUEST["delete"])
                    && !isset($_REQUEST["update_or_delete"])
                ){
                    $_REQUEST["page"] = 1;
                    $_REQUEST["limit"] = 50;
                    $_REQUEST["sort_direction"] = "asc";
                    echo "<script>window.location.href = '".$this->href."?table=".$_REQUEST["table"]."&page=1&limit=50&sort_direction=asc';</script>";
                }

                echo $this->renderNavbar(isset($_REQUEST["table"])?$_REQUEST["table"]:null,isset($_REQUEST["search"])?$_REQUEST["search"]:null
                    ,isset($_REQUEST["update_or_delete"])?$_REQUEST["update_or_delete"]:null,
                    isset($_REQUEST["create"])?$_REQUEST["create"]:null);
                if(isset($_REQUEST["table"])){
                    if(isset($_REQUEST["update_or_delete"]) && isset($_REQUEST["primary_key_value"]) && isset($_REQUEST["primary_key_name"])){
                        echo $this->__get($_REQUEST["table"])->renderUpdate($_REQUEST["primary_key_value"],$_REQUEST["primary_key_name"]);
                    } else if(isset($_REQUEST["create"]) && $_REQUEST["create"] == 1){
                        echo $this->__get($_REQUEST["table"])->renderCreate();
                    } else{
                        echo $this->renderTable($_REQUEST["table"],isset($_REQUEST["search"])?$_REQUEST["search"]:null);
                    }
                }else{
                    echo '<div class="alert alert-warning" role="alert">';
                    echo '<strong>Geen tabel gekozen. Gelieve een tabel te kiezen van de dropdown</strong> ';
                    echo '</div>';
                    return;
                }
            }else if($_SERVER['REQUEST_METHOD'] == "POST"){
                if(!is_string($this->href) || empty($this->href)){
                    throw new Exception("Parameter 'href' moet een string zijn en niet leeg zijn, dit zou de default locatie moeten zijn waar het paneel wordt getoont.");
                }
                if(!isset($_POST["table"])){
                    throw new Exception("table moet een string zijn");
                }
                if(isset($_POST["create"]) && $_POST["create"] == 1){
                    $this->__get($_POST["table"])->create($_POST);
                }else if (isset($_POST["update"]) && $_POST["update"] == 1){
                    $this->__get($_POST["table"])->update($_POST);
                }else if (isset($_POST["delete"]) && $_POST["delete"] == 1){
                    $this->__get($_POST["table"])->delete($_REQUEST["primary_key_name"],$_REQUEST["primary_key_value"]);
                }else{
                    throw new Exception("Er is geen actie geselecteerd. Formulier is verkeerd binnengekomen");
                }
                $location =$this->href."?table=".$_POST["table"].(isset($_POST["search"])?"&search=".$_POST["search"]:"");
                $location .=isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
                $location .=isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
                $location .=isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
                $location .=isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
                header("Location: ".$location);
                return;
            }else{
                echo '<div class="alert alert-warning" role="alert">';
                echo '<strong>Geen tabel gekozen. Gelieve een tabel te kiezen van de dropdown</strong> ';
                echo '</div>';
                return;
            }
        }catch(Exception $e){
            echo '<div class="alert alert-danger" role="alert">';
            echo '<strong>Fout!</strong> '.$e->getMessage();
            echo '<div class="dropdown-divider"></div>';
            echo '<a class="dropdown-item" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo '<span class="text-danger">Informatie foutmelding:</span>';
            echo '</a>';
            echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">';
            echo '<pre>';
            echo $e->getTraceAsString();
            echo '</pre>';
            echo '</div>';
            echo '</div>';
        }
    }
}
class Tabel{
    public $TABLE_NAME;
    public $TABLE_SCHEMA;
    public $link;
    public $href;
    public $headers = array();
    private $sort = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fad" data-icon="sort" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-sort fa-w-10 fa-3x"><g class="fa-group"><path fill="#e1e1e1" d="M279.05 288.05h-238c-21.4 0-32.07 25.95-17 41l119.1 119 .1.1a23.9 23.9 0 0 0 33.8-.1l119-119c15.1-15.05 4.4-41-17-41zm-238-64h238c21.4 0 32.1-25.9 17-41l-119-119a.94.94 0 0 0-.1-.1 23.9 23.9 0 0 0-33.8.1l-119.1 119c-15.05 15.1-4.4 41 17 41z" class="fa-secondary"></path><path fill="#e1e1e1" d="" class="fa-primary"></path></g></svg>';
    private $sort_up ='<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fad" data-icon="sort-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-sort-up fa-w-10 fa-3x"><g class="fa-group"><path fill="#e1e1e1" d="M41.05 288.05h238c21.4 0 32.1 25.9 17 41l-119 119a23.9 23.9 0 0 1-33.8.1l-.1-.1-119.1-119c-15.05-15.05-4.4-41 17-41z" class="fa-secondary"></path><path fill="#158caf" d="M24.05 183.05l119.1-119A23.9 23.9 0 0 1 177 64a.94.94 0 0 1 .1.1l119 119c15.1 15.1 4.4 41-17 41h-238c-21.45-.05-32.1-25.95-17.05-41.05z" class="fa-primary"></path></g></svg>';
    private $sort_down = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fad" data-icon="sort-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-sort-down fa-w-10 fa-3x"><g class="fa-group"><path fill="#e1e1e1" d="M279.07 224.05h-238c-21.4 0-32.1-25.9-17-41l119-119a23.9 23.9 0 0 1 33.8-.1l.1.1 119.1 119c15.07 15.05 4.4 41-17 41z" class="fa-secondary"></path><path fill="#158caf" d="M296.07 329.05L177 448.05a23.9 23.9 0 0 1-33.8.1l-.1-.1-119-119c-15.1-15.1-4.4-41 17-41h238c21.37 0 32.04 25.95 16.97 41z" class="fa-primary"></path></g></svg>';
    private $sort_column_name = "";
    private $sort_direction = "asc";
    private $limit = 50;
    private $page = 1;
    public function __construct($TABLE_NAME,$TABLE_SCHEMA,$link,$href){
        $qry =   "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$TABLE_SCHEMA."' AND TABLE_NAME = '".$TABLE_NAME."';";
        $result = mysqli_query($GLOBALS["link"], $qry);
        // error_log("CRUD:".$qry);
        while($row = mysqli_fetch_assoc($result)){
            $header = new Header ($row);
            array_push($this->headers, $header);
        }
        $this->TABLE_NAME = $TABLE_NAME;
        $this->TABLE_SCHEMA = $TABLE_SCHEMA;
        $this->link = $link;
        $this->href = $href;
    }
    public function __toString(){
        $string = "";
        foreach($this->headers as $header){
            $string .= $header;
        }
        return $string;
    }
    public function getData($search=null){
        $qry = "SELECT * FROM `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` ";
        if(isset($search)){
            $qry .= "WHERE ";
            $first = true;
            foreach($this->headers as $header){
                if($first){
                    $qry .= $header->COLUMN_NAME." LIKE '%".$search."%'";
                    $first = false;
                }else{
                    $qry .= " OR ".$header->COLUMN_NAME." LIKE '%".$search."%'";
                }
            }
        }
        if(isset($this->sort_column_name) && $this->sort_column_name != ""){
            $qry .= " ORDER BY ".$this->sort_column_name;
            if(isset($this->sort_direction) && ($this->sort_direction == "asc" || $this->sort_direction == "desc")){
                $qry .= " ".$this->sort_direction;
            }else{
                $qry .= " ASC";
            }
        }
        if(isset($this->limit) && $this->limit > 0){
            $qry .= " LIMIT ".$this->limit;
        }else{
            $qry .= " LIMIT 50";
        }
        if(isset($this->page) && $this->page > 0 && $this->limit > 0){
            $offset = ($this->page -1) * $this->limit;
            $qry .= " OFFSET ".$offset;
        }else{
            $qry .= " OFFSET 0";
        }
        $result = mysqli_query($this->link, $qry);
        $data = array();
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        error_log("CRUD:".$qry);
        return $data;
    }

    public function getDataByPrimaryKey($PRIMARY_KEY_NAME,$PRIMARY_KEY_VALUE){
        $qry = "SELECT * FROM `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` WHERE `".$PRIMARY_KEY_NAME."` = '".$PRIMARY_KEY_VALUE."';";
        $result = mysqli_query($this->link, $qry);
        $data = array();
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        error_log("CRUD:".$qry);
        return $data;
    }

    public function getPrimaryKeys(){
        $qry ="SHOW KEYS FROM `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` WHERE Key_name = 'PRIMARY';";
        $result = mysqli_query($this->link, $qry);
        $data = array();
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        //      error_log("CRUD:".$qry);
        return $data;
    }
    public function getFirstPrimaryKeyName(){
        $primaryKeys = array(current($this->getPrimaryKeys()));
        return $primaryKeys[0]["Column_name"];
    }
    public function renderTable($search=null,$sort_column_name="",$sort_direction="",$limit=50,$page=1){
        $this->sort_column_name = $sort_column_name;
        $this->sort_direction = $sort_direction;
        $this->limit = $limit;
        $this->page = $page;
        $data = $this->getData($search);
        $string = "<table class='table table-striped table-bordered table-hover'>";
        $string .= "<thead>";
        $string .= "<tr>";
        foreach($this->headers as $header){
            $string .= "<th>";
            $string .="<div style='display: flex;justify-content: space-between;align-items: center;'>";
            $string .="<div>";
            $string .= $header;
            $string .= "<br/>";
            $string .= "<small>".$header->COLUMN_TYPE."</small>";
            $string .= "</div>";
            $string .="<div>";
            if($this->sort_column_name == $header->COLUMN_NAME){
                if($this->sort_direction == "asc"){
                    $string .= "<a class='text-muted' href='".$this->href."?";
                    $string .= isset($_REQUEST["search"])?"search=".$_REQUEST["search"]."&":null;
                    $string .= "sort_column_name=".$header->COLUMN_NAME."&sort_direction=desc&limit=".$this->limit."&page=".$this->page."&table_name=".$this->TABLE_NAME."&table=".$this->TABLE_NAME."'>";
                    $string .= $this->sort_up;
                    $string .= "</a>";
                }else{
                    $string .= "<a class='text-muted' href='".$this->href."?";
                    $string .= isset($_REQUEST["search"])?"search=".$_REQUEST["search"]."&":null;
                    $string .= "sort_column_name=".$header->COLUMN_NAME."&sort_direction=asc&limit=".$this->limit."&page=".$this->page."&table_name=".$this->TABLE_NAME."&table=".$this->TABLE_NAME."'>";
                    $string .= $this->sort_down;
                    $string .= "</a>";
                }
            }else{
                $string .= "<a class='text-muted' href='".$this->href."?";
                $string .= isset($_REQUEST["search"])?"search=".$_REQUEST["search"]."&":null;
                $string .= "sort_column_name=".$header->COLUMN_NAME."&sort_direction=asc&limit=".$this->limit."&page=".$this->page."&table_name=".$this->TABLE_NAME."&table=".$this->TABLE_NAME."'>";
                $string .= $this->sort;
                $string .= "</a>";
            }
            $string .="</div>";
            $string .="</div>";
            $string .="</th>";
        }
        $string .= "</tr>";
        $string .= "</thead>";
        $string .= "<tbody>";
        foreach($data as $row){
            /*add primary key value and primary key name to the href*/
            $string .= "<tr style='cursor:pointer;'";
            $string .='onclick="window.location.href=\''.$this->href."?table=".$this->TABLE_NAME."&update_or_delete=1".(isset($search)?"&search=".$search:"");
            $string .='&primary_key_name='.$this->getFirstPrimaryKeyName().'&primary_key_value='.$row[$this->getFirstPrimaryKeyName()].'';
            $string .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
            $string .=isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
            $string .=isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
            $string .=isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
            $string .= "'\">";
            foreach($row as $key => $value){
                $string .= "<td id='".$key."'>".$value."</td>";
            }
            $string .= "</tr>";
        }
        $string .= "</tbody>";
        $string .= "</table>";
        return $string;
    }
    public function renderCreate(){
        $string = "";
        $string .= "<div class='col-md-12'>";
        $string .= "<form action='".$this->href."?table=".$this->TABLE_NAME."' method='POST'>";
        $string .= "<div class='row' style='padding: 22px;border: 1px solid #b7b7b7;margin: 10px;border-radius: 5px;'>";
        foreach($this->headers as $header){
            $string .= $header->renderCreate();
        }
        $string .= "<input type='hidden' name='table' value='".$_REQUEST["table"]."'>";
        if(isset($_REQUEST["sort_direction"])){$string .='<input type="hidden" name="sort_direction" value="'.$_REQUEST["sort_direction"].'">';}
        if(isset($_REQUEST["page"])){$string .='<input type="hidden" name="page" value="'.$_REQUEST["page"].'">';}
        if(isset($_REQUEST["sort_column_name"])){$string .='<input type="hidden" name="sort_column_name" value="'.$_REQUEST["sort_column_name"].'">';}
        if(isset($_REQUEST["limit"])){$string .='<input type="hidden" name="limit" value="'.$_REQUEST["limit"].'">';}
        if(isset($_REQUEST["search"])){$string .='<input type="hidden" name="limit" value="'.$_REQUEST["search"].'">';}
        $string .= "<input type='hidden' name='create' value='1'>";
        isset($_REQUEST["search"]) ? $string .= "<input type='hidden' name='search' value='".$_REQUEST["search"]."'>":null;
        $string .= "</div>";
        $string .= "<div class='col-md-12' style='padding: 22px;border: 1px solid #b7b7b7;margin: 10px;border-radius: 5px;'>";
        $string .= "<button class='btn btn-primary btn-block' type='submit' name='creating' value='Aanmaken' >";
        $string .= "Aanmaken";
        $string .= "</button>";
        $string .=  "<a href='".$this->href."?table=".$this->TABLE_NAME;
        $string .= isset($_REQUEST["search"]) ? '&search='.$_REQUEST["search"] : '';
        $string .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
        $string .= isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
        $string .= isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
        $string .= isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
        $string .= "' class='btn btn-danger btn-block'>Annuleren</a>";
        $string .= "</div>";
        $string .= "</form>";
        $string .= "</div>";
        return $string;
    }

    public function renderUpdate($PRIMARY_KEY_VALUE,$PRIMARY_KEY_NAME){
        $string = "";
        $string .= "<div class='col-md-12'>";
        $string .= "<form action='".$this->href."?table=".$this->TABLE_NAME;
        $string .= isset($_REQUEST["search"]) ? '&search='.$_REQUEST["search"] : '';
        $string .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
        $string .= isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
        $string .= isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
        $string .= isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
        $string .="' method='POST'>";
        $string .= "<div class='row' style='padding: 22px;border: 1px solid #b7b7b7;margin: 10px;border-radius: 5px;'>";
        foreach($this->headers as $header){
            $dataOfCurrentEntry = current($this->getDataByPrimaryKey($PRIMARY_KEY_NAME,$PRIMARY_KEY_VALUE));
            if($header->COLUMN_NAME != $PRIMARY_KEY_NAME){
                error_log($header->COLUMN_NAME);
                error_log($dataOfCurrentEntry[$header->COLUMN_NAME]);
                $string .= $header->renderUpdate($dataOfCurrentEntry[$header->COLUMN_NAME]);
            }else{
                $string .= "<input type='hidden' name='primary_key_name' value='".$PRIMARY_KEY_NAME."'>";
                $string .= "<input type='hidden' name='primary_key_value' value='".$PRIMARY_KEY_VALUE."'>";
            }
        }
        if(isset($_REQUEST["sort_direction"])){$string .='<input type="hidden" name="sort_direction" value="'.$_REQUEST["sort_direction"].'">';}
        if(isset($_REQUEST["page"])){$string .='<input type="hidden" name="page" value="'.$_REQUEST["page"].'">';}
        if(isset($_REQUEST["sort_column_name"])){$string .='<input type="hidden" name="sort_column_name" value="'.$_REQUEST["sort_column_name"].'">';}
        if(isset($_REQUEST["limit"])){$string .='<input type="hidden" name="limit" value="'.$_REQUEST["limit"].'">';}
        if(isset($_REQUEST["search"])){$string .='<input type="hidden" name="limit" value="'.$_REQUEST["search"].'">';}
        $string .= "<input type='hidden' name='table' value='".$_REQUEST["table"]."'>";
        $string .= "<input type='hidden' name='update' value='1'>";
        if(isset($search)){
            $string .= "<input type='hidden' name='search' value='".$search."'>";
        }
        $string .= "</div>";
        $string .= "<div class='col-md-12' style='padding: 22px;border: 1px solid #b7b7b7;margin: 10px;border-radius: 5px;'>";
        $string .= "<input class='btn btn-primary btn-block' type='submit' value='Opslaan' />";
        $string .=  "<a href='".$this->href."?table=".$this->TABLE_NAME;
        $string .= isset($_REQUEST["search"]) ? '&search='.$_REQUEST["search"] : '';
        $string .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
        $string .= isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
        $string .= isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
        $string .= isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
        $string .= "' class='btn btn-warning btn-block'>Annuleren</a>";
        $string .= "</div>";
        $string .= "</form>";
        $string .='<form action="'.$this->href."?table=".$this->TABLE_NAME;
        $string .= isset($_REQUEST["search"]) ? '&search='.$_REQUEST["search"] : '';
        $string .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
        $string .= isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
        $string .= isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
        $string .= isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
        $string .='" method="post">';
        $string .= "<div class='row' style='padding: 22px;border: 1px solid #b7b7b7;margin: 10px;border-radius: 5px;'>";
        $string .='<input type="hidden" name="table" value="'.$this->TABLE_NAME.'">';
        $string .='<input type="hidden" name="delete" value="1">';
        $string .='<input type="hidden" name="primary_key_value" value="'.$PRIMARY_KEY_VALUE.'">';
        $string .='<input type="hidden" name="primary_key_name" value="'.$PRIMARY_KEY_NAME.'">';
        $string .='<input type="submit" value="Verwijder" class="btn btn-danger btn-block">';
        $string .='</form>';
        $string .= "</div>";
        return $string;
    }

    public function delete($PRIMARY_KEY_NAME,$PRIMARY_KEY_VALUE){
        $qry = "DELETE FROM `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` WHERE `".$PRIMARY_KEY_NAME."` = '".$PRIMARY_KEY_VALUE."';";
        mysqli_query($this->link, $qry);
        error_log("CRUD:".$qry);
        return;
    }

    public function update($data){
        $qry = "UPDATE `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` SET ";
        $first = true;
        foreach($data as $key => $value){
            $header_info = $this->getHeaderInfo($key);
            if(isset($header_info["DATA_TYPE"]) && $header_info["COLUMN_KEY"] != "PRI"){
                if($value == ""){
                    $value = "NULL";
                }
                if($first){
                    switch($header_info["DATA_TYPE"]){
                        case "int":
                        case "tinyint":
                        case "smallint":
                        case "mediumint":
                        case "bigint":
                        case "float":
                        case "double":
                        case "decimal":
                        case "real":
                        case "bit":
                        case "bool":
                        case "boolean":
                            $qry .= "`".$key."` = ". $value;
                            break;
                        case "varchar":
                        case "text":
                        case "date":
                        case "datetime":
                        case "timestamp":
                        case "time":
                        case "year":
                        case "char":
                        case "tinytext":
                        case "mediumtext":
                        case "longtext":
                        case "enum":
                            $qry .= "`".$key."` = '".$value."'";
                            break;
                        default:
                            $qry .= "`".$key."` = ". $value;
                            break;
                    }
                    $first = false;
                }else{
                    switch($header_info["DATA_TYPE"]){
                        case "int":
                        case "tinyint":
                        case "smallint":
                        case "mediumint":
                        case "bigint":
                        case "float":
                        case "double":
                        case "decimal":
                        case "real":
                        case "bit":
                        case "bool":
                        case "boolean":
                            ",`".$key."` = ".$value;
                            break;
                        case "varchar":
                        case "text":
                        case "date":
                        case "datetime":
                        case "timestamp":
                        case "time":
                        case "year":
                        case "char":
                        case "tinytext":
                        case "mediumtext":
                        case "longtext":
                        case "enum":
                            ",`".$key."` = '".$value."'";
                            break;
                        default:
                            ",`".$key."` = ".$value;
                            break;
                    }
                }
            }
        }
        $qry .= " WHERE `".$data["primary_key_name"]."` = ".$data["primary_key_value"].";";
        $result = mysqli_query($this->link, $qry);
        error_log("CRUD:".$qry);
        error_log("CRUD:".json_encode($data));
        return;
    }

    public function create($data){
        if(!isset($data) || !is_array($data)){
            throw new Exception("Parameter 'data' moet een Array zijn en mag niet leeg zijn");
        }
        $qry = "INSERT INTO `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` (";
        $first = true;
        foreach($this->headers as $header){
            $header_info = $this->getHeaderInfo($header->COLUMN_NAME);
            if(isset($header_info["DATA_TYPE"]) && $header_info["COLUMN_KEY"] != "PRI") {
                if ($first) {
                    $qry .= "`" . $header->COLUMN_NAME . "`";
                    $first = false;
                } else {
                    $qry .= ", `" . $header->COLUMN_NAME . "`";
                }
            }
        }
        $qry .= ") VALUES (";
        $first = true;
        foreach($data as $key => $value){
            $header_info = $this->getHeaderInfo($key);
            if(isset($header_info["DATA_TYPE"]) && $header_info["COLUMN_KEY"] != "PRI"){
                if($value == ""){
                    $value = "NULL";
                }
                if($first){
                    switch($header_info["DATA_TYPE"]){
                        case "int":
                        case "tinyint":
                        case "smallint":
                        case "mediumint":
                        case "bigint":
                        case "float":
                        case "double":
                        case "decimal":
                        case "real":
                        case "bit":
                        case "bool":
                        case "boolean":
                            $qry .= $value;
                            break;
                        case "varchar":
                        case "text":
                        case "date":
                        case "datetime":
                        case "timestamp":
                        case "time":
                        case "year":
                        case "char":
                        case "tinytext":
                        case "mediumtext":
                        case "longtext":
                        case "enum":
                            $qry .= "'".$value."'";
                            break;
                        default:
                            $qry .= $value;
                            break;
                    }
                    $first = false;
                }else{
                    switch($header_info["DATA_TYPE"]){
                        case "int":
                        case "tinyint":
                        case "smallint":
                        case "mediumint":
                        case "bigint":
                        case "float":
                        case "double":
                        case "decimal":
                        case "real":
                        case "bit":
                        case "bool":
                        case "boolean":
                            $qry .= ",".$value;
                            break;
                        case "varchar":
                        case "text":
                        case "date":
                        case "datetime":
                        case "timestamp":
                        case "time":
                        case "year":
                        case "char":
                        case "tinytext":
                        case "mediumtext":
                        case "longtext":
                        case "enum":
                            $qry .= ",'".$value."'";
                            break;
                        default:
                            $qry .= ",".$value;
                            break;
                    }
                }
            }
        }
        $qry .= ");";
        $result = mysqli_query($this->link, $qry);
        if(!$result){
            throw new Exception("Error bij het aanmaken van een nieuw record: ".mysqli_error($this->link));
        }
        error_log("CRUD:".$qry);
        return;
    }

    private function getHeaderInfo($header){
        $qry = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->TABLE_SCHEMA."' AND TABLE_NAME = '".$this->TABLE_NAME."' AND COLUMN_NAME = '".$header."'";
        $result = mysqli_query($this->link, $qry);
        if(!$result){
            throw new Exception("Error bij het krijgen van header info '".$header."': ".mysqli_error($this->link));
        }
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
}
class Header{
    public $TABLE_CATALOG;
    public $TABLE_SCHEMA;
    public $TABLE_NAME;
    public $COLUMN_NAME;
    public $ORDINAL_POSITION;
    public $COLUMN_DEFAULT;
    public $IS_NULLABLE;
    public $DATA_TYPE;
    public $CHARACTER_MAXIMUM_LENGTH;
    public $CHARACTER_OCTET_LENGTH;
    public $NUMERIC_PRECISION;
    public $NUMERIC_SCALE;
    public $CHARACTER_SET_NAME;
    public $COLLATION_NAME;
    public $COLUMN_TYPE;
    public $COLUMN_KEY;
    public $EXTRA;
    public $PRIVILEGES;
    public $COLUMN_COMMENT;
    public $GENERATION_EXPRESSION;

    public function __construct($row){
        $this->TABLE_CATALOG = $row["TABLE_CATALOG"];
        $this->TABLE_SCHEMA = $row["TABLE_SCHEMA"];
        $this->TABLE_NAME = $row["TABLE_NAME"];
        $this->COLUMN_NAME = $row["COLUMN_NAME"];
        $this->ORDINAL_POSITION = $row["ORDINAL_POSITION"];
        $this->COLUMN_DEFAULT = $row["COLUMN_DEFAULT"];
        $this->IS_NULLABLE = $row["IS_NULLABLE"];
        $this->DATA_TYPE = $row["DATA_TYPE"];
        $this->CHARACTER_MAXIMUM_LENGTH = $row["CHARACTER_MAXIMUM_LENGTH"];
        $this->CHARACTER_OCTET_LENGTH = $row["CHARACTER_OCTET_LENGTH"];
        $this->NUMERIC_PRECISION = $row["NUMERIC_PRECISION"];
        $this->NUMERIC_SCALE = $row["NUMERIC_SCALE"];
        $this->CHARACTER_SET_NAME = $row["CHARACTER_SET_NAME"];
        $this->COLLATION_NAME = $row["COLLATION_NAME"];
        $this->COLUMN_TYPE = $row["COLUMN_TYPE"];
        $this->COLUMN_KEY = $row["COLUMN_KEY"];
        $this->EXTRA = $row["EXTRA"];
        $this->PRIVILEGES = $row["PRIVILEGES"];
        $this->COLUMN_COMMENT = $row["COLUMN_COMMENT"];
        $this->GENERATION_EXPRESSION = $row["GENERATION_EXPRESSION"];
    }

    public function __toString(){

        return $this->COLUMN_NAME;
    }

    public function getDataType(){
        return $this->DATA_TYPE;
    }

    public function renderCreate(){
        $string = "";
        $string .= "<div class='col-md-6 mb-3'>";
        $string .= "<div class='form-group'>";
        $string .= "<label for='".$this->COLUMN_NAME."'>".$this->COLUMN_NAME."</label>";
        $string .= $this->create_input_element();
        $string .= "</div>";
        $string .= "</div>";
        return $string;
    }

    public function renderUpdate($value=""){
        $string = "";
        $string .= "<div class='col-md-6 mb-3'>";
        $string .= "<div class='form-group'>";
        $string .= "<label for='".$this->COLUMN_NAME."'>".$this->COLUMN_NAME."</label>";
        $string .= $this->create_input_element($value);
        $string .= "</div>";
        $string .= "</div>";
        return $string;
    }

    private function create_input_element($value=null){
        $string = "";
        if($this->DATA_TYPE==="text" || $this->DATA_TYPE==="mediumtext" || $this->DATA_TYPE==="longtext" || $this->DATA_TYPE==="tinytext"){
            $string .= '<textarea style="width: 100%;border: 1pxsolid hsl(0, 0%, 70%);border-radius: 0.25em;margin: 0.25em0 1.5em;"';
        }else{
            $string .="<input ";
        }
        switch($this->DATA_TYPE){
            case "int":
            case "tinyint":
            case "smallint":
            case "mediumint":
            case "bigint":
            case "decimal":
            case "float":
            case "double":
            case "real":
                $string .="type='number' ";
                if(isset($this->NUMERIC_SCALE) && $this->NUMERIC_SCALE > 0){
                    $string .= "step='0.".str_repeat("0", $this->NUMERIC_SCALE)."' ";
                }
                if(isset($this->NUMERIC_PRECISION) && isset($this->NUMERIC_SCALE)){
                    $string .= "max='".( str_repeat("9",$this->NUMERIC_PRECISION-$this->NUMERIC_SCALE ));
                    if($this->NUMERIC_SCALE > 0){
                        $string .= ".".str_repeat("9",$this->NUMERIC_SCALE)."' ";
                    }else{
                        $string .= "' ";
                    }
                }
                break;
            case "varchar":
            case "char":
                $string .="type='text' ";
                if(isset($this->CHARACTER_MAXIMUM_LENGTH)){
                    $string .= "maxlength='".$this->CHARACTER_MAXIMUM_LENGTH."' ";
                }
                break;
            case "date":
                $string .="type='date' ";
                break;
            case "datetime":
            case "timestamp":
                $string .="type='datetime-local' ";
                break;
            case "text":
            case "longtext":
            case "tinytext":
            case "mediumtext":
                $string.="type='textarea' ";
                break;
            default:
                $string .="type='text' ";
                break;
        }
        if(!($this->DATA_TYPE==="text" || $this->DATA_TYPE==="mediumtext" || $this->DATA_TYPE==="longtext" || $this->DATA_TYPE==="tinytext")){
            if(isset($value)){
                $string .= "value='".$value."' ";
            }else{
                $string .= "value='".(isset($_POST[$this->COLUMN_NAME])?$_POST[$this->COLUMN_NAME]:"")."' ";
            }
        }
        $string .= "name='".$this->COLUMN_NAME."' ";
        // if($this->IS_NULLABLE == "NO"){
        //     $string .= "required='required' ";
        // }
        //value
        if($this->COLUMN_KEY == "PRI"){
            $string .= "disabled='disabled' ";
            $string .="placeholder='Primary Key (wordt automatisch gegenereerd)' ";
        }
        if(isset($this->COLUMN_DEFAULT)){
            $string .= "placeholder='".$this->COLUMN_DEFAULT."' ";
        }else{
            $string .= "placeholder='".$this->COLUMN_NAME."' ";
        }
        if($this->DATA_TYPE==="text" || $this->DATA_TYPE==="mediumtext" || $this->DATA_TYPE==="longtext" || $this->DATA_TYPE==="tinytext"){
            $string .= ">";
            if(isset($value)){
                $string .= $value;
            }
            $string .="</textarea>";
        }else{
            $string .="></input>";
        }
        return $string;
    }
}
