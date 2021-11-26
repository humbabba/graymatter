<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Logger extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'user_id',
        'model',
        'model_id',
        'model_link',
        'action',
        'notes'
    ];

    public static function getSearchedLoggers(Request $request)
    {
        //Deal with filter params
        $search = $request->get('search');
        $model = $request->get('model');
        $action = $request->get('action');
        $from = $request->get('from');
        $to = $request->get('to');
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $direction = $request->get('direction') ?? 'desc';

        $output = new \stdClass();

        //Users
        $output->loggers = Logger::where(function ($query) use ($search) {
            $query->where('username', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%')
                ->orWhere('user_id', '=', $search)
                ->orWhere('model_id', '=', $search);
        })
            ->where(function ($query) use ($model) {
                if ($model) {
                    $query->where('model', '=', $model);
                }
            })
            ->where(function ($query) use ($action) {
                if ($action) {
                    $query->where('action', '=', $action);
                }
            })
            ->where(function ($query) use ($from) {
                if ($from) {
                    $query->where('created_at', '>=', $from . ' 00:00:00');
                }
            })
            ->where(function ($query) use ($to) {
                if ($to) {
                    $query->where('created_at', '<=', $to . ' 23:59:59');
                }
            })
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        //Other output values
        $output->search = $search;
        $output->model = $model;
        $output->action = $action;
        $output->from = $from;
        $output->to = $to;
        $output->total = $output->loggers->total();

        $output->error = '';
        if (0 === $output->total) {
            $output->error = 'No loggers found. Try <a href="' . route('loggers.index') . '">clearing the filters</a>.';
        }

        return $output;
    }
}
