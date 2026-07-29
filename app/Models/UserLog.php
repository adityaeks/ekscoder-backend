<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'model_id',
        'description',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a user log entry easily anywhere.
     */
    public static function log(string $action, string $module, ?string $description = null, ?Model $model = null, ?array $changes = null): self
    {
        $user = Auth::user();

        return static::create([
            'user_id'     => $user ? $user->id : null,
            'user_name'   => $user ? $user->name : 'System / Guest',
            'action'      => strtolower($action),
            'module'      => $module,
            'model_id'    => $model ? $model->getKey() : null,
            'description' => $description,
            'changes'     => $changes,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}
