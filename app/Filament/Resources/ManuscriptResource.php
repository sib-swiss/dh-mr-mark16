<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManuscriptResource\Pages;
use App\Models\Manuscript;
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
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('temporal'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\IconColumn::make('published')->boolean(),
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
            //
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
