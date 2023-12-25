<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
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
 * @property Task $parent
 * @property Task $child
 * @property Task $children
 */
class Task extends Model
{
    use HasFactory;

    public const TASK_DELETE_MESSAGE = 'Task deleted successfully.';
    public const TASK_DELETE_ERROR = 'Task cant be deleted.';

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
        'user_id',
        'completed_at',
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

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'status' => 'required|in:' . implode(',', array_column(TaskStatusEnum::cases(), 'value')),
            'priority' => 'required|in:' . implode(',', range(1, 5)),
            'title' => 'required|string|max:255',
            'description' => 'string|max:512',
        ];
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function parent(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_task', 'child_id', 'parent_id');
    }

    /**
     * @return BelongsToMany
     */
    public function child(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_task', 'parent_id', 'child_id');
    }

    /**
     * Gets children recursively
     * @return BelongsToMany
     */
    public function children(): BelongsToMany
    {
        return $this->child()->with('children');
    }
}
