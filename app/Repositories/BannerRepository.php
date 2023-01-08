<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Banner;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;

class BannerRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Banner
     */
    protected $banner;

    /**
     * UserRepository constructor.
     *
     * @param Banner $banner
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all banners with Role.
     *
     * @return Banner $banner
     */
    public function index($request)
    {
        $page = $request->query('page');
        if(!$page)return $this->model->all();
        return $this->model->where('page',$page)->get();
    }
  
    /**
     * Get banner by id
     *
     * @param $id
     * @return mixed
     */
    public function showUser($id)
    {
        return $this->showTrait($this->model, $id);
    }
    /**
     * Get banner by id
     *
     * @param $id
     * @return mixed
     */
    public function showUserWithRelation($id, $relation)
    {
        return $this->showWithRelationTrait($this->model, $id, $relation);
    }
    /**
     *  Validate User And Provider data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function store($request)
    {
     
        
            $this->model->type = $request->type;
            $this->model->data_id = $request->data_id;
            $this->model->page = $request->page;
            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('/banners' . $this->model->id, 's3');
                Storage::disk('s3')->setVisibility($image_path, 'public');
                $this->model->image =Storage::disk('s3')->url($image_path);
                $this->model->save();
            }       
    
            $this->model->save();
                return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $this->model, 200);
        
     
    }
    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $banner)
    {
        $banner = Banner::findOrFail($banner);

        $banner->fill($request->all());
        if ($request->hasFile('image')) {
            $delete_old_image=Storage::disk('s3')->delete($banner->image);
            if ($delete_old_image) {
                $image_path = $request->file('image')->store('/banners' . $this->model->id, 's3');
                Storage::disk('s3')->setVisibility($image_path, 'public');
                $banner->image = Storage::disk('s3')->url($image_path);
            }
        }
        $banner->save();

        return $banner;
    }
    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($id)
    {
        // dd(Banner::all()->count());
        if(Banner::all()->count()>1){
            $destroy= $this->destroyTrait($this->model, $id);
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $destroy, 200);
        }else{
            return $this->prepare_response(__('auth.Something went wrong'),'لا يمكنك ترك هذا القسم فارغ', null, 400);

        }
    }

   
}
