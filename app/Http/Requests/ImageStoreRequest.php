<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImageStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image.file' => 'required|file|image',
            'image.tags' => 'required|array|max:5',
            'image.tags.*' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'image.file.required' => '画像ファイルは必須です。',
            'image.file.file' => '画像ファイルをアップロードしてください。',
            'image.file.image' => 'アップロードされたファイルは画像である必要があります。',
            'image.tags.required' => 'タグは必須です。',
            'image.tags.array' => 'タグは配列である必要があります。',
            'image.tags.max' => 'タグは最大5つまで指定できます。',
            'image.tags.*.string' => 'タグは文字列である必要があります。',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'messages' => collect($validator->errors()->messages())
                    ->flatten()
                    ->toArray()
            ], 422)
        );
    }
}
