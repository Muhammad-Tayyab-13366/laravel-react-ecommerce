<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatusEnum;
use App\Enums\RolesEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Department;
use App\Models\Product;
use Dom\Text;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    TextInput::make('title')
                        ->live(onBlur: true)
                        ->required()
                        ->maxLength(2000)
                        ->afterStateUpdated(function ($operation, $state, callable $set) {
                            if (!filled($state)) {
                                return;
                            }
                            $slug = Str::slug($state);
                            $set('slug', $slug);
                        })
                        ,
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(2000),
                    Select::make('department_id')
                        ->relationship('department', 'name')
                        ->label('Department')
                        ->preload()
                        ->searchable()
                        ->required()
                        ->afterStateUpdated(function (callable $set) {
                            $set('category_id', null);
                        })
                        ,
                    Select::make('category_id')
                        ->relationship(
                            name: 'category',
                            titleAttribute: 'name',
                            modifyQueryUsing: function(Builder $query, callable $get){
                                $departmentId = $get('department_id');
                                if ($departmentId) {
                                    $query->where('department_id', $departmentId);
                                }
                                return $query;
                            }
                        )
                        ->label('Category')
                        ->searchable()
                        ->preload()
                        ->required(),

                ]),
                RichEditor::make('description')
                    ->columnSpan(2)
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'strike',
                        'bulletList',
                        'orderedList',
                        'link',
                        'codeBlock',
                        'quote',
                        'attachFiles',
                        'h1',
                        'h2',
                        'h3',
                        'underline',
                        'redo',
                        'undo',
                        'table',
                        'orderedList',
                        'bulletList',
                    ]),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                   
                    ->minValue(0),
                TextInput::make('quantity')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Select::make('status')
                    ->options(ProductStatusEnum::labels())
                    ->default(ProductStatusEnum::Draft->value)
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Product Title')->sortable()->searchable()->words(15),
                TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn ($state) => $state === ProductStatusEnum::Published->value ? 'success' : 'gray')
                ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('department.name')->label('Department')->sortable()->searchable(),
                TextColumn::make('category.name')->label('Category')->sortable()->searchable(),
                TextColumn::make('price')->label('Price')->money('usd', true)->sortable(),
                TextColumn::make('quantity')->label('Quantity')->sortable(),
                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                ->label('Status')
                ->options(ProductStatusEnum::labels()),
                SelectFilter::make('department_id')
                ->relationship('department', 'name')
                ->label('Department'),
                SelectFilter::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        
        $user = Filament::auth()->user();
        return $user && ($user->hasRole(RolesEnum::Vendor->value));
    }
}
