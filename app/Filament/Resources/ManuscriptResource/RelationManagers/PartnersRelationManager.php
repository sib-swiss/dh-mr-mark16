<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PartnersRelationManager extends RelationManager
{
    protected static string $relationship = 'partners';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {

        return $form->schema([
            Forms\Components\TextInput::make('url'),
            FileUpload::make('image')
                ->image()
                ->label('Replace parter image'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('images')
                    ->html()
                    ->getStateUsing(function (Media $record): string {
                        $html = '';
                        $imageUrl = "/iiif/{$record->id}/full/150,/0/default.jpg";
                        $imageUrlFull = "/iiif/{$record->id}/full/full,/0/default.jpg";
                        $html .= '<a href="'.url($imageUrlFull).'" target="_blank">
                                <img src="'.url($imageUrl).'" alt="'.$record->name.'" width="150" >
                            </a>';

                        return $html;
                    }),

                Tables\Columns\TextColumn::make('url')
                    ->html()
                    ->getStateUsing(function (Media $record): string {

                        return $record->getCustomProperty('url')
                        ? $record->getCustomProperty('url')
                        : '';

                    }),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $manuscript = $livewire->getOwnerRecord();

                        $pathToFile = storage_path("/app/public/{$data['image']}");
                        $newRecord = $manuscript->addMedia($pathToFile)
                            ->withCustomProperties(['url' => $data['url']])
                            ->toMediaCollection('partners');

                        return $newRecord;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        // dd($data);
                        $mediaItem = Media::find($data['id']);
                        $data['url'] = $mediaItem->getCustomProperty('url') ?: '';
                        $data['fullUrl'] = $mediaItem->getFullUrl();

                        return $data;
                    })->using(function (Media $record, array $data): Media {

                        if ($data['image']) {
                            $pathToFile = storage_path("/app/public/{$data['image']}");
                            $newRecord = $record->model->addMedia($pathToFile)
                                ->withCustomProperties(['url' => $data['url']])
                                ->toMediaCollection('partners');

                            $record->delete();

                            return $newRecord;
                        }

                        $record->setCustomProperty('url', $data['url']);
                        $record->save();

                        return $record;

                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
            ]);
    }
}
