<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        //подготовка условия для проверки количества загружаемых файлов
        $post = $this->route()->parameters['post'];
        $countImgs = $post->media->count();
        $countForValidation = 1 - $countImgs;
        if (! is_null($this->deleted_preview) && count($this->deleted_preview) > 0) {
            $countForValidation = $countForValidation + count($this->deleted_preview);
        }

        //подготовка условия для параметра "deleted_preview"
        if ($countImgs > 0 && ! is_null($this->imgs) && is_array($this->imgs)) {
            $newImgs = count($this->imgs);

            if ($newImgs > 0) {
                $delRequired = 'required';  //если у поста нет картинок или не отправлены новые - удалять старые не нужно
            } else {
                $delRequired = 'nullable';
            }
        } else {
            $delRequired = 'nullable';
        }

        //если параметр "deleted_preview" обязательный - должен содержать реальные ID
        if ($delRequired == 'required') {
            $delExists = '|exists:media,id';
        } else {
            $delExists = '';
        }

        return [
            'title' => 'required|string',
            'body' => 'required|string',
            'imgs' => "nullable|array|size:$countForValidation",
            'imgs.*' => 'nullable|file',
            'deleted_preview' => "$delRequired|array",
            'deleted_preview.*' => "$delRequired|int{$delExists}",
        ];
    }
}
