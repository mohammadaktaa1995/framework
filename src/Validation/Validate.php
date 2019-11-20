<?php

namespace Wolfren\Validation;

use Wolfren\Http\Request;
use Wolfren\Http\Response;
use Wolfren\Session\Session;
use Wolfren\Url\Url;
use Rakit\Validation\Validator;

class Validate
{
    private function __construct()
    {
    }

    public static function validate($rules, $json)
    {
        $validator = new Validator;

        $validation = $validator->validate($_POST + $_FILES, $rules);

        $errors = $validation->errors();
        if ($validation->fails()) {
            if ($json) {
                return Response::json(['errors' => $errors->firstOfAll()]);
            } else {
                Session::set('errors', $errors);
                Session::set('old', Request::all());

                return Url::redirect(Url::previous());
            }
        }
    }
}