This is symfony 5.4 project that contains one symfony command that takes two input parameters:
1st parameter: path\to\file\ ( must point project's public folder, i.e C:\Users\me\commission_fee\public\)
2nd parameter: someFile.csv - the actual scv file. The name of the file can be any name, provided it is in .csv format. It is however case-sensitive.

There is also phpUnit Test for it, wich uses the input data from the provided in the task's description test input.csv file, proceeds it and asserts the result is same as the result in the task's description. And the mocked values for API call are the ones from the task's description.

Requirements:
composer, php 8.1

Instructions for use:

1. In root folder of the project run "composer install" to install the project dependencies.
2. Run the command with: "php bin/console app:commission_fee_calculator {C:\Users\me\commission_fee\public\ } someFile.csv", but replace the first argument with your actual path to the someFile.csv file with trailing slash and without the curly braces. There is one space between the two parameters.
3. Run the test with: "./vendor/bin/simple-phpunit"

The task completion took me about 15 hours.

