This is symfony 5.4 project that contains one symfony command that takes two input parameters:
1st parameter: path\to\file\ ( must point project's public folder, i.e C:\Users\me\commission_fee\public\)
2nd parameter: input.csv - the actual scv file. The name of the file must be exactly input.csv

There is also phpUnit Test for it, wich uses the input data from the provided test input.csv file, proceeds it and asserts the result is same as when the command is ran by hand.


IMPORTANT:
The class CurrencyMyCustomConverter.php (the name of the class may be better, I know) uses a URL to fetch dynamically the currency rates, so if the rates changes, the phpUnit test will fail. In such a case, replace the first argument in assertSame() method in the end of the test file (comission_fee/tests/Command/CommissionFeeTest.php) with copy=>pasted result from the command when ran by hand just before the test. Use double quotes around it, like how it is now. i.e "0.6\r\n3\r\n0\r\n0.06\r\n1.5\r\n0\r\n0.69\r\n0.34\r\n0.3\r\n3\r\n0\r\n0\r\n8607.4\r\n"

Requirements:
composer, php 8.1

Instructions for use:

1. In root folder of the project run "composer install" to install the project dependencies.
2. Run the command with: "php bin/console app:commission_fee_calculator {C:\Users\me\commission_fee\public\ } input.csv", but replace the first argument with your actual path to the input.csv file with trailing slash and without the curly braces. There is one space between the two parameters.
3. Run the test with: "./vendor/bin/simple-phpunit"

The task completion took me about 15 hours.

