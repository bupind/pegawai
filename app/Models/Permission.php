<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
    ];

    public static function getDataByGroupSuperadmin()
    {
        $permissions = self::orderBy('name')->get();
        $groupedData = [];

        foreach($permissions as $permission) {
            $parts      = explode(' ', $permission->name);
            $group      = $parts[0];
            $data       = $parts[1];
            $groupIndex = array_search($group, array_column($groupedData, 'group'));

            if($groupIndex !== false) {
                $groupedData[$groupIndex]['data'][] = ['id' => $permission->id, 'name' => $data];
            }
            else {
                $groupedData[] = [
                    'group' => $group,
                    'data'  => [['id' => $permission->id, 'name' => $data]],
                ];
            }
        }

        return $groupedData;
    }

    public static function getDataByGroup()
    {
        $permissions = self::orderBy('name')->whereNotIn('name', [
                'permission create',
                'permission read',
                'permission update',
                'permission delete'
            ])->get();
        $groupedData = [];

        foreach($permissions as $permission) {
            $parts      = explode(' ', $permission->name);
            $group      = $parts[0];
            $data       = $parts[1];
            $groupIndex = array_search($group, array_column($groupedData, 'group'));

            if($groupIndex !== false) {
                $groupedData[$groupIndex]['data'][] = ['id' => $permission->id, 'name' => $data];
            }
            else {
                $groupedData[] = [
                    'group' => $group,
                    'data'  => [['id' => $permission->id, 'name' => $data]],
                ];
            }
        }

        return $groupedData;
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->isoFormat('D MMMM Y HH:mm');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->isoFormat('D MMMM Y HH:mm');
    }
}
