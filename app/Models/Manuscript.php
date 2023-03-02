<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manuscript extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContent>
     */
    public function contents()
    {
        return $this->hasMany(ManuscriptContent::class);
    }

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContent>
     */
    public function folios()
    {
        return $this->contents()->where('extension', 'xml')->orderBy('name');
    }

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContent>
     */
    public function partners()
    {
        return $this->contents()->where('name', 'like', '%partner%');
    }

    public function getDisplayname(): string
    {
        return $this->getMeta('dcterm-bibliographicCitation');
    }

    public function getMeta(string $key): string
    {
        return $key;
    }

    public function getLangExtended(): string
    {
        return 'todo';
    }

    /**
     * @return array<string>
     */
    public function getMetas(string $key): array
    {
        return [];
    }
}
