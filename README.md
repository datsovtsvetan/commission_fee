this is symfony 5.4 project that has one command that takes two input parameters:
1. path\to\file\ ( must point project's public folder, i.e C:\Users\me\commission_fee\public\ input.csv)
2. input.csv - the actual scv file.

Requirements:
    composer, php 8

Instructions for use:

1. In root folder of the project run "composer install" to install the project dependencies.
2. Run the command with: "php bin/console app:commission_fee_calculator {C:\Users\me\commission_fee\public\} input.csv", but replace the first argument with your actual path to the input.csv file with trailing slash.
3. Test with: "./vendor/bin/simple-phpunit"


The task completion took me about 15 hours.

