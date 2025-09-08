<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected function getTableQuery(): Builder
    {
        return Review::query()
            ->select('product_id', DB::raw('COUNT(*) as totalUser'))
            ->groupBy('product_id')
            ->having('totalUser', '>', 1);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->unique()->required()->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Hidden::make('slug'),
                Hidden::make('user_id')->default(auth()->id()),
                Select::make('product_id')->label('Product')->required()->options(Product::all()->pluck('name', 'id'))->native(false),
                Textarea::make('description')->required()->columnSpan('full'),
                TextInput::make('rate')->required()->numeric()->inputMode('decimal')->minValue(0)->maxValue(5),
                // TextInput::make('rate')->required()->numeric()->inputMode('decimal')->minValue(0)->maxValue(5)->afterStateUpdated(function (Set $set, Get $get) {
                //     $allProducts = Review::all()->pluck('product_id', 'rate')->toArray();
                //     $myCollection = new Collection($allProducts);
                //     $duplicates = $myCollection->duplicates();
                //     $selectedProducts = collect($get('product_id'))->pluck('product_id')->toArray();
                //     // $availableSections = array_diff($allProducts, $selectedProducts);
                //     dd($duplicates);
                //     $totalRate = $get('rate'); 
                //     // $averageRate;

                //     // Update a related record in another table
                //     if ($averageRate) {
                //         Product::where('rate', $averageRate)->update([
                //             'related_column' => 'new_value_based_on_main_field',
                //         ]);
                //         // You can also use $set to update other fields in the current form
                //         $set('another_form_field', 'value_derived_from_main_field');
                //     }
                // }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->sortable()->searchable(),
                TextColumn::make('product.name')->sortable()->searchable(),
                TextColumn::make('rate')->sortable()->searchable(),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
