<?php


namespace Framework;


use Illuminate\Support\Facades\DB;

class PrintSql
{
    public static $sql = [];

    public static function listen()
    {
        DB::listen(function ($sql) {
            foreach ($sql->bindings as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } else {
                    if (is_string($binding)) {
                        $sql->bindings[$i] = "'$binding'";
                    }
                }
            }

            // Insert bindings into query
            $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

            self::$sql[] = vsprintf($query, $sql->bindings);
        });
    }
}
