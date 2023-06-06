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

        return $form->schema([
            SpatieMediaLibraryFileUpload::make('image'),
            Forms\Components\TextInput::make('copyright'),
            Forms\Components\TextInput::make('fontsize'),
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
                        $html = '';
                        $mediaItem = $record->getFirstMedia();
                        if ($mediaItem) {
                            $imageUrl = "/iiif/{$mediaItem->id}/full/65,/0/default.jpg";
                            $imageUrlFull = "/iiif/{$mediaItem->id}/full/full,/0/default.jpg";
                            $html .= '<a href="'.url($imageUrlFull).'" target="_blank">
                                <img src="'.url($imageUrl).'" alt="'.$record->name.'" width="100" height="100">
                            </a>';
                        }

                        return $html;
                    }),

                Tables\Columns\TextColumn::make('copyright Text')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        return $record->getFirstMedia()->getCustomProperty('copyright') ?: '';

                    }),

                Tables\Columns\TextColumn::make('copyright fontSize')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        return $record->getFirstMedia()->getCustomProperty('fontsize') ?: '';

                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $media = ManuscriptContentMeta::find($data['id'])->getFirstMedia();
                        $data['copyright'] = $media->getCustomProperty('copyright') ?: '';
                        $data['fontsize'] = $media->getCustomProperty('fontsize') ?: '';

                        return $data;
                    })->using(function (ManuscriptContentMeta $record, array $data): ManuscriptContentMeta {
                        $mediaItem = $record->getFirstMedia();
                        $mediaItem->setCustomProperty('fontsize', $data['fontsize']);
                        $mediaItem->setCustomProperty('copyright', $data['copyright']);
                        $mediaItem->save();

                        return $record;
                    }),
            ])
            ->filters([
            ]);
    }
}
