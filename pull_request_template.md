Please answer the following questions about your work:

0 - What language did you program in?
PHP

1 - Have you manually tested the SDK?
Yes, I created usage examples (/examples/usage-example.php) that interact with the MoviesEndpoint and QuotesEndpoint classes, simulating real API calls, including pagination, filtering, and specific resource fetching. 

2 - Did you add a test suite? If so, how will we use it? If not, why?
Yes, PHPUnit. 
To run the test on specific class, 
`vendor/bin/phpunit --filter <TestClassName>`
To run all the tests,
`vendor/bin/phpunit`

3 - Did you use any 3rd party library? Why did you use it? What are the tradeoffs?
Composer for dependency management and autoloading, and PHPUnit for unit testing.
Composer is standard package manager for PHP and users just need to install it.
PHP unit is widely used test framework for PHP and easy to mock API calls.

4 - Do you feel this SDK makes it easier to interact with the API?
Yes, the SDK simplifies interaction with original the Lord of the Rings API. By taking away the complexities of HTTP requests, authentication, pagination, and error handling, users can fetch data by calling simpler methods, like getAllMovies(), getAllQuotes(), or getMovieQuotes(), without managing low-level API calls. The factory methods also streamline initialization by allowing users to provide only the API key to start using SDK.

5 - If you had more time, what else would you add?
-add all available API endpoints
-more detailed tests
-add documentation with detailed examples and sample codes
-add detailed error logging, it might be good idea to push all event logs to cloud based logging system such as AWS CloudWatch.
-integrate with some tools like DataDog to quickly resolve performance issues, sending alarms to stand-by teams to take care of emergency down times etc.

6 - What would you change in your current SDK solution?
-add caching for frequently fetched data

7 - On a scale of 1 to 10 (10 being the highest), how would you rate this solution?
- 8 since there is still room for improvement 

8 - Anything else we should keep in mind when we evaluate the project?
- It is designed to be extensible, adding new endpoints can be done easily
- PHPUnit is already setup and developers can test their code without replying on live data
- It's using PSR-4 autoloading to automatic loading of dependences, using namespaces to prevent naming conflicts, improve code organization
