<?php

namespace App\Models;

use App\Traits\Copyable;
use App\Traits\Loggable;
use App\Traits\Manageable;
use App\Traits\Searchable;
use App\Traits\Trashable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use Copyable, Loggable, Manageable, Searchable, Trashable;

    protected $fillable = [
        'name',
        'description',
        'content',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if ($project->content) {
                $project->content = self::sanitizeHtml($project->content);
            }
        });
    }

    public static function sanitizeHtml(string $html): string
    {
        $allowed = '<h1><h2><h3><h4><h5><h6><p><br><strong><em><u><del><a><ul><ol><li><blockquote><pre><code><img><div><span>';

        $clean = strip_tags($html, $allowed);

        // Strip event handler attributes (on*) and javascript: URLs
        $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean);
        $clean = preg_replace('/\s+on\w+\s*=\s*\S+/i', '', $clean);
        $clean = preg_replace('/href\s*=\s*["\']?\s*javascript\s*:[^"\'>\s]*/i', 'href="#"', $clean);
        $clean = preg_replace('/src\s*=\s*["\']?\s*javascript\s*:[^"\'>\s]*/i', 'src=""', $clean);

        return $clean;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
