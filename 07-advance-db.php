<?php
//npm init -y     
//npm install --save express mysql2 multer body-parser dotenv nodemon
//option:
$port = '4000';
$db_host = 'localhost';
$db_password = '';
$user = 'root';
$name_database = 'classroo_uii_gateway'; //".$name_table." 
$name_table = 'academic_khs_cumulative';
$name_table1 = ucwords($name_table); //huruf besar di web

//
$link =  new mysqli($db_host, $user, $db_password, $name_database ) or die ("Error connecting to mysql $mysqli->connect_error");
$sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$name_database."' AND TABLE_NAME = '".$name_table."'";

if (!($result_database=mysqli_query($link,$sql))) {
        printf("Error: %s\n", mysqli_error($link));
    }
	$i=0;
    	$content1='';
		while( $row_coloumn = mysqli_fetch_row( $result_database ) ){
			//$content.= "$"."column$i  = '".$row_coloumn[0]."';</br>";
            $content1.= "$"."column$i  = '".$row_coloumn[0]."';";  
			$i++;
		}
          
		echo $content1;
        
//============================================================================================//
$column0 = 'id';$column1 = 'nim';$column2 = 'kd_matakuliah';$column3 = 'matakuliah';$column4 = 'sks';$column5 = 'flag_survey';$column6 = 'persentase_survey';$column7 = 'nilai';$column8 = 'bobot'; {"id":"-","nim":"-","kd_matakuliah":"-","matakuliah":"-","sks":"-","flag_survey":"-","persentase_survey":"-","nilai":"-","bobot":"-","result":"true"}
//============================================================================================//

$columnid = $column0;

//dont replace:
//special_character
$strs = '"';
$dot  = ".";
//

?>


<?php
$content_logs_js = "
const logRequest = (req, res, next) => {
    console.log('Terjadi request ke PATH: ', req.path);
    next();
}
module.exports = logRequest;
";
?>
<?php
$content_multer_js = "
const multer = require('multer');
const path = require('path');

const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'public/images');
    },
    filename: (req, file, cb) => {
        const timestamp = new Date().getTime();
        const originalname = file.originalname;
        // const extension = path.extname(file.originalname);

        cb(null, '$"."{timestamp}-$"."{originalname}');
    }
});

const upload = multer({
    storage: storage,
    limits: {
        fileSize: 3 * 1000 * 1000 // 3 MB
    }
});

module.exports = upload;
";
?>

<?php
$content_database_js = 
"
const mysql = require('mysql2');
const dbPool = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,


});

module.exports = dbPool.promise();
"
?>

<?php
$content_controller_advance = "
const ".$name_table."Model = require('../models/".$name_table.".model');

exports.allAccess = async (req, res) => {
    try {
        const [data] = await ".$name_table."Model.getAll".$name_table."();
    
        res.json({
            message: 'GET all ".$name_table." success',
            records: data?.length,
            data: data
        })
    } catch (error) {
        res.status(500).json({
            message: 'Server Error',
            serverMessage: error,
        })
    }
}
  
  exports.userBoard = (req, res) => {
    res.status(200).send('User Content.');
  };
  
  exports.adminBoard = (req, res) => {
    res.status(200).send('Admin Content.');
  };
  
  exports.moderatorBoard = (req, res) => {
    res.status(200).send('Moderator Content.');
  };
  
  
";
?>
<?php
$content_controller_js = 
"
const ".$name_table."Model = require('../models/".$name_table."');

const getAll".$name_table." = async (req, res) => {
    try {
        const [data] = await ".$name_table."Model.getAll".$name_table."();
    
        res.json({
            message: 'GET all ".$name_table." success',
            records: data?.length,
            data: data
        })
    } catch (error) {
        res.status(500).json({
            message: 'Server Error',
            serverMessage: error,
        })
    }
}

const createNew".$name_table." = async (req, res) => {
    const {body} = req;

    if(
        !body.".$column2." || 
        !body.".$column3."
        ){
        return res.status(400).json({
            message: 'Anda mengirimkan data yang salah',
            data: null,
        })
    }

    try {
        await ".$name_table."Model.createNew".$name_table."(body);
        res.status(201).json({
            message: 'CREATE new ".$name_table." success',
            data: body
        })
    } catch (error) {
        res.status(500).json({
            message: 'Server Error',
            serverMessage: error,
        })
    }
}

const update".$name_table." = async (req, res) => {
    const {id".$name_table."} = req.params;
    const {body} = req;
    try {
        await ".$name_table."Model.update".$name_table."(body, id".$name_table.");
        res.json({
            message: 'UPDATE ".$name_table." success',
            data: {
                id: id".$name_table.",
                ...body
            },
        })
    } catch (error) {
        res.status(500).json({
            message: 'Server Error',
            serverMessage: error,
        })
    }
}

const delete".$name_table." = async (req, res) => {
    const {id".$name_table."} = req.params;
    try {
        await ".$name_table."Model.delete".$name_table."(id".$name_table.");
        res.json({
            message: 'DELETE ".$name_table." success',
            data: null
        })
    } catch (error) {
        res.status(500).json({
            message: 'Server Error',
            serverMessage: error,
        })
    }
}

module.exports = {
    getAll".$name_table.",
    createNew".$name_table.",
    update".$name_table.",
    delete".$name_table.",
}
"
?>

<?php
$content_models_js = 
"
const dbPool = require('../config/database');
const uuid = require('uuid');

const getAll".$name_table." = () => {
    const SQLQuery = 'SELECT * FROM ".$name_table."';

    return dbPool.execute(SQLQuery);
}

const createNew".$name_table." = (body) => {
    const id = uuid.v4();

    const SQLQuery = `  INSERT INTO ".$name_table." (".$columnid.", ".$column2.", ".$column3.") 
                        VALUES ( '$"."{body.".$columnid."}' , '$"."{body.".$column2."}', '$"."{body.".$column3."}')`;

    //const SQLQuery = `  INSERT INTO ".$name_table." (".$columnid.", ".$column2.", ".$column3.") 
      //                  VALUES ( id , '$"."{body.".$column2."}', '$"."{body.".$column3."}')`;                        

    return dbPool.execute(SQLQuery);
}

const update".$name_table." = (body, id".$name_table.") => {
    const SQLQuery = `  UPDATE ".$name_table." 
                        SET ".$column1."='$"."{body.".$column1."}', ".$column2."='$"."{body.".$column2."}', ".$column3."='$"."{body.".$column3."}' 
                        WHERE ".$columnid."=$"."{id".$name_table."}`;

    return dbPool.execute(SQLQuery);
}

const delete".$name_table." = (id".$name_table.") => {
    const SQLQuery = `DELETE FROM ".$name_table." WHERE ".$columnid."=$"."{id".$name_table."}`;

    return dbPool.execute(SQLQuery);
}

module.exports = {
    getAll".$name_table.",
    createNew".$name_table.",
    update".$name_table.",
    delete".$name_table.",
}
"
?>

<?php
$content_routes_advance = 
'

const { authJwt } = require("../middleware");
const controller = require("../controllers/'.$name_table.'.controller");

module.exports = function(app) {
  app.use(function(req, res, next) {
    res.header(
      "Access-Control-Allow-Headers",
      "x-access-token, Origin, Content-Type, Accept"
    );
    next();
  });

  app.get("/v1/'.$name_table.'/all", controller.allAccess);

  app.get(
    "/v1/'.$name_table.'/user",
    [authJwt.verifyToken],
    controller.userBoard
  );

  app.get(
    "/v1/'.$name_table.'/mod",
    [authJwt.verifyToken, authJwt.isModerator],
    controller.moderatorBoard
  );

  app.get(
    "/v1/'.$name_table.'/admin",
    [authJwt.verifyToken, authJwt.isAdmin],
    controller.adminBoard
  );
};


';
?>
<?php
$content_routes_js = 
"
const express = require('express');
const ".$name_table."Controller = require('../controllers/".$name_table.".js');
const router = express.Router();

// CREATE - POST
router.post('/', ".$name_table."Controller.createNew".$name_table.");

// READ - GET
router.get('/', ".$name_table."Controller.getAll".$name_table.");

// UPDATE - PATCH
router.patch('/:id".$name_table."', ".$name_table."Controller.update".$name_table.");

// DELETE - DELETE
router.delete('/:id".$name_table."', ".$name_table."Controller.delete".$name_table.");



module.exports = router;
"
?>

<?php
//SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'my_database' AND TABLE_NAME = 'my_table';
$link =  new mysqli($db_host, $user, $db_password, $name_database ) or die ("Error connecting to mysql $mysqli->connect_error");
$sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$name_database."' AND TABLE_NAME = '".$name_table."'";

if (!($result_database=mysqli_query($link,$sql))) {
        printf("Error: %s\n", mysqli_error($link));
    }
	$i=0;
	$content='{';
		while( $row_coloumn = mysqli_fetch_row( $result_database ) ){
			//$content.= "$"."column$i  = '".$row_coloumn[0]."';</br>";
            $content.= '"'.$row_coloumn[0].'":"-",';
			$i++;
		}
        $content.='"result":"true"}';    
		echo $content;
        ?>


<?php
//membuat folder
if (!is_dir('cloud-api/advance/develop/app/')) {mkdir('cloud-api/advance/develop/app/', 0777, true);}
if (!is_dir('cloud-api/advance/develop/app/config')) {mkdir('cloud-api/advance/develop/app/config', 0777, true);}
if (!is_dir('cloud-api/advance/develop/app/controllers')) {mkdir('cloud-api/advance/develop/app/controllers', 0777, true);}
if (!is_dir('cloud-api/advance/develop/app/middleware')) {mkdir('cloud-api/advance/develop/app/middleware', 0777, true);}
if (!is_dir('cloud-api/advance/develop/app/models')) {mkdir('cloud-api/advance/develop/app/models', 0777, true);}
if (!is_dir('cloud-api/advance/develop/app/routes')) {mkdir('cloud-api/advance/develop/app/routes', 0777, true);}
 
?>

<?php
//$fp = fopen("cloud-api/advance/develop/app/index.js","wb");if( $fp == false ){ }else{ fwrite($fp,$content_index); fclose($fp);}  
//$fp = fopen("cloud-api/advance/develop/app/.env","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_env); fclose($fp);}  
$fp = fopen("cloud-api/advance/develop/app/config/database.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_database_js); fclose($fp);}
$fp = fopen("cloud-api/advance/develop/app/controllers/".$name_table .".controller.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_controller_advance); fclose($fp);}
$fp = fopen("cloud-api/advance/develop/app/models/".$name_table .".model.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_models_js); fclose($fp);} 
$fp = fopen("cloud-api/advance/develop/app/routes/".$name_table .".routes.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_routes_advance); fclose($fp);} 

$fp = fopen("cloud-api/advance/develop/app/middleware/logs.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_logs_js); fclose($fp);} 
$fp = fopen("cloud-api/advance/develop/app/middleware/multer.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_multer_js); fclose($fp);} 
 
 
?>
 
