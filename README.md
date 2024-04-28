# RESTful API

RESTful API created using Fat-Free Framework. https://fatfreeframework.com/

Sample data is included in api_data.sql

There is an option to log API requests which can be disabled in the class Api.php by setting

    $log_api_calls = false;

on line 6.

Here's how the logging looks like in backend i.e. database.

![API log](https://i.imgur.com/5o17RsV.png)

The API is located in __api/__

Below is a list of allowed methods and API endpoints.

| Method        | URI           |
| ------------- |:-------------:|
| GET           | /             |
| GET           | /{id}         |
| GET           | user/{id}     |
| GET           | /completed    |
| GET           | /uncompleted  |
| POST          | /             |
| PUT           | /{id}         |
| DELETE        | /{id}         |
| DELETE        | user/{id}     |
| DELETE        | /delete/{id}  |
| DELETE        | /delete/all   |
