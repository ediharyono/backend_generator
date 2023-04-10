<?php
$readme_md = '
Json Web Token (JWT) Basic

1) npm i express jsonwebtoken dotenv

';
$dotenv = 
"
REFRESH_TOKEN_SECRET = 1
ACCESS_TOKEN_SECRET = 1
";

$content = 
"
require('dotenv').config()

const express = require('express')
const app = express()
const jwt = require('jsonwebtoken')

app.use(express.json())

let refreshTokens = []

app.post('/token', (req, res) => {
  const refreshToken = req.body.token
  if (refreshToken == null) return res.sendStatus(401)
  if (!refreshTokens.includes(refreshToken)) return res.sendStatus(403)
  jwt.verify(refreshToken, process.env.REFRESH_TOKEN_SECRET, (err, user) => {
    if (err) return res.sendStatus(403)
    const accessToken = generateAccessToken({ name: user.name })
    res.json({ accessToken: accessToken })
  })
})

app.delete('/logout', (req, res) => {
  refreshTokens = refreshTokens.filter(token => token !== req.body.token)
  res.sendStatus(204)
})

app.post('/login', (req, res) => {
  // Authenticate User
  const username = req.body.username
  const user = { name: username }
  
  const accessToken = generateAccessToken(user)
  const refreshToken = jwt.sign(user, process.env.REFRESH_TOKEN_SECRET)
  refreshTokens.push(refreshToken)
        res.json({ accessToken: accessToken, refreshToken: refreshToken })
})

function generateAccessToken(user) {
  return jwt.sign(user, process.env.ACCESS_TOKEN_SECRET, { expiresIn: '15 min' })
}

app.listen(4000)
console.log('Server RUN');

";
//membuat folder
if (!is_dir('cloud-api/jwt/basic')) {mkdir('cloud-api/jwt/basic', 0777, true);}
$fp = fopen("cloud-api/jwt/basic/Readme.MD","wb");if( $fp == false ){ }else{ fwrite($fp,$readme_md); fclose($fp);}
$fp = fopen("cloud-api/jwt/basic/server.js","wb");if( $fp == false ){ }else{ fwrite($fp,$content); fclose($fp);}
$fp = fopen("cloud-api/jwt/basic/.env","wb");if( $fp == false ){ }else{ fwrite($fp,$dotenv); fclose($fp);}
?>