# Page analyzer

Page analyzer is application based on Slim framework. For SQL-queries in this project I used PDO extension. For building frontend interface I used Bootstrap. 

Page SEO-review provides by analyzing HTML code of the requesting page using DOM API and getting from the object certain elements.

Project architecture based on MVC building principles.

Follow link for discovering app *(in Russia works only with VPN, because of deploying on Railway platform)*:

https://php-project-9-production-aaz.up.railway.app

If link above doesn’t work you can install app locally.  
Edit file ``.env.example`` *(write your own configuration for connection to database)*. You need to have MySQL service installed locally. Then run this command for installing:
```
make install_local
```
For running project on local server:
```
make start
```
