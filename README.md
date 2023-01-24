# A-LED Weather API

This API support the process of temperature et humidity readings of an HTU-21 module sent by an ESP8266 01-S. It also handle Aled webapp backend by managing user registration and authentification, token creation. First you need to create an user account by using addUser request, then create a token for your account with createToken request which returns a jwt to include in requiring requests headers.

## Features

- Creation of temperature reports in the database
- Display of reports by ID, location, device, hourly and daily
- Creation of JWT tokens for authentication of requests
- Retrieval of the list of devices and available locations
- User registration with name, email, and password

## Installation

- Clone the repository to your machine
- Install dependencies using composer
- Configure the database connection in the .env file
- Start the PHP server using the command `php -S localhost:8000`

## Usage

Consult the API documentation to learn how to use the different routes and features : https://aled-weather.fr:8888/

## Contribution

All contributions are welcome! For bugs or feature requests, please open an issue. For direct contributions, please send a pull request.