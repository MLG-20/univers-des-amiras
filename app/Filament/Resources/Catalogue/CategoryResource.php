<?php

namespace App\Filament\Resources\Catalogue;

use App\Filament\Resources\Catalogue\CategoryResource\Pages;
use App\Models\Catalogue\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Catalogue';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('parent_id')
                    ->label('Catégorie parente')
                    ->relationship('parent', 'name', fn ($query, $record) => $record
                        ? $query->whereKeyNot($record->id)
                        : $query)
                    ->searchable()
                    ->preload(),

                Textarea::make('description')
                    ->rows(3)
                    ->maxLength(2000)
                    ->columnSpanFull(),

                TextInput::make('position')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable(),
                TextColumn::make('parent.name')->label('Parent')->placeholder('—'),
                TextColumn::make('position')->label('Position')->sortable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('position');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
