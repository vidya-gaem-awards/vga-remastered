<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SoundtrackSettingsRequest extends FormRequest
{
    protected $redirectRoute = 'soundtrack.manage';

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'download_url' => ['nullable', 'url', 'max:255'],
            'download_size' => ['nullable', 'string', 'max:255'],
            'preshow_download_url' => ['nullable', 'url', 'max:255'],
            'preshow_download_size' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'download_url' => 'download URL',
            'download_size' => 'download size',
            'preshow_download_url' => 'preshow download URL',
            'preshow_download_size' => 'preshow download size',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        // Backwards compatibility as we don't currently show Laravel validation errors in the template
        $this->session()->flash('error', $validator->errors()->all());

        parent::failedValidation($validator);
    }
}
