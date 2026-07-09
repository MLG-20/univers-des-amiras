<?php

namespace App\Filament\Resources\Catalogue;

use App\Filament\Resources\Catalogue\ProductResource\Pages;
use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

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

                Select::make('category_id')
                    ->label('Catégorie')
                    ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('price')
                    ->label('Prix (FCFA)')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Textarea::make('description')
                    ->rows(4)
                    ->maxLength(5000)
                    ->columnSpanFull(),

                Repeater::make('images')
                    ->relationship('images')
                    ->label('Images')
                    ->schema([
                        // ->image() valide le vrai type MIME côté serveur (pas juste
                        // l'extension), et Filament renomme le fichier au stockage —
                        // c'est ce qui rejette un .php déguisé en .jpg.
                        FileUpload::make('path')
                            ->label('Image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->maxSize(15360)
                            ->required(),
                        Toggle::make('is_primary')->label('Principale'),
                        TextInput::make('position')->numeric()->default(0),
                    ])
                    ->columns(3)
                    ->defaultItems(0)
                    ->columnSpanFull(),

                Repeater::make('variants')
                    ->relationship('variants')
                    ->label('Variantes')
                    ->schema([
                        TextInput::make('sku')->label('SKU')->required()->maxLength(255),
                        KeyValue::make('attributes')->label('Attributs (ex: taille, couleur)'),
                        TextInput::make('price_override')->label('Prix spécifique (FCFA)')->numeric(),
                        TextInput::make('stock')->numeric()->default(0)->required(),
                        Toggle::make('is_active')->label('Active')->default(true),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images.path')
                    ->label('Image')
                    ->disk('public')
                    ->limit(1),
                TextColumn::make('name')->label('Nom')->searchable(),
                TextColumn::make('category.name')->label('Catégorie')->sortable(),
                TextColumn::make('price')->label('Prix')->money('XOF')->sortable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id')),
                TernaryFilter::make('is_active')->label('Active'),
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            // Affiche aussi les produits soft-supprimés (avec une action Restaurer)
            // pour que l'admin puisse récupérer un produit plutôt que de le voir
            // simplement disparaître.
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
