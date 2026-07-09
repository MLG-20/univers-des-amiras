<?php

namespace App\Filament\Resources\Content;

use App\Filament\Resources\Content\HeroSlideResource\Pages;
use App\Models\Content\HeroSlide;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'Hero (accueil)';

    protected static ?string $modelLabel = 'slide du hero';

    protected static ?string $pluralModelLabel = 'slides du hero';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image_path')
                    ->label('Image de fond')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('hero')
                    ->visibility('public')
                    ->maxSize(15360)
                    ->helperText('Laisser vide pour conserver le fond en dégradé de la charte, en attendant une photo. 15 Mo maximum.'),

                Select::make('image_position')
                    ->label("Cadrage de l'image")
                    ->options([
                        'left' => 'Sujet à gauche de la photo',
                        'center' => 'Sujet centré',
                        'right' => 'Sujet à droite de la photo',
                    ])
                    ->default('center')
                    ->required()
                    ->helperText('Le bandeau est large : ce réglage évite de couper le sujet si la photo est un portrait. À ajuster selon le résultat affiché sur le site.'),

                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('subtitle')
                    ->label('Sous-titre')
                    ->maxLength(255),

                TextInput::make('cta_label')
                    ->label('Texte du bouton')
                    ->maxLength(255),

                TextInput::make('cta_url')
                    ->label('Lien du bouton')
                    ->maxLength(255)
                    ->helperText('Ex. : /catalogue ou /categories/voiles-hijabs'),

                TextInput::make('position')
                    ->label("Ordre d'affichage")
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->columns([
                ImageColumn::make('image_path')->label('Image')->disk('public'),
                TextColumn::make('title')->label('Titre')->searchable(),
                TextColumn::make('position')->label('Ordre')->sortable(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHeroSlides::route('/'),
        ];
    }
}
