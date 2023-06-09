<?php

$content_readme = "1. npm init-y
2. npm i express jsonwebtoken uuid
3. node index.js
4. post http://localhost:5000/api/login
5. copy token in respon
6. get  http://localhost:5000/api/profile";


$content_bearer ="
const express = require('express');
const jwt = require('jsonwebtoken');
const uuid = require('uuid');

const app = express();
app.use(express.json())

app.post('/api/login',(req,res)=>{
    //you can do this either synchronously or asynchronously
    //if synhronously, you can set a variable to jwt sign and pass it into the payload with secret key
    //if async => call back 


    //Mock user
    const user = {
        //id:Date.now(),
        id:uuid.v4(),
        userEmail:'example@gmail.com',
        password:'123'
    }

    //send abpve as payload
    jwt.sign({user},'secretkey',(err,token)=>{
        res.json({
            token
        })
    })
})



app.get('/api/profile',verifyToken,(req,res)=>{

    jwt.verify(req.token,'secretkey',(err,authData)=>{
        if(err)
            res.sendStatus(403);
        else{
            res.json({
                message:'Welcome to Profile',
                userData:authData
            })
           
        }
    })
  
});


//Verify Token
function verifyToken(req,res,next){
    //Auth header value = > send token into header

    const bearerHeader = req.headers['authorization'];
    //check if bearer is undefined
    if(typeof bearerHeader !== 'undefined'){

        //split the space at the bearer
        const bearer = bearerHeader.split(' ');
        //Get token from string
        const bearerToken = bearer[1];

        //set the token
        req.token = bearerToken;

        //next middleweare
        next();

    }else{
        //Fobidden
        res.sendStatus(403);
    }

}

app.listen(5000,err=>{
    if(err) {
        console.log(err);
    }
    console.log('Server Started on PORT 5000')
})";

if (!is_dir('cloud-api/bearer/basic')) {mkdir('cloud-api/bearer/basic', 0777, true);}
 
$fp = fopen("cloud-api/bearer/basic/index.js","wb");if( $fp == false ){ }else{ fwrite($fp,$content_bearer); fclose($fp);}
 $fp = fopen("cloud-api/bearer/basic/Readme.MD","wb");if( $fp == false ){ }else{ fwrite($fp,$content_readme); fclose($fp);}

?>
