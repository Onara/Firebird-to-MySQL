<HTML>
    <HEAD>
    <TITLE>PHP + Firebird / Interbase test (connection)</TITLE>
    </HEAD>
    <BODY>
    <H3>FB Connect test.</H3>
    <?php
    // DB definition of account
    define("DBNAME","localhost:C:/Users/jmuthukudage/Documents/Data/CMPDWIN.PKF"); // data bsse name
    define("DBUSER","SYSDBA"); // user name
    define("DBPASS","masterkey"); // password

    // DB connection
    $dbh = ibase_connect(DBNAME,DBUSER,DBPASS);
    if ($dbh == FALSE) {
    echo 'could not connect to DB<BR>';
    } else {
    echo 'success to connect to DB<BR>';
	$stmt = 'SELECT * FROM STATES';
    $sth = ibase_query($dbh, $stmt);
   while ($row = ibase_fetch_object($sth)) {
    echo $row->NAME;
     }
    ibase_free_result($sth);
    // DB dis connection
    ibase_close($dbh);
    }
    ?>

    </BODY>
    </HTML>