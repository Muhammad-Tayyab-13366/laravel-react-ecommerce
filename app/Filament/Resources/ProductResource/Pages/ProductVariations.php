<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\ProductVariationTypeEnum;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Print_;

class ProductVariations extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $title = 'Variations';


    public function form(Form $form): Form
    {
        $types = $this->record->variationTypes;
        $fields = [];
        foreach ($types as $i => $type) {
            // $fields[] = TextInput::make('variation_type_'.$type->id.'.id')->hidden()->dehydrated(true);
            $fields[] = Hidden::make('variation_type_'.$type->id.'.id')->dehydrated(true);;
            $fields[] = TextInput::make('variation_type_'.($type->id). '.name')->label($type->name);
        }

        // foreach ($types as $type) {
        //     $fields[] = TextInput::make("variation_type_{$type->id}.id")
        //         ->hidden()
        //         ->dehydrated(true);
        
        //     $fields[] = TextInput::make("variation_type_{$type->id}.name")
        //         ->label($type->name)
        //         ->dehydrated(true);
        // }

        return $form
        ->schema([
            Repeater::make('variations')
            ->collapsible()
            ->addable(false)
            ->defaultItems(1)
            ->columns(2)
            ->columnSpan(2)
            ->schema([
                Section::make()
                    ->schema($fields)
                    ->columns(3),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric(),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()

            ])
        ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $variations = $this->record->variations; // already a collection
        $variations = $variations->toArray();
        $data['variations'] = $this->mergeCartesianWithExisting($this->record->variationTypes, $variations);
        return $data;
    }

    private function mergeCartesianWithExisting($variationTypes, $existingData)
    {
        $defaultQuantity = $this->record->quantity;
        $defaultPrice = $this->record->price;

        $cartesianProduct = $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);
        
        $mergeResult = [];

        foreach ($cartesianProduct as $key => $product) {
           $optionIds = collect($product)
           ->filter(fn($value, $key) => str_starts_with($key, 'variation_type'))
           ->map(fn($option) => $option['id'])
           ->values()
           ->toArray();

            $match = array_filter($existingData, function ($existingOption) use  ($optionIds){
                return $existingOption['variation_type_option_ids'] == $optionIds;
            });

            if(!empty($match)){
                $existingEntry = reset($match);
                $product['quantity'] = $existingEntry['quantity'];
                $product['price'] = $existingEntry['price'];
            }else{
                $product['quantity'] = $defaultQuantity;
                $product['price'] = $defaultPrice;
            }

            $mergeResult[] = $product;
        }

       
        return $mergeResult;

    }

    private function cartesianProduct($variationTypes, $defaultQuantity = null, $defaultPrice = null)
    {
        $result = [[]];
        foreach($variationTypes as $index => $variationType){
            $temp = [];
            foreach ($variationType->options as $option) {
                foreach($result as $combination){
                    $newCombination = $combination + [
                        "variation_type_". ($variationType->id) => [
                            "id" => $option->id,
                            'name' => $option->name, 
                            'label' => $variationType->name
                        ]
                    ];
                    $temp[] = $newCombination;
                }
            }

            $result = $temp;

        }

        foreach ($result as &$combination) {
            if(count($combination) === count($variationTypes)){
                $combination['quantity'] = $defaultQuantity;
                $combination['price'] = $defaultPrice;
            }
        }

        return $result;
    }

    public function mutateFormDataBeforeSave(array $data): array
    {

        $formattedData = [];
        foreach($data['variations'] as $option){
            $variationTypeOptionIds = [];
            foreach($this->record->variationTypes as $i => $variationType){
                $variationTypeOptionIds[] = $option['variation_type_'.$variationType->id]['id'];
            }

            $quantity = $option['quantity'];
            $price = $option['price'];

            $formattedData[] = [
                'variation_type_option_ids' =>  $variationTypeOptionIds,
                'quantity' => $quantity,
                'price' => $price
            ];
        }
        $data['variations']  = $formattedData;
       
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $variations = $data['variations'];
        unset($data['variations']);
        $record->update($data);
        $record->variations()->delete();
        $record->variations()->createMany($variations);
        return $record;
    }
}
