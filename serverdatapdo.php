<?php
/*
* ServerDataPDO is a class file that wraps data tables SERVER-SIDE processing with PDO (PHP) SQL data abstraction
* and it provides a simple way to integrate Jquery  data tables with server side databases like SQLite, MySQL and other 
* PDO supported DB's. It also dynamically renders the Jquery (JAvascript) data tables code and corresponding HTML 
* (c) Tony Brandao <ab@abrandao.com>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

$guisettingsFile = "config/config.php";

if (file_exists($guisettingsFile)) { 
    require_once('config/config.php');
} else {
    error_log('plexWatchWeb :: Config file not found.');
    exit;
}

date_default_timezone_set(@date_default_timezone_get()); // Set default timezone

/* Change these to correspond to your database type (dsn) and access credentials, example below uses sqlite w/o pass */  
$db_dsn="sqlite:".$plexWatch['plexWatchDb'];  /* corresponds to PDO DSN strings refer to: http://www.php.net/manual/en/pdo.drivers.php */
$db_user=null;   
$db_pass=null; 

/* Sample MySQL Example
$db_dsn= 'mysql:host=localhost;dbname=testdb';
$db_user = 'username';
$db_pass = 'password';
 */

//When called directly via the Jquery Ajax Source look for this
//SECURITY NOTE: Consider moving this if..block to another file if security is a concern.
if ( isset($_GET['oDb']) )    //is this being called from datatables ajax?
{
    //Do we have an object database info (Serialized) if so expand it\\
    //echo $_GET['oDb'];
    $d=unserialize(base64_decode($_GET['oDb']));  //NOTE HARDEN  by encrypting
    $pdo = new ServerDataPDO($db_dsn,$user,$pass,$d['sql'],$d['table'],$d['idxcol']);  //construct the object
    $result=$pdo->query_datatables(); //now return the JSON Requested data */
    echo $result;
    exit;
}

class ServerDataPDO
{
    /* UPDATE these variables with valid PDO DSN and credentials to connect to database */
    /* DSN  information http://www.php.net/manual/en/ref.pdo-mysql.connection.php */
    public $db=array( 
        "dsn"=> null, 
        "user"=>null, 
        "pass"=>null,
        "conn"=>null,
        "sql"=>null,
        "table"=>null, /* DB table to use assigned by constructor*/
        "idxcol"=>null /* Indexed column (used for fast and accurate table cardinality) */
    );

    public static $default_ajax_url=__FILE__; //Defaults to current file name
	
    /* Array of database columns which should be read and sent back to DataTables.dynamically created  */
    public $aColumns = null; // holds SELECT [columns] from SQL query
    public $time_start=null; /* Start timer for metric performance collection */
	
    /********************************************************************
    constructor function : called when object is first instantiated
    */
    public function __construct($dsn=null,$user=null,$pass=null,$sql=null, $table=null, $index_col=null) 
    { 
        $this->db['dsn']= empty($dsn)? $this->db['dsn'] : $dsn;
        $this->db['sql']= empty($sql)? $this->db['sql'] : $sql;
        $this->db['user']= empty($user)? $this->db['user'] : $user;
        $this->db['pass']= empty($pass)? $this->db['pass'] : $pass;

	/* Create a database connection if $db['conn'] is null*/
	if (empty( $this->db['conn']) )  /* no valid connection? let's make one */
            $this->pdo_conn($this->db['dsn'],$this->db['user'],$this->db['pass']);
		
	/* Start timer for metrics */
        $this->time_start = microtime(true);
	 
        /* build the SQL table and columns from the String */
        if (!empty($sql) )
            $this->get_SQL_acolumns($sql);
	  
        /* assign table and index if provided */
	$this->db['idxcol'] = $index_col;
	$this->db['table'] = $table;
    }
	
    /********************************************************************
    pdo_conn : Creates a connection to a database vai the PDO (PHP) database abstraction layer
    Refer to http://ca1.php.net/manual/en/pdo.drivers.php  for possible PDO drivers and DSN strings
    Called by the constructor
    @dsn  matches PDO DSN string name for database connection 
    @return  null , sets global $db['conn'] variable
    */
    public function pdo_conn($dsn=null,$username=null,$password=null)
    {
        try {
            //echo "[dsn]: $dsn >> Connection: ".$this->db[ 'conn'];
            $this->db['conn'] = new PDO($dsn);    //typical dsn like  'mysql:host=localhost;dbname=testdb';

            // Set errormode to exceptions
            $this->db['conn']->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return true;

        } catch (PDOException $e) {
           $this->fatal_error( "Database Error!: <strong>".$e->getMessage()."</strong> Dsn= <strong>$dsn </strong><br/>" );
        }
    } //end of connection function
	
    /********************************************************************
    get_SQL_acolumns: Uses a basic SQL Statements SELECT field1,field3,field3 FROM Table 1 and extracts SELECT fields
    NOTES: Column names MUST be explicitly noted , SELECT * FROM is not supported
    complex SQL statements not currently supported
    Results of fields names are converted into array in $this->aColumns
    @SQL SQL Statements (basic) to have fields extracted
    @returns false if unable to extract columns otherwise true if  $this->aColumns is successfull
    */
    public function get_SQL_acolumns($sql=null,$s1="SELECT",$s2="FROM",$split_on=",")
    { 
        $pattern = "/$s1(.*?)$s2/i";
	if (preg_match($pattern, $sql, $matches)) 
        {
            //print_r($matches);	
            $this->aColumns=explode($split_on, $matches[1]);  //return into
            $this->aColumns=array_map('trim',$this->aColumns ); //trim white space	  
        }
	else
        {
            $this->fatal_error("NO SQL columns found in $sql string resulting in <strong>".$result."</strong> Be sure to have $split_on delimited values.");
            return false; //string not found
	}
    }
	
    /********************************************************************
    build_jquery_datables:  Static function no object needed to instantiate
    Builds the Javascript JQuery code to call for the database call use this function
    */
    public static function  build_jquery_datatable($aDBInfo=null,$table_id="datatable1",$ajax_source_url=null,$datatable_properties=null)
    {
        $js=null;  //Holds the javascript string
        $dba=array("a","b");

        $ajax_source_url = is_null($ajax_source_url)? basename(__FILE__) : $ajax_source_url;

        if (isset($aDBInfo))
            $serializd_db=base64_encode(serialize($aDBInfo));

/* Edit Jqeury Here */
$js=  <<<EOT
<!-- Start generated Jquery from $ajax_source_url  --->
<script type="text/JavaScript" charset="utf-8">
    $(document).ready(function() {
        var $table_id=$('#$table_id').dataTable( {
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                $.getJSON( sSource, aoData, function (json) {
                    fnCallback(json)
                })
            },
            "iDisplayLength": 25,
            "bLengthChange": true,
            "bProcessing": true,
            "bServerSide": true,
            "sPaginationType": 'bootstrap',
            "sAjaxSource": "$ajax_source_url?oDb='$serializd_db'",
            "bStateSave": false,$datatable_properties 
            "bDeferRender": true,
            "aaSorting": [[0,'desc']],
            "aoColumnDefs": [
                { 
                    "sName": 'id',
                    "aTargets": [ 0 ],
                    "bVisible": false,
                    "bSearchable": false
                },
                { 
                    "sName": 'date',
                    "aTargets": [ 1 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if(oData[8] !== null) {
                            $(nTd).html(moment(sData,"X").format("L"));
                        } else {
                            $(nTd).addClass('currentlyWatching');
                            $(nTd).html('Currently watching...');
                        }
                    },
                    "bSearchable": false
                },
                { 
                    "sName": 'title',
                    "aTargets": [ 5 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if(sData !== '') {
                            var xmlDoc=loadXMLString(oData[9]);
                            var mediaId = xmlDoc.getElementsByTagName("opt")[0].getAttribute("ratingKey");
                            $(nTd).html('<a href="info.php?id='+mediaId+'">'+sData+'</a>');
                        }    
                    }
                },
                { 
                    "sName": 'user',
                    "aTargets": [ 2 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if(sData !== '') {
                            $(nTd).html('<a href="user.php?user='+sData+'">'+sData+'</a>');
                        }    
                    }
                },
                { 
                    "sName": 'platform',
                    "aTargets": [ 3 ]
                },
                { 
                    "sName": 'time',
                    "aTargets": [ 6 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(moment(sData,"X").format("h:mm a"));
                    },
                    "bSearchable": false
                },
                { 
                    "sName": 'stopped',
                    "aTargets": [ 8 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if(sData !== '') {
                            $(nTd).html(moment(sData,"X").format("h:mm a"));
                        } else {
                            $(nTd).html('n/a');
                        }
                    },
                    "bSearchable": false
                },
                { 
                    "sName": 'ip_address',
                    "aTargets": [ 4 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if((sData === '0') || (sData === '')) {
                            $(nTd).html('n/a');
                        } else {
                            $(nTd).html(sData);
                        }    
                    }
                },
                { 
                    "sName": 'paused_counter',
                    "aTargets": [ 7 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if(sData === '') {
                            $(nTd).html('0 min');
                        } else {
                            $(nTd).html(Math.round((sData/60),1)+' min');
                        }    
                    },
                    "bSearchable": false
                },
                { 
                    "sName": 'xml',
                    "aTargets": [ 9 ],
                    "bVisible": false,
                    "bSearchable": false
                },
                {
                    "aTargets": [ 10 ],
                    "sDefaultContent": '',
                    "sType": "html",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if(oData[8] !== null) {
                            var unixStarted = new Date(oData[6]*1000);
                            var unixEnded = new Date(oData[8]*1000);
                            var pausedDuration = new Date(oData[7]*1000);
                            var duration = Math.abs(unixEnded - (unixStarted - pausedDuration));
                            var nd = Math.round(moment.duration(duration).asMinutes(),0);
                            $(nTd).html(nd+' min');
                        } else {
                            $(nTd).html('n/a');
                        }
                    },
                    "bSearchable": false,
                    "bSortable": false
                },
                {
                    "aTargets": [ 11 ],
                    "sDefaultContent": '',
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            var xmlDoc=loadXMLString(oData[9]);
                            var mediaDuration = xmlDoc.getElementsByTagName("opt")[0].getAttribute("duration");
                            var mediaOffset = xmlDoc.getElementsByTagName("opt")[0].getAttribute("viewOffset");
                            if((mediaDuration !== 0) && (mediaOffset !== null)) {
                                var percentComplete = Math.round(((mediaOffset / mediaDuration) * 100),0);
                            } else {
                                var percentComplete = 0;
                            }
                            $(nTd).html('<span class="badge badge-warning">'+percentComplete+'%</span>');
                    },
                    "bSearchable": false,
                    "bSortable": false
                },
                {
                    "aTargets": [ 12 ],
                    "sDefaultContent": '',
                    "sClass": 'modal-control',
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html('<a href="#info-modal" data-toggle="modal"><span class="badge badge-inverse"><i class="icon-info icon-white"></i></span></a>');
                    },
                    "bSearchable": false,
                    "bSortable": false
                }
            ]
        } );
        
        $('#$table_id').on('click', 'td.modal-control', function () {
        
            var tr = $(this).parents('tr');
            var id = tr.attr('id');
            var rowData = $table_id.fnGetData(id);

            $.post("datafactory/get-info-modal.php", { id: rowData[0] },
                function(data) {
                    if(data)
                    {
                        $("#modal-text").html(data);
                        $("#modal-stream-info").html(rowData[5]+' ('+rowData[2]+')');
                    }
                    else
                    {
                        $("#modal-text").html("Ietsie is verkeerd!");
                    }
                }
            );
        } );
    } );
</script>
<!--  End generated Jquery from  $ajax_source_url ---> 
EOT;

        return $js;  //returns the completed jquery string
    }	
    
    /********************************************************************
    build_html_datatable:   Static function no object needed to instantiate
    Based on the $this->aColumns array it dynamically builds the HTML code for HTML data tables
    @table_id  table_id for Jquery to refer to allow multiple tables
    @columns a comma separated string of column names defaults SQL columns if null
    @returns string containing the completed HTML data tables
    */
    public static function  build_html_datatable($columns=null,$table_id="datatable1")
    {
        $html=null;
        $html_columns=null;

        //lets extract the columns names from the string
        if ( !empty($columns) ) 
            $columns=explode(",",$columns);
        else	
            die(" $columns columns array must be  defined, such as col1,col2,col3");
        
        //build the header loop through the array and of columns 
        $count_cols=count($columns);
        
        $i = 0;
        foreach($columns as $key=>$val) {
            if ($i < 9) {
                $html_columns.="<th align='left'><i class='icon-sort icon-white'></i>".trim($val)."</th>\n";
            } else {
                $html_columns.="<th align='left'>".trim($val)."</th>\n";
            }
            $i++;
        }
  
$html = <<< EOT
<!-- Start of Generated HTML Datatables structure -->
<table class='display' width='100%' id='$table_id'>
    <thead>
        <tr>
            $html_columns
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan='$count_cols' class='dataTables_empty'>Loading data from server</td>
        </tr>
    </tbody>
</table>

EOT;

        return $html;
    }		
	
    /********************************************************************
    fatal_error : Creates a Server Error to be passed ot calling AJAX page
    @sErrorMessage Error message to be returned to browser
    */
    static function fatal_error( $sErrorMessage = '' )
    {
        header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error ' );
        die( $sErrorMessage );
    }

    /********************************************************************
    query_array : Create an array from a SQL Query string 
    @sql  SQL to be executed and returned
    @returns $results an array  a PHP array (2D) of results of SQL
    */
    function query_array($sql=null)
    {
        global $db,$debug;
 
        try {   
            if ($debug) 
                $time_start = microtime(true);
            
            $stmt = $this->db['conn']->prepare($sql);
            $stmt->execute();
		
            if ($debug){
                $time =  microtime(true)- $time_start;	
                echo "<HR>Executed SQL:<strong> $sql </strong> in <strong>$time</strong> s<HR>";
            }
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM | PDO::FETCH_ASSOC
            return  $results ;
        } catch (PDOException $e) {
            $this->fatal_error(" Database Error!: <strong>". $e->getMessage() ."</strong> SQL: $sql <br /> Using DSN ".$this->db['dsn']."<br/>");
            die();   die();
        }
    }	

    /********************************************************************
    query_datables : Primary server-side data table processing, builds query and returns encoded json
    reads input from the datables query string parameters and builds SQL
    @returns json_encode JSON encoded results compatible with data tables
    */

    function query_datatables()	
    {
	/** Paging	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
                intval( $_GET['iDisplayLength'] );
	}
	
	/** Ordering */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= "`".$this->aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
                        ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
	}
	
	/** Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($this->aColumns) ; $i++ )
            {
                if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
                {
                    $sWhere .= "`".$this->aColumns[$i]."` LIKE '%". $_GET['sSearch'] ."%' OR ";
                }
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($this->aColumns) ; $i++ )
	{
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = "WHERE ";
                }
                else
                {
                    $sWhere .= " AND ";
                }
                $sWhere .= "`".$this->aColumns[$i]."` LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
	}
	
	/** CORE SQL queries * Get data to display	 */
	$sQuery = "	SELECT  `".str_replace(" , ", " ", implode("`, `", $this->aColumns))."`
            FROM  ".$this->db['table']."
            $sWhere
            $sOrder
            $sLimit
            ";

        try {	
            $aResult  = $this->query_array($sQuery);
	} catch (PDOException $e) {
            print "SQL DAtabase Error!: " . $e->getMessage() . "<br/>";
            die();
        }
	
	/* Data set length after filtering */
	//TODO : improve efficiency currently does 2X query  to count total records in query
	$sQuery = "	SELECT COUNT(`".$this->db['idxcol']."`) as totalqry FROM  ".$this->db['table']." $sWhere ";
	$aResultFilterTotal =$this->query_array($sQuery); /* Fetch all of the remaining rows in the result set */
	$iFilteredTotal= $aResultFilterTotal[0]['totalqry'];
	
	/* Total data set length */
	$sQuery = "	SELECT COUNT(`".$this->db['idxcol']."`) as cnt FROM  ".$this->db['table'];
	$aResultTotal = $this->query_array($sQuery);
        //print_r($aResultTotal);
	$iTotal = $aResultTotal[0]['cnt'];

	/*
	 * Output
	 */
	$output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTime" => (microtime(true)- $this->time_start ),
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
	);
	
	/* Take the Query Result and resturn  JSON encoded String */
        $j = 0;
	foreach ($aResult as $key => $aRow)
	{
            $row = array();
            for ( $i=0 ; $i<count($this->aColumns) ; $i++ ) {
                if ($j == 0) { 
                    $row["DT_RowId"] = '0';
                } else {
                    $row["DT_RowId"] = $j;
                }
                $row[] = $aRow[ $this->aColumns[$i] ];
            }
            $output['aaData'][] = $row;
            $j++;
	}
	
	return json_encode( $output );
	
    } //end of function

} //end of class	

?>