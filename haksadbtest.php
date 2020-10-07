<?php
/*** mysql hostname ***/
$hostname = 'localhost';

/*** mysql username ***/
$username = 'haksauser';

/*** mysql password ***/
$password = 'haksapass';

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=haksa", $username, $password);
    /*** echo a message saying we have connected ***/
    echo 'Connected to database<br />';

    /*** INSERT data ***/
   $userid='A99888';
   $usertype='s';
   $pwd='123456';
   $username='kim';
   $surname='jjj';
   $firstname='kkkk';
   $email='ksjsj';
   $hp='1234777';
   $grade='4';
   $clscode='hhhhh';

    $sql2="INSERT INTO haksa(userid,usertype,pwd,surname,firstname,email,hp,grade,clscode) VALUES (?,?,?,?,?,?,?,?,?)";
    $q=$dbh->prepare($sql2);
    $count=$q->execute(array($userid,$usertype,$pwd,$surname,$firstname,$email,$hp,$grade,$clscode));

    /*** echo the number of affected rows ***/
    echo $count;

    /*** close the database connection ***/
    $dbh = null;
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
    }
?>
