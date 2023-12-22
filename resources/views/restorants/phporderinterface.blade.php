<script>
   
    <?php
     
   
    $items=[];
    $categories = [];
    foreach ($restorant->categories as $key => $category) {

        array_push($categories, clean(str_replace(' ', '', strtolower($category->name)).strval($key)));

        foreach ($category->items as $key => $item) {

            $formatedExtras=$item->extras;

            foreach ($formatedExtras as &$element) {
                $element->priceFormated=@money($element->price, config('settings.cashier_currency'),config('settings.do_convertion'))."";
            }

            //Now add the variants and optins to the item data
            $itemOptions=$item->options;

            $theArray=[
                'name'=>$item->name,
                'id'=>$item->id,
                'priceNotFormated'=>$item->price,
                'price'=>@money($item->price, config('settings.cashier_currency'),config('settings.do_convertion'))."",
                'image'=>$item->logom,
                'extras'=>$formatedExtras,
                'options'=>$item->options,
                'variants'=>$item->variants,
                'has_variants'=>$item->has_variants==1&&$item->options->count()>0,
                'description'=>$item->description
            ];
            echo "items[".$item->id."]=".json_encode($theArray).";";
        }
    }
    ?>
    var categories = <?php echo json_encode($categories); ?>;
</script>  