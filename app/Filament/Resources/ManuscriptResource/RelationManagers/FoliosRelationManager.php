<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use App\Models\ManuscriptContentMeta;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;

class FoliosRelationManager extends RelationManager
{
    protected static string $relationship = 'folios';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {

        return $form->schema([
            SpatieMediaLibraryFileUpload::make('image'),
            Forms\Components\Textarea::make('copyright'),
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
                    ->wrap()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $mediaItem = $record->getFirstMedia();

                        return $mediaItem && $mediaItem->getCustomProperty('copyright')
                        ? $mediaItem->getCustomProperty('copyright')
                        : '';

                    }),

                Tables\Columns\TextColumn::make('copyright fontSize')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $mediaItem = $record->getFirstMedia();

                        return $mediaItem && $mediaItem->getCustomProperty('fontsize')
                            ? $mediaItem->getCustomProperty('fontsize')
                            : '';

                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $mediaItem = ManuscriptContentMeta::find($data['id'])->getFirstMedia();
                        if ($mediaItem) {
                            $data['copyright'] = $mediaItem->getCustomProperty('copyright') ?: '';
                            $data['fontsize'] = $mediaItem->getCustomProperty('fontsize') ?: '';
                        }

                        return $data;
                    })->using(function (ManuscriptContentMeta $record, array $data): ManuscriptContentMeta {
                        $mediaItem = $record->getFirstMedia();
                        if ($mediaItem) {
                            $mediaItem->setCustomProperty('fontsize', $data['fontsize']);
                            $mediaItem->setCustomProperty('copyright', $data['copyright']);
                            $mediaItem->save();

                            // delete cached image to regenerate with new copyright/size
                            $filePath = 'images/'.$mediaItem->id.'_'.$mediaItem->file_name;
                            $storage = Storage::disk('public');
                            if ($storage->exists($filePath)) {
                                return $storage->delete($filePath);
                            }
                        }

                        return $record;

                    }),
            ])
            ->filters([
            ]);
    }
}
