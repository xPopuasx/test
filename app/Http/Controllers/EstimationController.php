<?php
/*
* Проверки реализованы в контроллерах, что не есть правильно, сделал так для упрощения Вам проверки из моделей
* подтягиваю исключительно Eloquent для работы с бд
*/
namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Estimation;

class EstimationController extends Controller
{

    public function show($id)
    {
        return Estimation::find($id);
    }

    public function update(Request $request)
    {

        $Validator = Validator::make($request->all(),[
            'id' => 'required',
            'estimation' => 'required'
        ]);
        if ($Validator->fails()) {

            $error = [
                "Errors"=>[
                    "id" => "Укажите идентификатор задачи",
                    "estimation"=> "Укажите оценку"
                ],
                "Global"=>"Невозможно начать скрипт"
            ];
            return response()->json($error, 400);
        }

        $replace_id = preg_replace('/[^0-9]/', '', $request->all('id'));
        $new_est = $request->all('estimation');

        $Estimation = Estimation::where('task_id' , '=', $replace_id['id'])->first();
        $Estimation->estimation = $new_est['estimation'];
        $Estimation->save();
    }

}
