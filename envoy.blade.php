@servers(['localhost' => '127.0.0.1'])

@setup
$pathOnServer = "./";
@endsetup


@story('deploy')
    app:down
    {{-- app:backup --}}
    {{-- git:pull --}}
    composer:install
    cache:clear
    db:migrate
    app:up
    deploy:done
@endstory

@task('app:down')
    cd {{ $pathOnServer }}
    echo "Bringing the application down"
    php artisan down
@endtask

@task('app:backup')
    cd {{ $pathOnServer }}
    echo "Backing up application"
    php artisan backup:run
@endtask


@task('git:pull')
    echo "Pulling changes on server"
    cd {{ $pathOnServer }}
    echo "git pull origin master"
    git pull origin master
@endtask

@task('composer:install')
    cd {{ $pathOnServer }}
    echo "Running composer install"
    composer update
    composer dump-autoload -o
@endtask

@task('cache:clear')
    cd {{ $pathOnServer }}
    php artisan cache:clear
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
@endtask

@task('db:migrate')
    cd {{ $pathOnServer }}
    echo "Running migrations"
    php artisan migrate --force --env=production
@endtask

@task('app:up')
    cd {{ $pathOnServer }}
    echo "Bringing the application up"
    php artisan up
@endtask

@task('deploy:done', ['on' => 'localhost'])
    echo "Application deployed"
@endtask

@finished
    @telegram('1918942839:AAE-SmPhT1aeSEQBx0ZPTQpNC3FMvY-6tTU','-506252586', "Sunlit's update completed.")
@endfinished
