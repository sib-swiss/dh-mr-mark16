<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use App\Models\ManuscriptContentImage;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ManuscriptPartnersRelationManager extends RelationManager
{
    protected static string $relationship = 'partners';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('image'),
                Forms\Components\TextInput::make('url'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('images')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentImage $record): string {
                        $html = '<div class="flex gap-2">';
                        if ($record) {
                            $imageUrl = "/iiif/{$record->identifier}/full/65,/0/default.jpg";
                            $html .= '<img src="'.url($imageUrl).'" alt="'.$record->name.'" width="100" height="100">';
                        }
                        $html .= '</div>';

                        return $html;
                    }),

                Tables\Columns\TextColumn::make('url'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->filters([
            ]);
    }
}
