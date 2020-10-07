<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(dirname(__FILE__) . '/lib.php');
require_login();

$returnurl = new moodle_url('/mod/courselist/updatedb.php');

$informixdir = getenv("INFORMIXDIR");
$uname = "****";
$password= "*****";
$conn_string = "informix:host=203.250.129.135;service=1200;database=pcuhs;server=centertli;protocol=onsoctcp;
TRANSLATIONDLL=$informixdir/lib/esql/igo4a304.so;
CLIENT_LOCALE=en_US.utf8;DB_LOCALE=en_US.819";

echo $OUTPUT->header();

try {
#print "<li>informixdir = $informixdir</li>\n";
#db_flush();
$conn = new PDO($conn_string, $uname, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
#print "<li>Got a connection</li>\n";
 
//$sql = "SELECT * FROM v_paichai_gaeseol";
//$sql = "SELECT * FROM v_paichai_gaeseol WHERE tutorid LIKE 'A03%'";
$sql = "SELECT * FROM v_paichai_gaeseol WHERE tutorid = 'A00089'";
$stmt = $conn->query($sql);
if ( ! $stmt ) {
 print "Error in execute: stmt->execute()\n";
 print "errInfo[0]=>$err[0]\nerrInfo[1]=>$err[1]\nerrInfo[2]=>$err[2]\n";
 }
foreach($stmt as $row) {
   $courseinfo = new stdClass();
   $courseinfo->course=1;
   $courseinfo->name='test';
   $courseinfo->lectureid=$row['LECTUREID'];
   $courseinfo->seb_no=$row['SEB_NO'];
   $courseinfo->termyear=$row['TERMYEAR'];
   $courseinfo->termid=$row['TERMID'];
   $courseinfo->gwamok_name=trim(iconv("EUC-KR","UTF-8",$row['GWAMOK_NAME']));
   $courseinfo->intro=trim(iconv("EUC-KR","UTF-8",$row['INTRO']));
//   if(empty($row['TUTORID'])) {
   $courseinfo->tutorid=$row['TUTORID'];
//   } else {
//   $courseinfo->tutorid='UNKNOWN';
//   }
   $courseinfo->point=$row['POINT'];
   $courseinfo->jumsu_rate1=$row['JUMSU_RATE1'];
   $courseinfo->jumsu_rate2=$row['JUMSU_RATE2'];
   $courseinfo->jumsu_rate3=$row['JUMSU_RATE3'];
   $courseinfo->jumsu_rate4=$row['JUMSU_RATE4'];
   $courseinfo->jumsu_rate5=$row['JUMSU_RATE5'];
   $courseinfo->isclose=$row['ISCLOSE'];
   $courseinfo->planurl=$row['PLANURL'];
   $courseinfo->inonline=$row['INONLINE'];
   $courseinfo->lec_aim1=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM1']));
   $courseinfo->lec_aim2=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM2']));
   $courseinfo->lec_aim3=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM3']));
   $courseinfo->lec_aim4=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM4']));
   $courseinfo->lec_aim5=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM5']));
   $courseinfo->lec_aim6=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM6']));
   $courseinfo->lec_aim7=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM7']));
   $courseinfo->lec_aim8=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM9']));
   $courseinfo->lec_aim9=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM9']));
   $courseinfo->lec_aim10=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM10']));
   $courseinfo->lec_aim11=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM11']));
   $courseinfo->lec_aim12=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM12']));
   $courseinfo->lec_aim13=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM13']));
   $courseinfo->lec_aim14=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM14']));
   $courseinfo->lec_aim15=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM15']));
   $courseinfo->lec_aim16=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM16']));
   $courseinfo->lec_aim17=trim(iconv("EUC-KR","UTF-8",$row['LEC_AIM17']));
//   var_dump($courseinfo);
   $newcourselist=create_courselist($courseinfo);
//   var_dump($newcourselist);
}
$conn = null; 
echo 'ok';  
} catch (Exception $e) {
print "Exception messsage: {$e->getMessage()}\n";
exit(0);
}

echo $OUTPUT->footer();
die;
?>
