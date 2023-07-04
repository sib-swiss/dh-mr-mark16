@setup
    $repository = 'git@gitlab.sib.swiss:mark16-vre-group/manuscript.git';

    $server_dev = 'mark16@mark16-dev8.vital-it.ch';
    $server_prod = 'mark16@mark16-prod8.vital-it.ch';

    $deploy_path_dev = '/var/vhosts/vital-it.ch/mark16-dev/htdocs';
    $deploy_path_prod = '/var/vhosts/vital-it.ch/mark16-prod/htdocs';

    $app_dir = $server === 'prod' ? $deploy_path_prod : $deploy_path_dev ;

    $server = isset($server) ? $server : 'dev' ;
@endsetup


@servers(['dev' => $server_dev, 'prod' => $server_prod])


@story('deploy', ['on' => $server])
    pull_repository
    run_composer
    migrate_database
    restart_queue_workers
@endstory


@task('pull_repository')
    cd {{ $app_dir }}
    git pull
@endtask


@task('run_composer')
    echo "Running composer"
    cd {{ $app_dir }}
    composer install --prefer-dist -q -o
@endtask


@task('migrate_database')
    echo "Migrating database"
    cd {{ $app_dir }}
    php artisan migrate --force
@endtask


@task('restart_queue_workers')
    echo "Restart Queue Workers"
    cd {{ $app_dir }}
    php artisan queue:restart
@endtask
