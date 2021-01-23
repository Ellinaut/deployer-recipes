<?php

namespace Deployer;

use Deployer\Task\Context;

set('rsync_paths', []);

desc('Rsync assets');
task(
    'deploy:ellinaut:rsync:assets',
    function () {
        $host     = Context::get()->getHost();
        $hostname = $host->getRealHostname();
        $user     = $host->getUser();
        $paths    = get('rsync_paths', []);

        foreach ($paths as $path) {
            $rsync = sprintf(
                'rsync -avz %s %s@%s:{{release_path}}/%s',
                $path,
                $user,
                $hostname,
                $path
            );
            runLocally($rsync);
        }
    }
);
