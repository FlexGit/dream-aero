<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    CONST API_VALIDATION_MESSAGES = [
		'required' => 'Обязательно для заполнения',
		'required_with' => 'Обязательно для заполнения',
		'required_without' => 'Обязательно для заполнения',
		'required_if' => 'Обязательно для заполнения',
		'confirmed' => 'Пароли должны совпадать',
		'same' => 'Пароли должны совпадать',
		'email' => 'Некорректный формат',
		'min' => 'Не менее :min символов',
		'max' => 'Не более :max символов',
		'date' => 'Некорректный формат',
		'numeric' => 'Должно быть числом',
		'digits' => 'Должно быть :digits цифры',
		'valid_city' => 'Город не найден или доступ запрещен',
		'valid_phone' => 'Некорректный формат',
		'valid_password' => 'Некорректный формат',
		'valid_password_confirmation' => 'Некорректный формат',
		'image' => 'Некорректный тип файла',
		'mimes' => 'Некорректный тип файла',
		'after_or_equal' => 'Некорректное значение',
	];
}
