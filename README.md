# A-LED Weather API

![Aled logo](logo.png)

This API manages the import of temperature et humidity readings of an HTU-21 module sent by an ESP8266 01-S. 
It also stands as Aled-weather webapp backend by managing user registration, authentification, token creation. 

## Features

- Creation of temperature reports in the database
- Display of reports by ID, location, period of time
- Retrieval of the list of available locations
- User registration with name, email, and password
- User authentification
- Creation of JWT tokens for registered users
- General CRUD operations with Reports, Users, and Locations data

## Installation

In production environment, this project is configured for an Apache http server.
But you can alternatively use php built-in server for a simple test and development context :

- Clone the repository to your machine
- Install PHP 8.2.1.
- Set the "cgi.fix_pathinfo" to 0 in your php.ini file to avoid route interpretation
- Install dependencies using `composer install`
- Install MariaDb
- Configure the database connection and secret JWT encoding key in the .env file
- launch the initialize_db_insert_data_set.sql script into your database (you can remove the dataset INSERT part as it stands for test purpose)
- Start the PHP server using the command `php -S 0.0.0.0:8080 -t path_to_your_application_root_folder -c path_to_your_php.ini_file ./index.php` 

### With Docker

- Set up your .env file variables accordingly to your docker-compose.yaml file.
- use the command `docker compose up` from your application directory
- RUN !

## Usage

First you need to create a user account by using addUser request, then create a token for your account with createToken request which returns a jwt to include in requiring requests headers.

Consult the API documentation to learn how to use the different routes and features : https://aled-weather.fr:8888/

## Contribution

This is a school project, so it is very improvable.

So all advices and contributions are welcome! For bugs or feature requests, please open an issue. For direct contributions, please send a pull request.