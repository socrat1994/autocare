<?php

namespace App\HelperClasses;


class ToArray
{
  public function to_array($objects, $attribute)// convert colomn retrived from database as objects to array
 {
   $i = 0;
   foreach($objects as $object)
   {
     $object_arr[$i] =  $object->$attribute;
     $i++;
   }
   if(isset($object_arr))
     {return $object_arr;}
 }
}
