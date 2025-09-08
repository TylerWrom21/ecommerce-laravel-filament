<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->unique()->required()->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Hidden::make('slug'),
                // ->required()->unique('products', 'slug', ignoreRecord: true),
                Hidden::make('user_id')->default(auth()->id()),
                TextInput::make('price')->required()->prefix('USD')->numeric()->minValue(0)->maxValue(999999999999999)->inputMode('decimal'),
                Select::make('status')->required()->options(['available' => 'Available','notready' => 'Not Ready',])->native(false),
                Select::make('category_id')->label('Category')->required()->options(Category::all()->pluck('name', 'id'))->native(false),
                TextInput::make('quantity')->required()->numeric()->inputMode('decimal')->minValue(0)->maxValue(100000),
                TagsInput::make('tags')->required()->separator(',')->nestedRecursiveRules(['min:3','max:48',]),
                Textarea::make('description')->required()->columnSpan('full'),
                FileUpload::make('thumbnail')->required()->image()->directory('thumbnails'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('category.name')->searchable()->sortable(),
                TextColumn::make('status')->searchable()->sortable(),
                // TextColumn::make('rate')->searchable()->sortable(),
                TextColumn::make('quantity')->searchable(),
                TextColumn::make('tags')->searchable(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
