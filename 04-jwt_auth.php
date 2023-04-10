<?php

$readme_md = '
Json Web Token (JWT) Middle

1. npm init -y
2. npm i --save express jsonwebtoken dotenv cors sequelize mysql2 mysql bcryptjs uuid


## Project setup
```
npm install
```

### Run
```
node server.js
```

Example

localhost:8080/v1/auth/signup
body:

  {
  "email" : "ak@gmail.com", 
  "username" : "Damar AsjP",
  "password" : "071002239",
  "role" : "1"
  }

  localhost:8080/v1/auth/signin

  {
  "email" : "ak@gmail.com", 
  "username" : "Damar AsjP",
  "password" : "071002239"
  }

  
';

$server_js =
'
const express = require("express");
const cors = require("cors");

const app = express();

var corsOptions = {
  origin: "http://localhost:8081"
};

app.use(cors(corsOptions));

// parse requests of content-type - application/json
app.use(express.json());

// parse requests of content-type - application/x-www-form-urlencoded
app.use(express.urlencoded({ extended: true }));

// database
const db = require("./app/models");
const Role = db.role;

db.sequelize.sync();
// force: true will drop the table if it already exists
// db.sequelize.sync({force: true}).then(() => {
//   console.log("Drop and Resync Database with { force: true }");
//   initial();
// });

// simple route
app.get("/", (req, res) => {
  res.json({ message: "Welcome to bezkoder application." });
});

// routes
require("./app/routes/auth.routes")(app);
require("./app/routes/user.routes")(app);

// set port, listen for requests
const PORT = process.env.PORT || 8080;
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}.`);
});

function initial() {
  Role.create({
    id: 1,
    name: "user"
  });
 
  Role.create({
    id: 2,
    name: "moderator"
  });
 
  Role.create({
    id: 3,
    name: "admin"
  });
}
'
;

$auth_config_js = 'module.exports = {
  secret: "secret-key"
};
';
$db_config_js = '
module.exports = {
  HOST: "localhost",
  USER: "root",
  PASSWORD: "",
  DB: "classroo_uii_gateway",
  dialect: "mysql",
  pool: {
    max: 5,
    min: 0,
    acquire: 30000,
    idle: 10000
  }
};

';

$auth_controller_js = '
const db = require("../models");
const config = require("../config/auth.config");
const User = db.user;
const Role = db.role;

const Op = db.Sequelize.Op;

var jwt = require("jsonwebtoken");
var bcrypt = require("bcryptjs");

exports.signup = (req, res) => {
  // Save User to Database
  User.create({
    username: req.body.username,
    email: req.body.email,
    password: bcrypt.hashSync(req.body.password, 8)
  })
    .then(user => {
      if (req.body.roles) {
        Role.findAll({
          where: {
            name: {
              [Op.or]: req.body.roles
            }
          }
        }).then(roles => {
          user.setRoles(roles).then(() => {
            res.send({ message: "User registered successfully!" });
          });
        });
      } else {
        // user role = 1
        user.setRoles([1]).then(() => {
          res.send({ message: "User registered successfully!" });
        });
      }
    })
    .catch(err => {
      res.status(500).send({ message: err.message });
    });
};

exports.signin = (req, res) => {
  User.findOne({
    where: {
      username: req.body.username
    }
  })
    .then(user => {
      if (!user) {
        return res.status(404).send({ message: "User Not found." });
      }

      var passwordIsValid = bcrypt.compareSync(
        req.body.password,
        user.password
      );

      if (!passwordIsValid) {
        return res.status(401).send({
          accessToken: null,
          message: "Invalid Password!"
        });
      }

      var token = jwt.sign({ id: user.id }, config.secret, {
        expiresIn: 86400 // 24 hours
      });

      var authorities = [];
      user.getRoles().then(roles => {
        for (let i = 0; i < roles.length; i++) {
          authorities.push("ROLE_" + roles[i].name.toUpperCase());
        }
        res.status(200).send({
          id: user.id,
          username: user.username,
          email: user.email,
          roles: authorities,
          accessToken: token
        });
      });
    })
    .catch(err => {
      res.status(500).send({ message: err.message });
    });
};

';

$user_controller_js = '
exports.allAccess = (req, res) => {
  res.status(200).send("Public Content.");
};

exports.userBoard = (req, res) => {
  res.status(200).send("User Content.");
};

exports.adminBoard = (req, res) => {
  res.status(200).send("Admin Content.");
};

exports.moderatorBoard = (req, res) => {
  res.status(200).send("Moderator Content.");
};

';

$authJwt_js = '
const jwt = require("jsonwebtoken");
const config = require("../config/auth.config.js");
const db = require("../models");
const User = db.user;

verifyToken = (req, res, next) => {
  let token = req.headers["x-access-token"];

  if (!token) {
    return res.status(403).send({
      message: "No token provided!"
    });
  }

  jwt.verify(token, config.secret, (err, decoded) => {
    if (err) {
      return res.status(401).send({
        message: "Unauthorized!"
      });
    }
    req.userId = decoded.id;
    next();
  });
};

isAdmin = (req, res, next) => {
  User.findByPk(req.userId).then(user => {
    user.getRoles().then(roles => {
      for (let i = 0; i < roles.length; i++) {
        if (roles[i].name === "admin") {
          next();
          return;
        }
      }

      res.status(403).send({
        message: "Require Admin Role!"
      });
      return;
    });
  });
};

isModerator = (req, res, next) => {
  User.findByPk(req.userId).then(user => {
    user.getRoles().then(roles => {
      for (let i = 0; i < roles.length; i++) {
        if (roles[i].name === "moderator") {
          next();
          return;
        }
      }

      res.status(403).send({
        message: "Require Moderator Role!"
      });
    });
  });
};

isModeratorOrAdmin = (req, res, next) => {
  User.findByPk(req.userId).then(user => {
    user.getRoles().then(roles => {
      for (let i = 0; i < roles.length; i++) {
        if (roles[i].name === "moderator") {
          next();
          return;
        }

        if (roles[i].name === "admin") {
          next();
          return;
        }
      }

      res.status(403).send({
        message: "Require Moderator or Admin Role!"
      });
    });
  });
};

const authJwt = {
  verifyToken: verifyToken,
  isAdmin: isAdmin,
  isModerator: isModerator,
  isModeratorOrAdmin: isModeratorOrAdmin
};
module.exports = authJwt;

';
$index_js = '
const authJwt = require("./authJwt");
const verifySignUp = require("./verifySignUp");

module.exports = {
  authJwt,
  verifySignUp
};

';
$verifySignUp_js = '
const db = require("../models");
const ROLES = db.ROLES;
const User = db.user;

checkDuplicateUsernameOrEmail = (req, res, next) => {
  // Username
  User.findOne({
    where: {
      username: req.body.username
    }
  }).then(user => {
    if (user) {
      res.status(400).send({
        message: "Failed! Username is already in use!"
      });
      return;
    }

    // Email
    User.findOne({
      where: {
        email: req.body.email
      }
    }).then(user => {
      if (user) {
        res.status(400).send({
          message: "Failed! Email is already in use!"
        });
        return;
      }

      next();
    });
  });
};

checkRolesExisted = (req, res, next) => {
  if (req.body.roles) {
    for (let i = 0; i < req.body.roles.length; i++) {
      if (!ROLES.includes(req.body.roles[i])) {
        res.status(400).send({
          message: "Failed! Role does not exist = " + req.body.roles[i]
        });
        return;
      }
    }
  }
  
  next();
};

const verifySignUp = {
  checkDuplicateUsernameOrEmail: checkDuplicateUsernameOrEmail,
  checkRolesExisted: checkRolesExisted
};

module.exports = verifySignUp;

';

$index_model_js = '
const config = require("../config/db.config.js");

const Sequelize = require("sequelize");
const sequelize = new Sequelize(
  config.DB,
  config.USER,
  config.PASSWORD,
  {
    host: config.HOST,
    dialect: config.dialect,
    operatorsAliases: false,

    pool: {
      max: config.pool.max,
      min: config.pool.min,
      acquire: config.pool.acquire,
      idle: config.pool.idle
    }
  }
);

const db = {};

db.Sequelize = Sequelize;
db.sequelize = sequelize;

db.user = require("../models/user.model.js")(sequelize, Sequelize);
db.role = require("../models/role.model.js")(sequelize, Sequelize);

db.role.belongsToMany(db.user, {
  through: "user_roles",
  foreignKey: "roleId",
  otherKey: "userId"
});
db.user.belongsToMany(db.role, {
  through: "user_roles",
  foreignKey: "userId",
  otherKey: "roleId"
});

db.ROLES = ["user", "admin", "moderator"];

module.exports = db;

';

$role_model_js = '
module.exports = (sequelize, Sequelize) => {
  const Role = sequelize.define("roles", {
    id: {
      type: Sequelize.INTEGER,
      primaryKey: true
    },
    name: {
      type: Sequelize.STRING
    }
  });

  return Role;
};

';

$user_model_js = '
module.exports = (sequelize, Sequelize) => {
  const User = sequelize.define("users", {
    username: {
      type: Sequelize.STRING
    },
    email: {
      type: Sequelize.STRING
    },
    password: {
      type: Sequelize.STRING
    }
  });

  return User;
};
';

$routes_auth_js = '
const { verifySignUp } = require("../middleware");
const controller = require("../controllers/auth.controller");

module.exports = function(app) {
  app.use(function(req, res, next) {
    res.header(
      "Access-Control-Allow-Headers",
      "x-access-token, Origin, Content-Type, Accept"
    );
    next();
  });

  app.post(
    "/v1/auth/signup",
    [
      verifySignUp.checkDuplicateUsernameOrEmail,
      verifySignUp.checkRolesExisted
    ],
    controller.signup
  );

  app.post("/v1/auth/signin", controller.signin);
};

';

$routes_user_js = '
const { authJwt } = require("../middleware");
const controller = require("../controllers/user.controller");

module.exports = function(app) {
  app.use(function(req, res, next) {
    res.header(
      "Access-Control-Allow-Headers",
      "x-access-token, Origin, Content-Type, Accept"
    );
    next();
  });

  app.get("/v1/test/all", controller.allAccess);

  app.get(
    "/v1/test/user",
    [authJwt.verifyToken],
    controller.userBoard
  );

  app.get(
    "/v1/test/mod",
    [authJwt.verifyToken, authJwt.isModerator],
    controller.moderatorBoard
  );

  app.get(
    "/v1/test/admin",
    [authJwt.verifyToken, authJwt.isAdmin],
    controller.adminBoard
  );
};

';

//membuat folder
if (!is_dir('cloud-api/jwt/middle')) {mkdir('cloud-api/jwt/middle', 0777, true);}
if (!is_dir('cloud-api/jwt/middle/app')) {mkdir('cloud-api/jwt/middle/app', 0777, true);}

if (!is_dir('cloud-api/jwt/middle/app/config')) {mkdir('cloud-api/jwt/middle/app/config', 0777, true);}
if (!is_dir('cloud-api/jwt/middle/app/controllers')) {mkdir('cloud-api/jwt/middle/app/controllers', 0777, true);}
if (!is_dir('cloud-api/jwt/middle/app/middleware')) {mkdir('cloud-api/jwt/middle/app/middleware', 0777, true);}
if (!is_dir('cloud-api/jwt/middle/app/models')) {mkdir('cloud-api/jwt/middle/app/models', 0777, true);}
if (!is_dir('cloud-api/jwt/middle/app/routes')) {mkdir('cloud-api/jwt/middle/app/routes', 0777, true);}


$fp = fopen("cloud-api/jwt/middle/Readme.MD","wb");if( $fp == false ){ }else{ fwrite($fp,$readme_md); fclose($fp);}
$fp = fopen("cloud-api/jwt/middle/server.js","wb");if( $fp == false ){ }else{ fwrite($fp,$server_js); fclose($fp);}


$fp = fopen("cloud-api/jwt/middle/app/config/auth.config.js","wb");if( $fp == false ){ }else{ fwrite($fp,$auth_config_js); fclose($fp);}
$fp = fopen("cloud-api/jwt/middle/app/config/db.config.js","wb");if( $fp == false ){ }else{ fwrite($fp,$db_config_js); fclose($fp);}

$fp = fopen("cloud-api/jwt/middle/app/controllers/auth.controller.js","wb");if( $fp == false ){ }else{ fwrite($fp,$auth_controller_js); fclose($fp);}
$fp = fopen("cloud-api/jwt/middle/app/controllers/user.controller.js","wb");if( $fp == false ){ }else{ fwrite($fp,$user_controller_js); fclose($fp);}

$fp = fopen("cloud-api/jwt/middle/app/middleware/authJwt.js","wb");if( $fp == false ){ }else{ fwrite($fp,$authJwt_js); fclose($fp);}
$fp = fopen("cloud-api/jwt/middle/app/middleware/index.js","wb");if( $fp == false ){ }else{ fwrite($fp,$index_js); fclose($fp);}
$fp = fopen("cloud-api/jwt/middle/app/middleware/verifySignUp.js","wb");if( $fp == false ){ }else{ fwrite($fp,$verifySignUp_js); fclose($fp);}

$fp = fopen("cloud-api/jwt/middle/app/models/index.js","wb");if( $fp == false ){ }else{ fwrite($fp,$index_model_js); fclose($fp);}
$fp = fopen("cloud-api/jwt/middle/app/models/role.model.js","wb");if( $fp == false ){ }else{ fwrite($fp,$role_model_js); fclose($fp);}
$fp = fopen("cloud-api/jwt/middle/app/models/user.model.js","wb");if( $fp == false ){ }else{ fwrite($fp,$user_model_js); fclose($fp);}

$fp = fopen("cloud-api/jwt/middle/app/routes/auth.routes.js","wb");if( $fp == false ){ }else{ fwrite($fp,$routes_auth_js); fclose($fp);} 
$fp = fopen("cloud-api/jwt/middle/app/routes/user.routes.js","wb");if( $fp == false ){ }else{ fwrite($fp,$routes_user_js); fclose($fp);} 
?>