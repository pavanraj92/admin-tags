<?php

namespace admin\tags\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTag extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'course_tag';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'course_id',
        'tag_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'course_id' => 'integer',
        'tag_id' => 'integer',
    ];

    /**
     * Get the course that belongs to this relationship.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the tag that belongs to this relationship.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo('admin\tags\Models\Tag');
    }

    /**
     * Scope a query to filter by course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to filter by tag.
     */
    public function scopeForTag($query, $tagId)
    {
        return $query->where('tag_id', $tagId);
    }
}
