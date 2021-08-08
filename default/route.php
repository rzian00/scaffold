

Route::get('@{ROUTE}', '@{CONTROLLER}@index');
Route::get('@{ROUTE}/create', '@{CONTROLLER}@create');
Route::post('@{ROUTE}', '@{CONTROLLER}@store');
Route::get('@{ROUTE}/{@{VARNAME}}', '@{CONTROLLER}@show');
Route::get('@{ROUTE}/{@{VARNAME}}/edit', '@{CONTROLLER}@edit');
Route::patch('@{ROUTE}/{@{VARNAME}}', '@{CONTROLLER}@update');
Route::delete('@{ROUTE}/{@{VARNAME}}', '@{CONTROLLER}@destroy');