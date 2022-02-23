<?php
/**
 * CRUD - Create, Read, Update, Delete Paneel
 * Single file.
 * ! If you want to excel download functionality install 'composer require PhpOffice/PhpSpreadsheet'
 * ! populate $downloadFolder and $autoload_file with the correct values
 * @requires: PHP ^5.4
 * @requires: Bootstrap ^4.6
 * @requires: MySQL
 * @author: Karel De Smet (snakehead007)
 * @version: 1.4
 * @since: 05/11/2021
 * @link: https://github.com/snakehead007/CRUD-php-panel
 * @lastUpdated: 23/02/2022
 */

class CRUD{
    public $tabellen;
    public $TABLE_SCHEMA;
    public $downloadFolder;
    public $autoload_file;
    public $link;
    public $href;
    public $chevron_double_right = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-double-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-chevron-double-right fa-w-16 fa-3x"><path fill="currentColor" d="M477.5 273L283.1 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.7-22.7c-9.4-9.4-9.4-24.5 0-33.9l154-154.7-154-154.7c-9.3-9.4-9.3-24.5 0-33.9l22.7-22.7c9.4-9.4 24.6-9.4 33.9 0L477.5 239c9.3 9.4 9.3 24.6 0 34zm-192-34L91.1 44.7c-9.4-9.4-24.6-9.4-33.9 0L34.5 67.4c-9.4 9.4-9.4 24.5 0 33.9l154 154.7-154 154.7c-9.3 9.4-9.3 24.5 0 33.9l22.7 22.7c9.4 9.4 24.6 9.4 33.9 0L285.5 273c9.3-9.4 9.3-24.6 0-34z" class=""></path></svg>';
    public $search = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-search fa-w-16 fa-3x"><path fill="currentColor" d="M508.5 481.6l-129-129c-2.3-2.3-5.3-3.5-8.5-3.5h-10.3C395 312 416 262.5 416 208 416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c54.5 0 104-21 141.1-55.2V371c0 3.2 1.3 6.2 3.5 8.5l129 129c4.7 4.7 12.3 4.7 17 0l9.9-9.9c4.7-4.7 4.7-12.3 0-17zM208 384c-97.3 0-176-78.7-176-176S110.7 32 208 32s176 78.7 176 176-78.7 176-176 176z" class=""></path></svg>';
    public $filter = '<svg style="width:25px;" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="filter" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-filter fa-w-16 fa-3x"><path fill="currentColor" d="M479.968 0H32.038C3.613 0-10.729 34.487 9.41 54.627L192 237.255V424a31.996 31.996 0 0 0 10.928 24.082l64 55.983c20.438 17.883 53.072 3.68 53.072-24.082V237.255L502.595 54.627C522.695 34.528 508.45 0 479.968 0zM288 224v256l-64-56V224L32 32h448L288 224z" class=""></path></svg>';
    public $excel = '<svg style="width:25px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm212-240h-28.8c-4.4 0-8.4 2.4-10.5 6.3-18 33.1-22.2 42.4-28.6 57.7-13.9-29.1-6.9-17.3-28.6-57.7-2.1-3.9-6.2-6.3-10.6-6.3H124c-9.3 0-15 10-10.4 18l46.3 78-46.3 78c-4.7 8 1.1 18 10.4 18h28.9c4.4 0 8.4-2.4 10.5-6.3 21.7-40 23-45 28.6-57.7 14.9 30.2 5.9 15.9 28.6 57.7 2.1 3.9 6.2 6.3 10.6 6.3H260c9.3 0 15-10 10.4-18L224 320c.7-1.1 30.3-50.5 46.3-78 4.7-8-1.1-18-10.3-18z"/></svg>';
    public $includeBeforeArray = array();
    public $includeAfterArray = array();
    public function __construct($tabellen,$link,$href,$TABLE_SCHEMA,$downloadFolder="",$autoload_file=""){
        if(!is_array($tabellen)) {
            throw new Exception("Lijst met tabellen moeten als Array worden opgegeven");
        }
        $this->link = $link;
        $this->href = $href;
        $tabellen_as_objects = array();
        foreach($tabellen as $tabel){
            if(!is_string($tabel)){
                throw new Exception("Tabel moet is niet als tekst meegeven.");
            }
            $tabel_as_object = new Tabel($tabel,$TABLE_SCHEMA,$link,$this->href);
            array_push($tabellen_as_objects,$tabel_as_object);
        }
        $this->tabellen = $tabellen_as_objects;
        $this->TABLE_SCHEMA = $TABLE_SCHEMA;
        $this->downloadFolder = $downloadFolder;
        $this->autoload_file = $autoload_file;
        if(!empty($downloadFolder) && !is_dir($downloadFolder)){
            throw new Exception("De gegeven download folder bestaat niet");
        }
        if(!empty($autoload_file) && !is_file($autoload_file)){
            throw new Exception("De gegeven autoload file bestaat niet");
        }
        if(!empty($downloadFolder) && empty($autoload_file)){
            throw new Exception("Er moet een autoload file worden opgegeven als download folder is opgegeven. installeer PhpOffice/PhpSpreadsheet via composer");
        }
    }

    public function __get($TABLE_NAME){
        foreach($this->tabellen as $tabel){
            if($tabel->TABLE_NAME == $TABLE_NAME){
                return $tabel;
            }
        }
        throw new Exception("tabel bestaat niet, gelieve een andere tabel te kiezen.");
    }

    public function downloadExcelFile($table){
        if($this->downloadFolder==""){
            throw new Exception("Download folder moet worden opgegeven bij de configuratie.");
        }else{
            //get data of table
            $data = $this->__get($table)->getData();
            // echo json_encode($data);
            if(empty($data)){
                throw new Exception("Deze tabel is leeg.");
            }

            $headers = array();
            foreach($data[0] as $key=>$value){
                array_push($headers,$key);
            }

            //init
            require_once $this->autoload_file;

            $row = 1;
            $collumn_index = 0; //max 'HH'
            $collums = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ","CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ","DA","DB","DC","DD","DE","DF","DG","DH","DI","DJ","DK","DL","DM","DN","DO","DP","DQ","DR","DS","DT","DU","DV","DW","DX","DY","DZ","EA","EB","EC","ED","EE","EF","EG","EH","EI","EJ","EK","EL","EM","EN","EO","EP","EQ","ER","ES","ET","EU","EV","EW","EX","EY","EZ","FA","FB","FC","FD","FE","FF","FG","FH","FI","FJ","FK","FL","FM","FN","FO","FP","FQ","FR","FS","FT","FU","FV","FW","FX","FY","FZ","GA","GB","GC","GD","GE","GF","GG","GH","GI","GJ","GK","GL","GM","GN","GO","GP","GQ","GR","GS","GT","GU","GV","GW","GX","GY","GZ","HA","HB","HC","HD","HE","HF","HG","HH");
            $lastLetter = $collums[count($headers)-1];

            $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => [
                        'rgb' => 'E1E1E1',
                    ]
                ],
            ];
            $styleData = [
                'alignment' => [
                    'horizontal' => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ]
            ];


            //headers

            //foreach header
            foreach($headers as $header){
                error_log("".$collums[$collumn_index].$row." with ".$header."");
                $sheet->setCellValue("".$collums[$collumn_index].$row,$header."");
                $spreadsheet->getActiveSheet()->getColumnDimension($collums[$collumn_index])->setAutoSize(true);
                $collumn_index++;
            }

            $spreadsheet->getActiveSheet()->getStyle("A".$row.":".$lastLetter.$row)->applyFromArray($styleArray);
            $sheet->calculateColumnWidths();

            //data
            foreach ($data as $key => $value_row) {
                $collumn_index = 0;
                $row++;
                foreach($value_row as $key_col => $col){
                    $sheet->setCellValue($collums[$collumn_index].$row,$col);
                    $collumn_index++;
                }
                $spreadsheet->getActiveSheet()->getStyle("A".$row.":".$lastLetter.$row)->applyFromArray($styleData);
            }
            // $spreadsheet->getActiveSheet()->getStyle('F1:'.$collums[$collumn_index].$row)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:'.$lastLetter.$row);
            $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = $table."-".time().".xlsx";
            $path = $this->downloadFolder."/".$filename;
            $writer->save($path);
            return $filename;
        }
    }

    public function renderNavbar($active_TABLE_NAME=null,$search=null,$update_or_delete=null,$create=null){
        $navbar = "";
        $navbar .='<nav class="navbar navbar-expand-lg navbar-light bg-light">';
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
            $navbar .='<a class="dropdown-item text-reset list-group-item list-group-item-action '.($tabel->TABLE_NAME == $active_TABLE_NAME?"active text-white":"text-muted").'" href="'.$this->href.'?table='.$tabel->TABLE_NAME.'">';
            $navbar .= $tabel->TABLE_NAME;
            $navbar .='</a>';
        }
        $navbar .='</div>';
        $navbar .='</li>';
        if(isset($active_TABLE_NAME)){
            if(isset($update_or_delete)){
                $navbar .='<li class="nav-item col" style="list-style-type:none;">';
                $navbar .='<p class="nav-link active">';
                $navbar .='<b>Verwijder of wijzig entry</b>';
                $navbar .='</p>';
                $navbar .='</li>';
                $navbar .='<li class="nav-item col" style="list-style-type:none;">';
                $navbar .='<button class="btn btn-info" onclick=\'window.history.back()\'>Terug</button>';
                $navbar .='</li>';

            } else if(isset($active_TABLE_NAME) && !isset($create)){
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
                if(isset($this->downloadFolder) && $this->downloadFolder!=""){
                    $navbar .='<li class="nav-item col" style="list-style-type:none; ">';
                    $navbar .= '<a href="'.$this->href.'?table='.$active_TABLE_NAME.'&excel=1'.
                        (isset($search) ? '&search='.$search : '').
                        (isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '').
                        (isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '').
                        (isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '').
                        (isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '').
                        '" class="nav-link">';
                    $navbar .='<button class="btn btn-outline-success my-2 my-sm-0" style="padding: 6px;" >';
                    $navbar .=$this->excel;
                    $navbar .='</button>';
                    $navbar .='</a>';
                    $navbar .='</li>';
                }
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
    private function echoScript(){
        // echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.26.0/axios.min.js" integrity="sha512-bPh3uwgU5qEMipS/VOmRqynnMXGGSRv+72H/N260MQeXZIK4PG48401Bsby9Nq5P5fz7hy5UGNmC/W1Z51h2GQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
        echo "<script>";
        echo "const tableNameCurrent = new URLSearchParams(window. location. search).get('table');";
        echo 'const tableNames ='.json_encode($this->tabellen).'.map(t=>t.TABLE_NAME).filter(t=>t!==tableNameCurrent);';
        echo 'const tables ='.json_encode($this->tabellen).';';
        echo 'function startContextMenuEvent(e){if("TD"==e.target.nodeName&&"TR"==e.target.parentNode.nodeName){e.target.id;let t=e.target.innerText,n=e.target.parentNode.dataset.editlink;document.getElementsByClassName("ContextMenu-item")[0].onclick=function(){location.href=n};const o=document.getElementsByClassName("ContextMenu_submenu")[0],a=tableNames.map(e=>`<li class="ContextMenu-item" onclick="location.href=\'https://ilt.kuleuven.be/stan/admin/crud/?table=${e}&update_or_delete=1&primary_key_name=id&primary_key_value=${t}&page=${new URLSearchParams(window.location.search).get("page")}&sort_column_name=${new URLSearchParams(window.location.search).get("sort_column_name")}&sort_direction=${new URLSearchParams(window.location.search).get("sort_direction")}&limit=${new URLSearchParams(window.location.search).get("limit")}\'">${e}</li>`);o.innerHTML=a.join(""),changeContextMenuXY(e.clientX,e.clientY+window.scrollY),showContextMenu()}}function dismissContextMenu(){console.log("dismissing context menu"),document.getElementsByClassName("ContextMenu")[0].classList.remove("is-open")}function showContextMenu(){document.getElementsByClassName("ContextMenu")[0].classList.add("is-open")}function changeContextMenuXY(e,t){let n=document.getElementsByClassName("ContextMenu")[0];n.style.left=e+"px",n.style.top=t+"px"}document.addEventListener("click",function(e){document.getElementsByClassName("ContextMenu")[0].classList.contains("is-open")&&dismissContextMenu()},!1),document.addEventListener?document.addEventListener("contextmenu",function(e){e.preventDefault(),startContextMenuEvent(e)},!1):document.attachEvent("oncontextmenu",function(e){e.preventDefault(),startContextMenuEvent(e),window.event.returnValue=!1});';
        echo 'function addCssLinkToHead(e){var t=document.createElement("link");t.type="text/css",t.rel="stylesheet",t.href=e,document.getElementsByTagName("head")[0].appendChild(t)}';
        echo "</script>";
        echo "<style>";
        echo ".ContextMenu{display:none;list-style:none;margin:0;max-width:250px;min-width:125px;padding:0;position:absolute;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;z-index:99999}.ContextMenu_submenu{display:none;list-style:none;margin:0;max-width:250px;min-width:125px;padding:0;position:absolute;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;z-index:99999}#ContextMenu_submenuOpener:hover>.ContextMenu_submenu{display:block}.ContextMenu--theme-default{background-color:#fff;border:1px solid rgba(0,0,0,.2);-webkit-box-shadow:0 2px 5px rgba(0,0,0,.15);box-shadow:0 2px 5px rgba(0,0,0,.15);font-size:16px;outline:0;padding:2px 0}.ContextMenu--theme-default .ContextMenu-item{padding:6px 12px}.ContextMenu--theme-default .ContextMenu-item:focus,.ContextMenu--theme-default .ContextMenu-item:hover{background-color:rgba(0,0,0,.05)}.ContextMenu--theme-default .ContextMenu-item:focus{outline:0}.ContextMenu--theme-default .ContextMenu-divider{background-color:rgba(0,0,0,.15)}.ContextMenu.is-open{display:block}.ContextMenu-item{cursor:pointer;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ContextMenu-divider{height:1px;margin:4px 0}";
        echo "</style>";
    }

    public function renderPaneel(){
        try{
            if($_SERVER['REQUEST_METHOD'] == "GET"){
                if(!is_string($this->href) || empty($this->href)){
                    throw new Exception("Parameter 'href' moet een string zijn en niet leeg zijn, dit zou de default locatie moeten zijn waar het paneel wordt getoont.");
                }
                $this->renderIncludeBefore();
                echo '<ul
                    class="ContextMenu ContextMenu--theme-default"
                    data-contextmenu="0"
                    tabindex="-1"
                    style="left: 144px; top: 129px"
                    >
                    <li class="ContextMenu-item">Aanpassen...</li>
                    <li class="ContextMenu-item" id="ContextMenu_submenuOpener">
                    Aanpassen als key in...
                    <img style="width: 19px;margin-top: 3px;"src="/php80/ici/_images/icons/arrow-right-grey.svg" alt=">">
                        <ul class="ContextMenu_submenu ContextMenu ContextMenu--theme-default" data-contextmenu="1" tabindex="-1" style="left: 204px;top: 40px;">
                        </ul>
                    </li>
                </ul>';
                $this->echoScript();
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
                if(isset($_REQUEST['excel']) && $_REQUEST['excel'] == 1){
                    $filename =$this->downloadExcelFile(
                        $_REQUEST["table"]
                    );
                    unset($_REQUEST["excel"]);
                    //todo, dit beter maken
                    echo "<a href='".$this->href
                        ."?table=".$_REQUEST["table"]
                        ."&page=".($_REQUEST["page"])
                        ."&limit=".($_REQUEST["limit"])
                        ."&sort_direction=".($_REQUEST["sort_direction"])
                        .(isset($_REQUEST["search"])?"&search=".$_REQUEST["search"]:"")
                        .(isset($_REQUEST["sort_column_name"])?"&sort_column_name=".$_REQUEST["sort_column_name"]:"")."'>".
                        "<button class='btn btn-primary btn-block'>Keer terug</button>".
                        "</a>";
                    echo "<script>window.location.href = 'https://ilt.kuleuven.be/php80/protected/upload/crud/".$filename."'</script>";
                    return;
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
                }
                $this->renderIncludeAfter();
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
    private function renderIncludeBefore(){
        if($_SERVER['REQUEST_METHOD'] == "GET"){
            foreach($this->includeBeforeArray as $includeFile){
                include_once($includeFile);
            }
        }
    }
    private function renderIncludeAfter(){
        if($_SERVER['REQUEST_METHOD'] == "GET"){
            foreach($this->includeAfterArray as $includeFile){
                include_once($includeFile);
            }
        }
    }
    public function setIncludeBefore($array){
        if(is_array($array)){
            $this->includeBeforeArray = $array;
        }else{
            $this->includeBeforeArray = array($array);
        }
    }
    public function setIncludeAfter($array){
        if(is_array($array)){
            $this->includeAfterArray = $array;
        }else{
            $this->includeAfterArray = array($array);
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
        return $data;
    }

    public function getDataByPrimaryKey($PRIMARY_KEY_NAME,$PRIMARY_KEY_VALUE){
        $qry = "SELECT * FROM `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` WHERE `".$PRIMARY_KEY_NAME."` = '".$PRIMARY_KEY_VALUE."';";
        $result = mysqli_query($this->link, $qry);
        $data = array();
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        return $data;
    }

    public function getPrimaryKeys(){
        $qry ="SHOW KEYS FROM `".$this->TABLE_SCHEMA."`.`".$this->TABLE_NAME."` WHERE Key_name = 'PRIMARY';";
        $result = mysqli_query($this->link, $qry);
        $data = array();
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
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
            if(isset($header->COLUMN_COMMENT) && trim($header->COLUMN_COMMENT) !=""){
                $string .= "<small class='text-muted'>&nbsp;&nbsp;&nbsp;".$header->COLUMN_COMMENT."</small>";
            }
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
            $string .= "<tr style='cursor:pointer;'";
            $string .= " data-editlink='".$this->href."?table=".$this->TABLE_NAME."&update_or_delete=1".(isset($search)?"&search=".$search:"");
            $string .='&primary_key_name='.$this->getFirstPrimaryKeyName().'&primary_key_value='.$row[$this->getFirstPrimaryKeyName()].'';
            $string .= isset($_REQUEST["page"]) ? '&page='.$_REQUEST["page"] : '';
            $string .=isset($_REQUEST["sort_column_name"]) ? '&sort_column_name='.$_REQUEST["sort_column_name"] : '';
            $string .=isset($_REQUEST["sort_direction"]) ? '&sort_direction='.$_REQUEST["sort_direction"] : '';
            $string .=isset($_REQUEST["limit"]) ? '&limit='.$_REQUEST["limit"] : '';
            $string .= "'";
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
            if(empty($dataOfCurrentEntry)){
                throw new Exception("Deze key heeft bestaat niet in deze tabel.");
            }
            if($header->COLUMN_NAME != $PRIMARY_KEY_NAME){
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
