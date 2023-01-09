<?php

namespace App\HelperClasses;

//use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Validator;

class Iteration
{
  public function Iteration($common , $rules , $class , $method)
  {
    try
    {
      $status = [];
      $i = 0;
      a:foreach(array_slice($common['datas'], $i, count($common['datas'])-$i) as $data)
      {
        $validated = Validator::make($data, $rules);
        if ($validated->fails()) {
          $status[$i] = $validated->errors();
          $i++;
          $error = true;
          continue;
        }
        $status[$i] = call_user_func(array($class, $method),$data);
        $i++;
      }
    }catch (\Exception $e) {
      $status[$i] = $e->getMessage();
      $i++;
      goto a;
    }
    return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
  }

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

  public function delete($array, $del_arr)
  {
    $after_del_arr = [];
    $sub = [];
    foreach($array as $key =>$value)
    {
      foreach($value as $subvalue)
      {
        if(array_search($subvalue , $del_arr, true) === false)
        {
          array_push($sub, $subvalue);
        }
      }
      array_push($after_del_arr, [$key =>$sub]);
      $sub = [];
    }
    return $after_del_arr;
  }
}
