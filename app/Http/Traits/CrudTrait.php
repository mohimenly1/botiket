<?php
namespace App\Http\Traits;

trait CrudTrait
{
    public function indexTrait($model)
    {
        return $model->orderBy('id', 'desc')->get();
    }

    public function indexGetTrait($model, $get)
    {
        return $model->orderBy('id', 'desc')->get($get);
    }
    public function indexDeletedGetTrait($model, $get)
    {
        return $model->orderBy('id', 'desc')->onlyTrashed()->paginate(10,$get);
    }
    public function indexDeletedTrait($model)
    {
        return $model->orderBy('id', 'desc')->onlyTrashed()->get();
    }

    public function indexWithRelationTrait($model, $relation)
    {
        return $model->orderBy('id', 'desc')->with($relation)->get();
    }

    public function indexWithRelationGetTrait($model, $relation, $get)
    {
        return $model->orderBy('id', 'desc')->with($relation)->get($get);
    }
    public function indexDeletedWithRelationGetTrait($model, $relation, $get)
    {
        return $model->orderBy('id', 'desc')->with($relation)->onlyTrashed()->get($get);
    }
    public function indexWhithCondetionTrait($model, $condetion)
    {
        return $model->orderBy('id', 'desc')->where($condetion)->get();
    }

    public function indexWhithCondetionAndRelationTrait($model, $condetion, $relation)
    {
        return $model->with($relation)->where($condetion)->orderBy('id', 'desc')->get();
    }
    public function indexDeletedWhithCondetionAndRelationTrait($model, $condetion, $relation)
    {
        return $model->with($relation)->where($condetion)->orderBy('id', 'desc')->onlyTrashed()->get();
    }
    public function storeTrait($model, $data)
    {
        return $model::create($data->toArray());
    }
    public function showTrait($model, $id)
    {
        return $model->find($id);
    }
    public function showWithRelationTrait($model, $id, $relation)
    {
        return $model->where('id', $id)->with($relation)->first();
        
    }

    public function updateTrait($model, $id, $data)
    {
        $model::where('id', $id)->update($data);
        return $model::findOrFail($id);
    }

    public function destroyTrait($model, $id)
    {
        return $model->where('id', $id)->delete();
    }
    public function restoreTrait($model, $id)
    {
        return $model->where('id', $id)->restore();
    }
}
