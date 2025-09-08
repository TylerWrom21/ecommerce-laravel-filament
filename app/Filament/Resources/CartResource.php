<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Filament\Resources\CartResource\RelationManagers;
use App\Models\Cart;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(auth()->id()),
                // Select::make('product_id')->label('Product')->required()->options(Product::all()->pluck('name', 'id'))->native(false)->live()->afterStateUpdated(fn (Forms\Set $set) => $set('quantity', 1)),
                Select::make('product_id')
                ->label('Product')
                ->required()
                ->options(Product::all()->pluck('name', 'id'))
                ->native(false)
                ->live()
                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                    $set('quantity', 1); // reset quantity to 1 when product changes

                    $product = Product::find($state);
                    $quantity = 1; // default quantity

                    if ($product) {
                        $set('price', $product->price * $quantity);
                    } else {
                        $set('price', null);
                    }
                }),
                TextInput::make('quantity')
                ->numeric()
                ->minValue(1)
                ->maxValue(fn (Forms\Get $get) => Product::find($get('product_id'))?->quantity ?? null)
                ->required()
                ->disabled(fn (Forms\Get $get): bool => ! filled($get('product_id')))
                ->live(debounce: 200)
                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                    $product = Product::find($get('product_id'));

                    if ($product) {
                        $set('price', $product->price * $state);
                    } else {
                        $set('price', null);
                    }
                }),
                // TextInput::make('quantity')->numeric()->minValue(1)->maxValue(fn (Forms\Get $get) => Product::find($get('product_id'))?->quantity ?? null)->required()->disabled(fn(Forms\Get $get) : bool => ! filled($get('product_id')))->live(),
                TextInput::make('price')->numeric()->disabled()->required()->prefix('USD'),
                //     ->formatStateUsing(function (Forms\Get $get) {
                //     $product = Product::find($get('product_id'));
                //     $quantity = $get('quantity') ?? 0;
                //     // dd($product);
                //     dd($get('product_id'), $get('quantity'), Product::find($get('product_id')));
                //     if ($product) {
                //         return $product->price * $quantity;
                //     }

                //     return null;
                // }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->sortable()->searchable(),
                TextColumn::make('product.name')->sortable()->searchable(),
                TextColumn::make('quantity')->sortable()->searchable(),
                TextColumn::make('price')->sortable()->searchable(),
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
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }
}
