<?php

namespace App\Filament\Resources\ManuscriptResource\Pages;

use App\Filament\Resources\ManuscriptResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManuscript extends EditRecord
{
    protected static string $resource = ManuscriptResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
