<?php

namespace App\Imports;

use App\Categories;
use App\Items;
use App\Restorant;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements ToModel, WithHeadingRow
{
    public function __construct(Restorant $restorant)
    {
        $this->restorant = $restorant;
        $this->lastCategoryName="";
        $this->lastCategoryID=0;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $category = Categories::where(['name' => $row['category'], 'restorant_id' => $this->restorant->id])->first();
        $CATID=null;
        if($category != null){
            $CATID= $category->id;
        }else{
            //Check last inssert category
            if($this->lastCategoryName==$row['category']){
                $CATID=$this->lastCategoryID;
            }
        }
        if ($CATID != null) {
            
            $item = Items::where(['name' => $row['name'], 'category_id' => $CATID])->first();
        
            if($item == null){       
                return new Items([
                    'name' => $row['name'],
                    'description' => $row['description']?$row['description']:"",
                    'price' => $row['price'],
                    'category_id' => $CATID,
                    'image' => $row['image'] ? $row['image'] : "",
                ]); 
            }else{
                //Update
                $item->price=$row['price'];
                $item->image =$row['image'] ? $row['image'] : "";
                $item->category_id =$CATID;
                $item->description =$row['description']?$row['description']:"";
            }
        } else {
            $newCat=new Categories([
                'name'=>$row['category'],
                'restorant_id'=>$this->restorant->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $newCat->save();
            $categoryID=$newCat->id;
            $this->lastCategoryID=$categoryID;
            $this->lastCategoryName=$row['category'];


            

            return new Items([
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => $row['price'],
                'category_id' => $categoryID,
                'image' => $row['image'] ? $row['image'] : "",
            ]);
        }
    }
}
