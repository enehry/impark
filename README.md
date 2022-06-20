## IMPARK 

Impark is a stock and inventory management system.

prerequisite:
you need to install 
* XAMPP   : https://www.apachefriends.org/
* composer: https://getcomposer.org/
* nodejs  : https://nodejs.org/en/

**How to run the project on your local machine:**
1. Clone the project from [GitHub]
2. Run **composer install**
3. Run **php artisan key:generate**
4. Run **php artisan migrate --seed**
5. Run **php artisan serve**
6. Run **npm install** (It uses inertia and vue so you need to run also the NPM)
7. Run **npm run dev** (or **npm run prod**, if you want to run in dev mode with realtime updates **npm run watch**)
8. Open http://localhost:8000/

Note: you need to copy env.example to root folder and rename it to .env
then setup sa DB connection in .env file
you need to create a database first.

