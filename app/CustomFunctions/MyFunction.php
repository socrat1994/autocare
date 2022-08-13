<?php

function hello(){return "dd";}
     function to_array($objects, $attribute)// convert colomn retrived from database as objects to array
    {
      $i = 0;
      foreach($objects as $object)
      {
        $branch_arr[$i] =  $object->$attribute;
        $i++;
      }
        return $branch_arr;
    }
