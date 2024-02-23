# Test

### About API

A simple one-route API for calculating some statistical values.

Post route takes a text file as a list of numeric values, processes it and returns the following values:
- _minimum value_;
- _maximum value_;
- _average_;
- _median_;
- _longest increasing sequence of numbers_;
- _longest decreasing sequence of numbers_.

### How to use

This API assumes the following:
- Install an IDE, for example PHPStorm, and clone this repository using _**git clone**_;
- Install Docker desktop to launch the runtime environment with Nginx and PHP.
All configuration files are contained in this repository. You can _**docker-compose up**_ to run containers, and _**ctrl+C**_ to exit;
- Install postman and import the _**Test.postman_collection.json**_ from this repository, which contains the route for testing;
- The route accepts only _**one .txt file**_ with a list of numbers (one line - one number);
- Because memory resources are not infinite, there is a limit of _**100MB**_ on the uploaded file;
- The API returns the response in _**json**_ format

### Author

This API was developed by **Kate Koltsova**.

To contact the developer, use the methods specified in the profile.
