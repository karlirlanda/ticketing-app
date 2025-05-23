<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['comments', 'attachments']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255)
                            ->disabled(fn (string $operation, $record) => $operation === 'edit' && $record->created_by != auth()->id())
                            ->columnSpan(2),
                        Forms\Components\Select::make('priority')
                            ->label('Priority')
                            ->options([
                                1 => 'Low',
                                2 => 'Medium',
                                3 => 'High',
                            ])
                            ->disabled(fn (string $operation, $record) => $operation === 'edit' && $record->created_by != auth()->id())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                1 => 'Pending',
                                2 => 'In Progress',
                                3 => 'Completed',
                            ])
                            ->hidden(fn (string $operation) => $operation === 'create')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->disabled(fn (string $operation, $record) => $operation === 'edit' && $record->created_by != auth()->id())
                            ->rows(4),
                    ])
                    ->columns(4),
                Section::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Repeater::make('comments')
                                    ->relationship('comments')
                                    ->schema([
                                        Textarea::make('comment')
                                            ->label('Comment')
                                            ->required(),
                                    ])
                                    ->label('Comments')
                                    ->afterStateHydrated(function ($component, $state) {
                                        // Optional: Prevent editing user_id from form
                                    })
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                                        $data['user_id'] = auth()->id(); // Set the user who added the comment

                                        return $data;
                                    })
                                    ->defaultItems(0),
                                // ->collapsible(),
                            ])
                            ->columnSpan(1),
                        Section::make()
                            ->schema([
                                Repeater::make('attachments')
                                    ->relationship('attachments')
                                    ->schema([
                                        FileUpload::make('file')
                                            ->label('Attachment')
                                            ->disk('public') // or whatever disk you use
                                            ->directory('ticket-attachments')
                                            ->required(),
                                    ])
                                    ->label('Attachments')
                                    ->defaultItems(0)
                                    ->collapsible(),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'Low',
                        2 => 'Medium',
                        3 => 'High',
                        default => 'Unknown',
                    })
                    ->color(fn ($state) => match ($state) {
                        1 => 'success',
                        2 => 'warning',
                        3 => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'Pending',
                        2 => 'In Progress',
                        3 => 'Completed',
                        default => 'Unknown',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.username')
                    ->label('Updated By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comments')
                    ->counts('comments')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->label('Attachments')
                    ->counts('attachments')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
