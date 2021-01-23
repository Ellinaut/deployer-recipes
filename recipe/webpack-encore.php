<?php

namespace Deployer;


set('stages_with_development_assets_build', ['development']);
set('stages_with_production_assets_build', ['staging', 'production']);

$isPipeline = getenv('BITBUCKET_BUILD_NUMBER');

task(
    'deploy:ellinaut:yarn:install',
    static function () {
        run('yarn install');
    }
)->local();

task(
    'deploy:ellinaut:frontend:build',
    static function () {
        run('yarn run encore dev');
    }
)->onStage(get('stages_with_development_assets_build'))->local();

task(
    'deploy:ellinaut:frontend:build',
    static function () {
        run('yarn run encore production');
    }
)->onStage(get('stages_with_production_assets_build'))->local();

if (!$isPipeline) {
    before('deploy:prepare', 'deploy:ellinaut:yarn:install');
    after('deploy:ellinaut:yarn:install', 'deploy:ellinaut:frontend:build');
}
