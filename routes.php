<?php

$router->pattern('id', '[0-9]+');
$router->pattern('examId', '[0-9]+');
$router->pattern('userId', '[0-9]+');
$router->pattern('ansId', '[0-9]+');
// Site Routes
Route::get('log', function() {
    //
    Log::info('Hi ');
});
Route::get('time', function() {
    //
    return view('admin/exam/time');
});
Route::get('/', function () {
    return view('site.index');
});
Route::get('/leaders', function () {
    return view('site.leaders');
});
Route::get('/contact', function () {
    return view('site.contactUs');
});
Route::post('/contact','AdminCtrl@contact');
Route::get('/levels', function () {
    return view('site.levels');
});
Route::get('/aboutqaf3', function () {
    return view('site.aboutqaf3');
});
Route::get('/protemplate', function () {
    return view('site.protemplate');
});
Route::get('/know', function () {
    return view('site.know');
});

Route::get('/reg/{id}', function ($id) {
	$city = App\City::where('id',$id)->where('status',1)->get();
	if (count($city) > 0 ) {
		return view('site.register',compact('id'));
	}
	return 'Page Not Found';
});
Route::post('/reg/{id}','StudentCtrl@reg');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'admins','prefix'=>'admin'], function() {
	Route::get('/main', function() {
	    return view('admin.layout');
	});
	Route::get('/home','AdminCtrl@index');
	// Setting
	Route::resource('/setting','SettingCtrl');
	// Students
	Route::get('/students/{id}/msg','StudentCtrl@sendMsg');
	Route::post('/studentMsg/{id}','StudentCtrl@send');
	Route::resource('/students','StudentCtrl');
	Route::get('/users','StudentCtrl@user');
	Route::post('/showStuByCity','StudentCtrl@showStuByCity');
	Route::post('/showUserByCity','StudentCtrl@showUserByCity');
	// Mentors
	Route::resource('/mentors','MentorCtrl');
	// Subjects
	Route::resource('/subjects','SubjectCtrl');
	Route::get('subjects/delete/{name}/{id}','SubjectCtrl@deleteSub');
	// UpSub - with out create & store
	Route::get('/userSub','UploadSubCtrl@index');
	Route::get('/userSub/{id}','UploadSubCtrl@userUpSub');
	Route::delete('/userSub/{id}','UploadSubCtrl@destroy');
	Route::get('/showUserSub/{id}/{subId}/{userId}','UploadSubCtrl@showUserUpSub');
	Route::post('/comment/{id}','UploadSubCtrl@comment');
	// Exams
	Route::resource('/exams','ExamCtrl');
	Route::get('exams/delete/{name}/{id}','ExamCtrl@deleteSub');
	// Images
	Route::resource('/images','ImageCtrl');
	Route::get('images/delete/{name}/{id}','ImageCtrl@deleteImg');
	// Videos
	Route::resource('/videos','VideoCtrl');
	Route::get('videos/delete/{name}/{id}','VideoCtrl@deleteVideo');
	// Answers 
	Route::get('/answers/{ansId}/{userId}','AnswerCtrl@showUsersAnswer');
	Route::delete('/answers/{examId}/{userId}','AnswerCtrl@destroy');
	Route::resource('/answers','AnswerCtrl');
	// Message
	Route::resource('/messages','MessageCtrl');
	Route::get('/selectUserCity','MessageCtrl@showSelectUserMsg');
	Route::post('/userMsg','MessageCtrl@selectUserMsg');
	Route::post('/sendUserMsg','MessageCtrl@sendSelectUserMsg');
	// Search
	Route::post('search','StudentCtrl@search');
	Route::post('search/{id}','StudentCtrl@delete');
	// City
	Route::resource('/city','CityCtrl');
	// Slider
	Route::resource('/sliders','SliderCtrl');
	// Search
	Route::get('searchAjax', function() {
	    //
	    return view('admin.search');
	});
	Route::get('searchAjaxR','StudentCtrl@searchAjax');
	// 
	Route::get('hi','StudentCtrl@searchCityAjaxR');

	// Result
	Route::get('/result/{idExam}/{idUser}','ResultCtrl@create');
	Route::post('/result/{idExam}/{idUser}','ResultCtrl@store');
	Route::get('/result/{idExam}/{idUser}/edit','ResultCtrl@edit');
	Route::put('/result/{idExam}/{idUser}','ResultCtrl@update');
	Route::delete('/result/{idExam}/{idUser}','ResultCtrl@destroy');
	Route::get('/result/{id}','ResultCtrl@index');
	Route::delete('/result/{id}','ResultCtrl@destroy');


});
Route::get('/exam', function () {
    return 'close';
});
/*
|--------------------------------------------------------------------------
| Mentor Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'mentors','prefix'=>'mentor'], function() {
	Route::get('home', 'MentorCtrl@profile');
	Route::post('home/{id}', 'MentorCtrl@updateProfile');
	// Student
	Route::get('students', 'MentorCtrl@student');
	Route::get('students/{id}', 'MentorCtrl@studentShow');
	Route::get('students/{id}/edit', 'MentorCtrl@studentEdit');
	Route::post('students/{id}', 'MentorCtrl@studentUpdate');
	// UpSub - with out create & store
	Route::get('/userSub','MentorCtrl@subject');
	Route::get('/userSub/{id}','MentorCtrl@userUpSub');
	Route::get('/showUserSub/{id}/{subId}/{userId}','MentorCtrl@showUserUpSub');
	Route::post('/comment/{id}','UploadSubCtrl@comment');
	// Message
	Route::get('/students/{id}/msg','MentorCtrl@sendMsg');
	Route::post('/studentMsg/{id}','MentorCtrl@send');
	Route::get('/messages','MentorCtrl@indexMsg');
	Route::get('/messages/{id}','MentorCtrl@showMsg');
	Route::post('/replay/{id}','MentorCtrl@replay');

});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth'], function() {
	// Subject
	Route::get('/subjects','SubjectCtrl@subUser');
	Route::get('/subjects/{subjects}','SubjectCtrl@showSubUser');
	// Exams
	Route::get('/exams','ExamCtrl@examUser');
	Route::get('/exams/{exams}','ExamCtrl@showExamUser');
	// Answers
	Route::get('/answers/{id}','AnswerCtrl@userAnswer');
	Route::post('/answers/{id}','AnswerCtrl@store');
	// UploadSub
	Route::get('/upSub/{id}','UploadSubCtrl@create');
	Route::post('/upSub/{id}','UploadSubCtrl@store');
	// Traning
	Route::get('/traning','HomeController@traning');
	// Messages
	Route::get('/messages','MessageCtrl@msgUser');
	Route::get('/messages/{messages}','MessageCtrl@showUserMsg');
	Route::post('/replay/{id}','MessageCtrl@replay');
	// Exams Result
	Route::get('/result','ResultCtrl@userResult');

});
Route::resource('profile', 'HomeController');
Route::auth();

