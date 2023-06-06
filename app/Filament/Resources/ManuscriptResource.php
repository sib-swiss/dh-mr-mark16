<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManuscriptResource\Pages;
use App\Filament\Resources\ManuscriptResource\RelationManagers\ManuscriptFolioRelationManager;
use App\Models\Manuscript;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ManuscriptResource extends Resource
{
    protected static ?string $model = Manuscript::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Toggle::make('published'),
            SpatieMediaLibraryFileUpload::make('partners')
                ->multiple()
                ->collection('partners')
                ->enableReordering(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('temporal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('folios_count')
                    ->label('Folios')
                    ->counts('folios')
                    ->sortable(),

                Tables\Columns\TextColumn::make('images')
                    ->html()
                    ->getStateUsing(function (Manuscript $record): string {
                        $html = '<div class="flex justify-start gap-2">';
                        foreach ($record->folios as $folio) {
                            $mediaItem = $folio->getFirstMedia();
                            if ($mediaItem) {
                                $imageUrl = "/iiif/{$mediaItem->id}/full/65,/0/default.jpg";
                                $imageUrlFull = "/iiif/{$mediaItem->id}/full/max,/0/default.jpg";
                                $html .= '<div>
                                    <a href="'.url($imageUrlFull).'" target="_blank">
                                        <img src="'.url($imageUrl).'" alt="'.$folio->name.'" class="max-w-none">
                                    </a>
                                </div>';
                            }

                        }
                        $html .= '</div>';

                        return $html;
                    }),
                Tables\Columns\TextColumn::make('partners')
                    ->html()
                    ->getStateUsing(function (Manuscript $record): string {
                        $html = '<div class="flex gap-2">';
                        foreach ($record->getMedia('partners') as $partner) {
                            $imageUrl = "/iiif/{$partner->id}/full/,72/0/default.jpg";
                            $html .= '<a href="'.$partner->url.'" target="_blank">
                                <img src="'.url($imageUrl).'" alt="'.$partner->name.'" class="max-w-none">
                            </a>';
                        }
                        $html .= '</div>';

                        return $html;
                    }),
                Tables\Columns\IconColumn::make('published')->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Nakala')
                    ->label('')
                    ->tooltip('Sync from Nakala')
                    ->action(function (Manuscript $record) {
                        $syncFromNakala = $record->syncFromNakala();
                        if (isset($syncFromNakala['version'])) {
                            Notification::make()
                                ->title('Updated manuscript '.$record->getDisplayname().' to revision '.$syncFromNakala['version'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('ERROR while try to syunc manuscript '.$record->getDisplayname())
                                ->danger()
                                ->send();
                        }

                    })
                    // ->requiresConfirmation()
                    ->icon('heroicon-o-refresh')
                    ->color('success'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ManuscriptFolioRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManuscripts::route('/'),
            'create' => Pages\CreateManuscript::route('/create'),
            'edit' => Pages\EditManuscript::route('/{record}/edit'),
        ];
    }
}
