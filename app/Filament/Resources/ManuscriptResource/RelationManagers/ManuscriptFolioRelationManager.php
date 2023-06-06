<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use App\Models\ManuscriptContentMeta;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

                SpatieMediaLibraryFileUpload::make('image'),

                Forms\Components\TextInput::make('imageContent.content.copyright'),

                Forms\Components\TextInput::make('imageContent.content.fontSize'),
                // copyright
                // font size
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('images')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $html = '<div class="flex gap-2">';
                        if ($record->contentImage) {
                            $imageUrl = "/iiif/{$record->contentImage->identifier}/full/65,/0/default.jpg";
                            $html .= '<img src="'.url($imageUrl).'" alt="'.$record->contentImage->name.'" width="100" height="100">';
                        }
                        $html .= '</div>';

                        return $html;
                    }),

                Tables\Columns\TextColumn::make('copyright Text')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        return isset($record->contentImage->content['copyright']) ? $record->contentImage->content['copyright'] : '';

                    }),

                Tables\Columns\TextColumn::make('copyright fontSize')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        return isset($record->contentImage->content['fontsize']) ? $record->contentImage->content['fontsize'] : '';

                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->filters([
            ]);
    }
}
