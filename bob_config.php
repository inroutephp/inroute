<?php

namespace Bob\BuildConfig;

task('default', ['test', 'phpstan', 'sniff']);

desc('Run all tests');
task('test', ['phpspec' , 'behat', 'examples']);

desc('Run phpspec unit tests');
task('phpspec', function() {
    shell('phpspec run');
    println('Phpspec unit tests passed');
});

desc('Run behat feature tests');
task('behat', function() {
    shell('behat --stop-on-failure');
    println('Behat feature tests passed');
});

desc('Test examples');
task('examples', function() {
    shell('readme-tester README.md');
    println('Examples passed');
});

desc('Run statical analysis using phpstan');
task('phpstan', function() {
    shell('phpstan analyze -c phpstan.neon -l 7 src');
    println('Phpstan analysis passed');
});

desc('Run php code sniffer');
task('sniff', function() {
    shell('phpcs src --standard=PSR2 --ignore=router_template.php');
    println('Syntax checker on src/ done');
    shell('phpcs spec --standard=spec/ruleset.xml');
    println('Syntax checker on spec/ done');
});

desc('Globally install development tools');
task('install_dev_tools', function() {
    shell('composer global require consolidation/cgr');
    shell('cgr phpspec/phpspec');
    shell('cgr behat/behat');
    shell('cgr phpstan/phpstan');
    shell('cgr squizlabs/php_codesniffer');
    shell('cgr hanneskod/readme-tester:^1.0@beta');
});

function shell(string $command)
{
    return sh($command, null, ['failOnError' => true]);
}
