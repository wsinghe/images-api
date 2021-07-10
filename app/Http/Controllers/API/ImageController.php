<?php

namespace App\Http\Controllers\API;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Image as ImageResource;

class ImageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $images = Image::orderBy('id', 'DESC')->paginate(5);

        return $this->sendResponse($images, 'Images retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if($request->has('image')) {
            $image_parts = explode(";base64,", $request->image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.'.$image_type;
            $file = public_path('images') . "/" . $imageName;

            file_put_contents($file, $image_base64);

            $path = url('/images') . "/" . $imageName;
        }

        $image = new Image();
        $image->title = $request->input('title');
        $image->description = $request->input('description');
        $image->image = $path;
        $image->save();

        return $this->sendResponse(new ImageResource($image), 'Image created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if($request->has('image') && !empty($request->image)) {
            $image_parts = explode(";base64,", $request->image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.'.$image_type;
            $file = public_path('images') . "/" . $imageName;

            file_put_contents($file, $image_base64);

            $path = url('/images') . "/" . $imageName;
        }

        $image = Image::find($id);

        $image->title = $request->input('title');
        $image->description = $request->input('description');
        if(isset($path)) {
            $image->image = $path;
        }
        $image->save();

        return $this->sendResponse(new ImageResource($image), 'Image updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        if (is_null($image)) {
            return $this->sendError('image not found.');
        }

        $image->delete();

        return $this->sendResponse([], 'Image deleted successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function imageSearch(Request $request)
    {
        $images = Image::where("title", "LIKE" , "%" . $request->title . "%")->orderBy('id', 'DESC')->paginate(5);

        if (is_null($images)) {
            return $this->sendError('image not found.');
        }

        return $this->sendResponse($images, 'Image retrieved successfully.');
    }
}
