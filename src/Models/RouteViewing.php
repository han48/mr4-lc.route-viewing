<?php

namespace Mr4Lc\RouteViewing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use stdClass;

class RouteViewing extends Model
{
    use SoftDeletes;

    const VIEWING = 1;
    const CLOSED = 0;
    const PENDING = -1;

    protected $table = 'route_viewings';
    protected $guarded = [];

    public static function UpdateOrCreate($data)
    {
        if (!isset($data)) {
            return null;
        }
        $record = [
            'href' => static::GetDataFromObject($data, 'href'),
            'path' => static::GetDataFromObject($data, 'path'),
            'resource_id' => static::GetDataFromObject($data, 'resource_id'),
            'session_id' => static::GetDataFromObject($data, 'session_id'),
            'config' => static::GetDataFromObject($data, 'config', "0"),
            'status' => static::GetDataFromObject($data, 'action') === 'open' ? static::VIEWING : static::CLOSED,
        ];
        static::where('resource_id', $record['resource_id'])->delete();
        if ($record['status'] === static::VIEWING) {
            $suffix = config('mr4lc-route-viewing.lock.suffix', null);
            if (isset($suffix) && str_ends_with($record['path'], $suffix)) {
                $count = static::where('path', $record['path'])->where('resource_id', '<>', $record['resource_id'])->where('status', static::VIEWING);
                $count = $count->count();
                if ($count > 0) {
                    $record['status'] = static::PENDING;
                }
            }
            return static::create($record);
        }
        return null;
    }

    public static function GetDataFromObject($data, $key, $default = 'N/A')
    {
        if (property_exists($data, $key)) {
            return $data->{$key};
        }
        return $default;
    }

    public function getUser()
    {
        $user = new stdClass();
        $user->displayName = __('mr4lc-route-viewing.user.guest');
        if (config('session.driver') === 'database') {
            try {
                $session = DB::table('sessions')->where('id', $this->session_id)->first();
                if (isset($session)) {
                    $config = config('mr4lc-route-viewing.config.' . $this->config);
                    $model = new $config['user_class']();
                    $obj = $model->find($session->user_id);
                    if (isset($obj)) {
                        if (array_key_exists($config['display_name'], $obj->attributes)) {
                            $user->displayName = $obj->{$config['display_name']};
                        } else if (array_key_exists('displayName', $obj->attributes)) {
                            $user->displayName = $obj->displayName;
                        } else if (array_key_exists('name', $obj->attributes)) {
                            $user->displayName = $obj->name;
                        }
                    }
                }
            } catch (\Exception $ex) {;
            }
        } else {
            $user->displayName = __('mr4lc-route-viewing.user.anyone');
        }
        return $user;
    }
}
