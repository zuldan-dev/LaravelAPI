<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * This is model class for table 'tasks'
 *
 * The followings are the available columns in table 'tasks'
 * @property integer $id
 * @property string $status
 * @property integer $priority
 * @property string $title
 * @property string $description
 * @property integer $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $completed_at
 * @property User $user
 * @property Task $children
 */
class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'priority',
        'title',
        'description',
    ];

    /**
     * Attributes for filter
     *
     * @var array<string>
     */
    public array $filterable = [
        'status',
        'priority',
        'title',
        'description',
    ];

    /**
     * Attributes for sorting
     *
     * @var array<string>
     */
    public array $sortable = [
        'id',
        'created_at',
        'completed_at',
        'priority',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function parent(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_task', 'child_id', 'parent_id');
    }

    public function child(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_task', 'parent_id', 'child_id');
    }

    public function children(): BelongsToMany
    {
        return $this->child()->with('children');
    }
}
