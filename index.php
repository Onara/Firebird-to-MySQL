<HTML>
    <HEAD>
    <TITLE>Extract Data from PK and load to local DB</TITLE>
    </HEAD>
    <BODY>
    <H3>Extracting data from original PK system and loading into local PK databse</H3>
    <?php
    // DB definition of account : PK original database
    define("DBNAME","localhost:C:/Users/jmuthukudage/Documents/Data/CMPDWIN.PKF"); // data bsse name
    define("DBUSER","SYSDBA"); // user name
    define("DBPASS","masterkey"); // password
    
    //MySQL connect: local DB
    
		$con=mysqli_connect("localhost","root","","pk");
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
				
    //End MySQL connect

    // DB connection to Original PK database
    $dbh = ibase_connect(DBNAME,DBUSER,DBPASS);
    if ($dbh == FALSE) {
    echo 'could not connect to Original PK<BR>';
    } else {
    echo 'success to connect to PK<BR>';
	$stmt = 'SELECT RDB$RELATION_NAME AS REL_NAME FROM RDB$RELATIONS WHERE RDB$SYSTEM_FLAG=0';
    $sth = ibase_query($dbh, $stmt);
   while ($row = ibase_fetch_object($sth)) {
   	
	$stmt2 = 'SELECT * FROM '.$row->REL_NAME;
    $sth2 = ibase_query($dbh, $stmt2);
	
	 $sql1= "DELET from $row->REL_NAME";
	 mysqli_query($con,$sql1);
	 
	 while ($row2 = ibase_fetch_row($sth2)) {
	 	   
	    	$sql2 = "INSERT INTO $row->REL_NAME values ('".implode($row2,"','")."')";
		   // echo $sql.nl2br('<br>'); 
		    mysqli_query($con,$sql2);
		    
		}
       
     }
     
	echo "Done...";
    ibase_free_result($sth);
    // DB dis connection
    mysqli_close($con);
    ibase_close($dbh);
    }
    ?>

    </BODY>
    </HTML>