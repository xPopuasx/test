<?php
/*
* Проверки реализованы в контроллерах, что не есть правильно, сделал так для упрощения Вам проверки из моделей
* подтягиваю исключительно Eloquent для работы с бд
*/
namespace App\Http\Controllers;

use Validator;
use App\Task;
use App\Estimation;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function index()
    {
        return Task::all();
    }

    public function show($id)
    {
        return Task::find($id);
    }

    public function store(Request $request)
    {

        $Validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required'
        ]);

        if ($Validator->fails()) {

            $error = [
                    "Errors"=>[
                        "title" => "Укажите заголовок задачи",
                        "description"=> "Укажите описание задачи"
                    ],
                    "Global"=>"Невозможно начать скрипт"
            ];
            return response()->json($error, 400);
        }
        $Backlog = Task::create($request->all());
        $Estimation = Estimation::create(['task_id'=> $Backlog->id]);

        return response()->json(['id' => 'TASK-'.$Backlog->id], 200);
    }

    public function Close(Request $request)
    {
        $Validator = Validator::make($request->all(),[
            'taskId' => 'required',
        ]);

        if ($Validator->fails()) {

            $error = [
                "Errors"=>[
                    "taskId" => "Неверно указан обязательный параметр",
                ],
                "Global"=>"Невозможно начать скрипт"
            ];
            return response()->json($error, 400);
        }
        /*
         * Можно ввести столбец в таблице tasks для алиаса по которому вести поиск TASK-id но решение
         * но данное решение приянто из-за моего понимания уникальности ключа {id}
         */

        $task_id = preg_replace('/[^0-9]/', '', $request->all('taskId'));

        if($Backlog=Task::where('id' , '=', $task_id['taskId'])->first())
        {
            if(!Task::where('status' , '=', 1)->first())
            {
                $Backlog->status = 1;
                $Backlog->save();
            }
            else
            {
                /*
                 * Тут можно ввести параметр масив с указанием кода, для перехвата на стороне пользователя
                 */
                return response()->json('Задача числится закрытой', 400);
            }
        }
        else
        {
            /*
             * Тут можно ввести параметр масив с указанием кода, для перехвата на стороне пользователя
             */
            return response()->json('Заданы несуществующие элементы', 400);
        }

        return response()->json('Задача успешно закрыта', 200);
    }

}
