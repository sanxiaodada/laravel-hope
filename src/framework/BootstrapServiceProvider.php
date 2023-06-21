<?php
namespace Framework;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use InfluxDB\Client as InfluxClient;
use InfluxDB\Database as InfluxDB;
use InfluxDB\Driver\UDP;

class BootstrapServiceProvider extends ServiceProvider
{
    public function register()
    {
//        $this->app->singleton('session', function () {
//            return new EmptySession();
//        });

        $this->app->singleton(InfluxDB::class, function($app) {
            $client = new InfluxClient(
                config('influxdb.host'),
                config('influxdb.port'),
                config('influxdb.username'),
                config('influxdb.password'),
                config('influxdb.ssl'),
                config('influxdb.verifySSL'),
                config('influxdb.timeout')
            );
            if (config('influxdb.udp.enabled') === true) {
                $client->setDriver(new UDP(
                    $client->getHost(),
                    config('influxdb.udp.port')
                ));
            }
            return $client->selectDB(config('influxdb.dbname'));
        });

        PrintSql::listen();
    }

    public function boot()
    {
        $flg = app()->configurationIsCached();
        if (!$flg) {
            $conf = ConfigDataCommand::loadDataConfig();
            if (count($conf)) {
                config()->set($conf);
            }
        }
    }
}
