<?php
//npm init -y     
//npm install --save express mysql2 multer body-parser dotenv nodemon
//option:
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
$column0  = 'id';$column1  = 'nim';$column2  = 'kd_matakuliah';$column3  = 'matakuliah';$column4  = 'sks';$column5  = 'flag_survey';$column6  = 'persentase_survey';$column7  = 'nilai';$column8  = 'bobot';
//============================================================================================//

$columnid = $column0;

//dont replace:
//special_character
$strs = '"';
$dot  = ".";
//

?>

<?php
$content_index = 
"
const dotenv = require('dotenv');
dotenv.config();

const PORT = process.env.PORT || 5000;
const express = require('express');
//koleksi routersnya//
const ".$name_table."Routes = require('./routes/".$name_table."');

const middlewareLogRequest = require('./middleware/logs');
const upload = require('./middleware/multer');

const app = express();

app.use(middlewareLogRequest);
app.use(express.json());
app.use('/assets', express.static('public/images'))

//ditambahkan saja / koleksi tablenya//
app.use('/".$name_table."', ".$name_table."Routes);
//////////////////////////////

app.post('/upload',upload.single('photo'),(req, res) => {
    res.json({
        message: 'Upload berhasil'
    })
})

app.use((err, req, res, next) => {
    res.json({
        message: err.message
    })
})

app.listen(PORT, () => {
    console.log(`Server berhasil di running di port $"."{PORT}`);
})
";
?>   


<?php
$port = "4000";
$content_env = 
"
PORT=".$port."

DB_HOST = '".$db_host."'
DB_USERNAME = '".$user."'
DB_PASSWORD = '".$db_password."'
DB_NAME = '".$name_database."'
";
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
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,


});

module.exports = dbPool.promise();
"
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
    const SQLQuery = `DELETE FROM ".$name_table." WHERE ".$columnid."='$"."{id".$name_table."}'`;

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
$readme_md_content = 
'
1. npm init -y
2. npm install express multer body-parser uuid sequelize nodemon env2 cors mysql2 dotenv --save
3. cd src, node index.js
4. browse http://localhost:'.$port.'
5. Example Body JSON Request 

'.
$content1. '
For body request : 
'.
$content;

?>
<?php
//membuat folder
if (!is_dir('cloud-api/middle/src')) {mkdir('cloud-api/middle/src', 0777, true);}
if (!is_dir('cloud-api/middle/src/config')) {mkdir('cloud-api/middle/src/config', 0777, true);}
if (!is_dir('cloud-api/middle/src/controllers')) {mkdir('cloud-api/middle/src/controllers', 0777, true);}
if (!is_dir('cloud-api/middle/src/middleware')) {mkdir('cloud-api/middle/src/middleware', 0777, true);}
if (!is_dir('cloud-api/middle/src/models')) {mkdir('cloud-api/middle/src/models', 0777, true);}
if (!is_dir('cloud-api/middle/src/routes')) {mkdir('cloud-api/middle/src/routes', 0777, true);}
 
?>

<?php
$fp = fopen("cloud-api/middle/src/index.js","wb");if( $fp == false ){ }else{ fwrite($fp,$content_index); fclose($fp);}  
$fp = fopen("cloud-api/middle/src/.env","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_env); fclose($fp);}  
$fp = fopen("cloud-api/middle/src/config/database.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_database_js); fclose($fp);}
$fp = fopen("cloud-api/middle/src/controllers/".$name_table .".js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_controller_js); fclose($fp);}
$fp = fopen("cloud-api/middle/src/models/".$name_table .".js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_models_js); fclose($fp);} 
$fp = fopen("cloud-api/middle/src/routes/".$name_table .".js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_routes_js); fclose($fp);} 

$fp = fopen("cloud-api/middle/src/middleware/logs.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_logs_js); fclose($fp);} 
$fp = fopen("cloud-api/middle/src/middleware/multer.js","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_multer_js); fclose($fp);} 
 
$fp = fopen("cloud-api/middle/Readme.MD","wb"); if( $fp == false ){ }else{ fwrite($fp,$readme_md_content); fclose($fp);} 
?>
 