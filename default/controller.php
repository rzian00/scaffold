<?php
/**
 * Created by rzian/scaffold
 * User: @{USER}
 * Date: @{DATETIME}
 */

namespace App\Http\Controllers;

use @{CLASSMAP};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class @{NAME} extends Controller
{
    /**
     * The constructor class
     *
     * @todo Manually set your middleware here...
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.     
     *
     * @route get @{ROUTE}
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! $request->wantsJson())
        {
            return view('@{VARNAME}.index');
        }

        $limit = (int)$request->input('limit', 15);
        $page = (int)$request->input('page', 1);
        $status = Response::HTTP_NO_CONTENT;
        $pages = 0;
        $list = [];

        $query = @{CLASS}::query();

        // @todo Customize your query here...
        // $query->where();

        if (($rows = $query->count()) > 0)
        {
            $status = Response::HTTP_OK;
            $pages = (int)ceil($rows / $limit);
            $list = $query->limit($limit)
                          ->offset(--$page * $limit)
                          ->get();
        }

        return response()->json(compact('rows', 'pages', 'list'), $status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @route get @{ROUTE}/create
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->show(new @{CLASS}, '@{VARNAME}.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @route post @{ROUTE}
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($this->request->all(), @{CLASS}::$rules);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->all(), Response::HTTP_BAD_REQUEST);
        }

        try
        {
            @{CLASS}::create($request->all());

            return response()->json(['message' => 'Created successfully'], Response::HTTP_CREATED);
        }
        catch(QueryException $exception)
        {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @route get @{ROUTE}/{@{VARNAME}}
     *
     * @param  \@{CLASSMAP}  @{VAR}
     * @return \Illuminate\View\View
     */
    public function show(@{CLASS} @{VAR}, $view = '@{VARNAME}.show')
    {
        return view($view, ['@{VARNAME}' => @{VAR}]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @route get @{ROUTE}/{@{VARNAME}}/edit
     *
     * @param  \@{CLASSMAP}  @{VAR}
     * @return \Illuminate\View\View
     */
    public function edit(@{CLASS} @{VAR})
    {
        return $this->show(@{VAR}, '@{VARNAME}.edit');
    }


    /**
     * Update the specified resource in storage.
     *
     * @route put|patch @{ROUTE}/{@{VARNAME}}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \@{CLASSMAP}  @{VAR}
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, @{CLASS} @{VAR})
    {
        $validator = Validator::make($this->request->all(), @{VAR}->rules);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->all(), Response::HTTP_BAD_REQUEST);
        }

        try
        {
            @{VAR}->update($request->all());
            if (! @{VAR}->isDirty())
            {
                return response()->json(false, Response::HTTP_NOT_MODIFIED);
            }

            return response()->json(['message' => 'Updated successfully']);
        }
        catch(QueryException $exception)
        {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @route delete @{ROUTE}/{@{VARNAME}}
     *
     * @param  \@{CLASSMAP}  @{VAR}
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(@{CLASS} @{VAR})
    {
        try
        {
            @{VAR}->delete();

            return response()->json(['message' => 'Removed successfully']);
        }
        catch(QueryException $exception)
        {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
