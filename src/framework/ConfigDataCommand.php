<?php

namespace Framework;

use Illuminate\Foundation\Console\ConfigCacheCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LogicException;
use Throwable;

class ConfigDataCommand extends ConfigCacheCommand
{
    protected $signature = 'config:data';

    protected $description = '生成数据配置缓存';

    public function handle()
    {
        $this->call('config:clear');

        $config = $this->getFreshConfiguration();

        $config_data = static::loadDataConfig();

        $configPath = $this->laravel->getCachedConfigPath();

        $this->files->put(
            $configPath, '<?php return '.var_export(array_merge($config, $config_data), true).';'.PHP_EOL
        );

        try {
            require $configPath;
        } catch (Throwable $e) {
            $this->files->delete($configPath);

            throw new LogicException('Your configuration files are not serializable.', 0, $e);
        }

        $this->info('Configuration cached successfully!');
    }

    public static function loadDataConfig(): array
    {
        $database = config('database.connections.mysql.database');
        $res = collect(Schema::getAllTables())
            ->map(function($item) use ($database) {
                $col = 'Tables_in_' . $database;
                return $item->$col;
            })
            ->filter(function($table) use ($database) {
                return Str::startsWith($table, 'config_');
            })
            ->reduce(function($ret, $table) {
//                $conf = Cache::store('file')->remember($table, 86400, function() use ($table) {
                $data = [];
                $name = Str::substr($table, 7);
                $list = DB::table($table)->get();
                foreach ($list as $item) {
                    $key = $item->key;

                    foreach ($item as $col => $val) {
                        if (!in_array($col, [ 'created_at', 'updated_at'])) {
                            $data["data.$name.$key.$col"] = $val;
                        }
                    }
                }
//                    return $data;
//                });

                $ret = array_merge($ret ?? [], $data);
                return $ret;
            });
        return $res ?? [];
    }
}
