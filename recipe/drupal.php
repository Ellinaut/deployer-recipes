<?php

namespace Deployer;

// Path to drush
set(
    'bin/drush',
    function () {
        return parse('{{bin/php}} {{release_path}}/vendor/bin/drush');
    }
);


//Set drupal site. Change if you use different site
set('drupal_site', 'default');

// Shared files/dirs between deploys
add(
    'shared_files',
    [
        'drush/drush.yml',
        'web/sites/{{drupal_site}}/settings.local.php',
        'web/sites/development.services.yml',
        'web/sites/{{drupal_site}}/services.yml',
        'web/sites/{{drupal_site}}/settings.php',
    ]
);
add(
    'shared_dirs',
    [
        'web/sites/{{drupal_site}}/files',
    ]
);

desc('Clear cache');
task(
    'deploy:ellinaut:drupal:cache:clear',
    function () {
        run('{{bin/drush}} cr');
    }
);

desc('Update database');
task(
    'deploy:ellinaut:drupal:database:update',
    function () {
        run('{{bin/drush}} updb -y');
    }
);

desc('Config import');
task(
    'deploy:ellinaut:drupal:config:import',
    function () {
        run('{{bin/drush}} cim -y');
    }
);

desc('Maintenance mode enable');
task(
    'deploy:ellinaut:drupal:maintenance:enable',
    function () {
        run('{{bin/drush}} sset system.maintenance_mode 1');
    }
);

desc('Maintenance mode disable');
task(
    'deploy:ellinaut:drupal:maintenance:disable',
    function () {
        run('{{bin/drush}} sset system.maintenance_mode 0');
    }
);


desc('Deploy project');
task(
  'deploy',
  [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:ellinaut:drupal:maintenance:enable',
    'deploy:ellinaut:drupal:cache:clear',
    'deploy:ellinaut:drupal:database:update',
    'deploy:ellinaut:drupal:config:import',
    'deploy:ellinaut:drupal:cache:clear',
    'deploy:ellinaut:drupal:maintenance:disable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
  ]
);
after('deploy', 'success');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
