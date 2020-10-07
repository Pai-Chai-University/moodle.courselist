<?php

$informixdir = getenv("INFORMIXDIR");
$uname = "";
$password= "";
$conn_string = "informix:host=203.250.129.135;service=1200;database=pcuhs;server=centertli;protocol=onsoctcp;
TRANSLATIONDLL=$informixdir/lib/esql/igo4a304.so;
CLIENT_LOCALE=en_US.utf8;DB_LOCALE=en_US.819";

$myhostname='localhost';
$myusername='haksauser';
$mypasswd='haksapass';
$mydbname='haksa';

try {
#print "<li>informixdir = $informixdir</li>\n";

$conn = new PDO($conn_string, $uname, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
print "<li>Got a informix connection</li>\n";

$dbh = new PDO("mysql:host=$myhostname;dbname=$mydbname", $myusername, $mypasswd); 
print "<li>Got a mysql connection</li>\n";

$sql = "SELECT * FROM v_paichai_user";

$stmt = $conn->query($sql);
if ( ! $stmt ) {
 print "Error in execute: stmt->execute()\n";
 print "errInfo[0]=>$err[0]\nerrInfo[1]=>$err[1]\nerrInfo[2]=>$err[2]\n";
 }
$total_count=0;

foreach($stmt as $row) {

   $userid=$row['USERID'];
   $usertype=$row['USERTYPE'];
   $pwd=$row['PWD'];
   $username=trim(iconv("EUC-KR","UTF-8",$row['USERNAME']));
   $surname=mb_substr($username,0,1,"UTF-8");
   $firstname=mb_substr($username,1,3,"UTF-8");
   $email=trim($row['EMAIL']);
   $hp=$row['HP'];
   $grade=$row['GRADE'];
   $clscode=$row['CLSCODE'];

   $sql2="INSERT INTO haksa(userid,usertype,pwd,surname,firstname,email,hp,grade,clscode) VALUES (?,?,?,?,?,?,?,?,?)";
   $q=$dbh->prepare($sql2);
   $count=$q->execute(array($userid,$usertype,$pwd,$surname,$firstname,$email,$hp,$grade,$clscode));
   $total_count++;
}
   echo $total_count;
$conn = null; 
$dbh = null;
echo 'ok';  
} catch (Exception $e) {
print "Exception messsage: {$e->getMessage()}\n";
exit(0);
}

?>
