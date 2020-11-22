<?php
/*
 * Проверки реализованы в контроллерах, что не есть правильно, сделал так для упрощения Вам проверки из моделей
 * подтягиваю исключительно Eloquent для работы с бд
 */
namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Sprint;
use App\Task;
use App\TasksInSprint;

class SprintController extends Controller
{

    public function show($id)
    {
        return Sprint::find($id);
    }

    public function store(Request $request)
    {
        $Validator = Validator::make($request->all(),[
            'week' => 'required|max:2|min:2',
            'year' => 'required|max:4|min:4'
        ]);
        if ($Validator->fails()) {

            $error = [
                "Errors"=>[
                    "week" => "Неверно указана или отсутствует неделя",
                    "year"=> "Неверно указан или отсутствует год"
                ],
                "Global"=>"Невозможно начать скрипт"
            ];
            return response()->json($error, 400);
        }
        $week = $request->all('week');
        $year = $request->all('year');
        $year_format = substr($year['year'],2);

        $Sprint = new Sprint;
        $Sprint->alias_sprint = $year_format.'-'.$week['week'];
        $Sprint->save();
        return response()->json(['Id' => $Sprint->alias_sprint], 200);
    }

    public function addTask(Request $request, TasksInSprint $TaskInSprint)
    {
        /*
         * В этом методе можно реализовать много проверок, но сделал проверку на присутствие спринта и задачи
         * и проверку на присутствие их связи
         */
        $Validator = Validator::make($request->all(),[
            'sprintId' => 'required',
            'taskId' => 'required'
        ]);
        if ($Validator->fails()) {

            $error = [
                "Errors"=>[
                    "sprintId" => "Неверно указан или отсутствует параметр",
                    "taskId"=> "Неверно указан или отсутствует параметр"
                ],
                "Global"=>"Невозможно начать скрипт"
            ];
            return response()->json($error, 400);
        }

        $task_id = preg_replace('/[^0-9]/', '', $request->all('taskId'));

        $sprint_id = $request->all('sprintId');

        if(Task::find($task_id['taskId']) && Sprint::where('alias_sprint' , '=', $sprint_id['sprintId'])->first())
        {
            if(!TasksInSprint::where('id_sprint' , '=', $sprint_id['sprintId'])->where('id_task' , '=', $task_id['taskId'])->first())
            {
                $TaskInSprint->id_task = $task_id['taskId'];
                $TaskInSprint->id_sprint = $sprint_id['sprintId'];
                $TaskInSprint->save();
            }
            else
            {
                return response()->json('Данная связь уже установлена', 400);
            }
        }
        else
        {
            return response()->json('Заданы несуществующие элементы', 400);
        }

        return response()->json('Задача успешно добавлена в спринт', 200);
    }

    public function Start(Request $request)
    {
        $Validator = Validator::make($request->all(),[
            'sprintId' => 'required',
        ]);
        if ($Validator->fails()) {

            $error = [
                "Errors"=>[
                    "sprintId" => "Неверно указан обязательный параметр"
                ],
                "Global"=>"Невозможно начать скрипт"
            ];
            return response()->json($error, 400);
        }
        $sprint_id = $request->all('sprintId');
        if($Sprint=Sprint::where('alias_sprint' , '=', $sprint_id['sprintId'])->first())
        {
            if(!Sprint::where('status_sprint' , '=', 1)->first())
            {
                $Sprint->status_sprint = 1;
                $Sprint->save();
            }
            else
            {
                /*
                 * Тут можно ввести параметр масив с указанием кода, для перехвата на стороне пользователя
                 */
                return response()->json('Спринт уже запущен!', 400);
            }
        }
        else
        {
            /*
             * Тут можно ввести параметр масив с указанием кода, для перехвата на стороне пользователя
             */
            return response()->json('Заданы несуществующие элементы', 400);
        }

        return response()->json('Спринт успешно запущен', 200);
    }
}
