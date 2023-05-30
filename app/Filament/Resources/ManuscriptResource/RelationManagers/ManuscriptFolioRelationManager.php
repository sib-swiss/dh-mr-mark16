<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ManuscriptFolioRelationManager extends RelationManager
{
    protected static string $relationship = 'folios';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // image upload
                // copyright
                // font size
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                // number of folio image
                // copyright?
            ])
            ->filters([
            ]);
    }
}
