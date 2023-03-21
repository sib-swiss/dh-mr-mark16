<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManuscriptContent extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Manuscript, \App\Models\ManuscriptContent>
     */
    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function pageNumber(): Attribute
    {
        $pagNumber = 0;
        foreach ($this->manuscript->folios as $p => $folio) {
            if ($folio->name === $this->name) {
                $pagNumber = $p + 1;
            }
        }

        return Attribute::make(
            get: fn () => $pagNumber,
        );
    }

    /**
     * return Folio name
     *
     * @return string
     */
    public function getFolioName()
    {
        $name = str_replace('_Metadata', '', $this->name);
        $parts1 = explode('/', $name);
        if (count($parts1) == 1) {
            $parts2 = explode('.', $parts1[0]);

            return  substr($name, 0, -strlen(end($parts2)) - 1);
        }

        //  GA304_240r/GA304_240r_ENG.html
        $parts2 = explode('_', $parts1[1]);
        $parts3 = explode('_', $parts2[1]);
        $parts4 = explode('.', $parts3[0]);

        return $parts2[0].'_'.$parts4[0];
    }
}
