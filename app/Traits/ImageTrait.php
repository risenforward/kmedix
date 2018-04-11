<?php

use \Illuminate\Support\Facades\Storage;

trait ImageTrait
{
    private function saveImg($img, $path, $storage)
    {
        Storage::disk($storage)->put($path, file_get_contents($img));
    }

    private function deleteImg($path, $storage)
    {
        $s = Storage::disk($storage);
        if ($s->has($path)) {
            $s->delete($path);
        }
    }

    public function createModelImage($img, $model, $storage, $attr = 'logo')
    {
        $filename = $img->getClientOriginalName();
        $this->saveImg($img, '/' . strtolower(class_basename($model)) . '-' . $model->id . '/' . $filename, $storage);

        $model->$attr = $filename;
        $model->save();
    }

    public function updateModelImage($img, $model, $storage, $attr = 'logo')
    {
        $path = strtolower(class_basename($model));
        if ($model->$attr) {
            $path = '/' . $path . '-' . $model->id . '/' . $model->$attr;
            $this->deleteImg($path, $storage);
        }

        $filename = $img->getClientOriginalName();
        $this->saveImg($img, '/' . $path . '-' . $model->id . '/' . $filename, $storage);

        $model->$attr = $filename;
        return $model;
    }

    public function deleteModelLogo($model, $id, $storage, $attr = 'logo')
    {
        $m = $model::find($id);
        if ($m && $m->$attr) {
            $path = '/' . strtolower(class_basename($model)) . '-' . $m->id . '/' . $m->$attr;
            $this->deleteImg($path, $storage);

            $m->$attr = null;
            $m->save();
        } else {
            return response()->json(['error' => 'Image not found']);
        }

        return response()->json(['status' => true]);
    }
}