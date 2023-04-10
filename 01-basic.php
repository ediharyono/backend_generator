<?php
//option:
$db_host = 'localhost';
$db_password = '';
$user = 'root';
$name_database = 'uii_gateway';
$name_table = 'academic_search_student';
$name_table1 = ucwords($name_table); //huruf besar di web

//============================================================================================//
$column0 = 'id_academic_search_student';
$column1 = 'angkatan';
$column2 = 'nama_mahasiswa';
$column3 = 'nim';
$column4 = 'prodi';
$column5 = 'status';
$column6 = 'no_hp';
$column7 = 'alamat_mhs';
$column8 = 'dpa';
$column9 = 'nik_dpa';
$column10 = 'ipk';
$column11 = 'sks';

//============================================================================================//

$columnid = $column0;

//dont replace:
//special_character
$strs = '"';
$dot  = ".";
//

?>
<?php

$readme_md = '
1. npm init -y
2. npm i --save express dotenv cors sequelize mysql2 mysql
';

?>

<?php
$content_api = 
"
// Importing the packages required for the project.  
  
const mysql = require('mysql');  
const express = require('express');  
var app = express();  
const bodyparser = require('body-parser');  
const { body, validationResult } = require('express-validator');
//dotenv
const dotenv = require('dotenv');
dotenv.config();
// Used for sending the Json Data to Node API  
app.use(bodyparser.json());  
  
// Connection String to Database   
var mysqlConnection = mysql.createConnection({  
    host: process.env.HOST_".$name_table1.",  
    user : process.env.USER_".$name_table1.",  
    password : process.env.PASSWORD_".$name_table1.",   
    database : process.env.DATABASE_".$name_table1."  
});  
  
// To check whether the connection is succeed for Failed while running the project in console.  
mysqlConnection.connect((err) => {  
    if(!err) {  
        console.log(".$strs."Db Connection Succeed".$strs.");  
    }  
    else{  
        console.log(".$strs."Db connect Failed Error :".$strs." + JSON.stringify(err,undefined,2));  
    }  
});  
  
// To Run the server with Port Number  
app.listen(3000,()=> console.log(".$strs."Express server is running at port no : 3000".$strs."));  
//
app.get('/".$name_table."', function (req, res) {
    //query
    mysqlConnection.query('SELECT * FROM ".$name_table." ORDER BY ".$columnid." ASC', function (err, rows) {
        if (err) {
            return res.status(500).json({
                status: false,
                message: 'Internal Server Error',
            })
        } else {
            return res.status(200).json({
                status: true,
                message: 'List Data Posts',
                data: rows
            })
        }
    });
});
///Delete///
//Delete the ".$name_table." Data based on Id  
app.delete('/".$name_table."/:".$columnid."',(req,res)=>{  
    mysqlConnection.query('DELETE FROM ".$name_table." WHERE ".$columnid." = ?',[req.params.nim],(err,rows,fields)=>{  
    if(!err)   
    res.send(".$strs."Data Deletion Successful".$strs.");  
    else  
        console.log(err);  
      
})  
});  
///////////
";
?>


<?php
$content_env = 
"
HOST_".$name_table1." = '".$db_host."'
DATABASE_".$name_table1." = '".$name_database."'
PASSWORD_".$name_table1." = '".$db_password."'
USER_".$name_table1." = '".$user."'
";
?>

<?php

//membuat folder
if (!is_dir('cloud-api/basic')) {mkdir('cloud-api/basic', 0777, true);}

$fp = fopen("cloud-api/basic/readme.MD","wb");if( $fp == false ){ }else{ fwrite($fp,$readme_md); fclose($fp);}
//==========================================================================
//basic
if (!is_dir('cloud-api/basic')) {
    mkdir('cloud-api/basic', 0777, true);
}

//basic
 $fp = fopen("cloud-api/basic/api_".$name_table.".js","wb");
if( $fp == false ){
    //do debugging or logging here
}else{
    fwrite($fp,$content_api);
    fclose($fp);
}  
?>

<?php
//==========================================================================
//dotenv
 $fp = fopen("cloud-api/basic/.env","wb");
if( $fp == false ){
    //do debugging or logging here
}else{
    fwrite($fp,$content_env);
    fclose($fp);
}  
?>