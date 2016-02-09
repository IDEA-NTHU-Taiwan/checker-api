<?php
require_once __DIR__.'/config.php'; 

/* 
 *  GET methods (READ)
 */

// Get courses info
$app->get('/courses', function (Silex\Application $app) {
    $sql = "SELECT * FROM course";
    $courses = $app['db']->fetchAll($sql);

    return json_encode($courses);
});

// Get a course info
$app->get('/course/{id}', function (Silex\Application $app, $id) {
    $sql = "SELECT * FROM course WHERE id = ?";
    $course = $app['db']->fetchAssoc($sql, array((int) $id));

    return json_encode($course);
});

// Get classes info
$app->get('/course/{id}/classes', function (Silex\Application $app, $id) {
    $sql = "SELECT * FROM class WHERE course_id = ?";
    $classes = $app['db']->fetchAll($sql, array((int) $id));

    return json_encode($classes);
});

// Get a class info
$app->get('/class/{id}', function (Silex\Application $app, $id) {
    $sql = "SELECT * FROM class WHERE id = ?";
    $class = $app['db']->fetchAssoc($sql, array((int) $id));

    return json_encode($class);
});

// Get checks info
$app->get('/class/{id}/checks', function (Silex\Application $app, $id) {
    $sql = "SELECT * FROM `check` WHERE class_id = ?";
    $checks = $app['db']->fetchAll($sql, array((int) $id));

    return json_encode($checks);
});

// Get questions
$app->get('/class/{id}/questions', function (Silex\Application $app, $id) {
    $sql = "SELECT * FROM `question` WHERE check_id IN (SELECT id FROM `check` WHERE class_id = ?)";
    $questions = $app['db']->fetchAll($sql, array((int) $id));

    return json_encode($questions);
});


/* 
 *  POST methods (CREATE)
 */

$app->post('/create/course', function (Silex\Application $app, Request $request) {
    $title = $request->get('title'); // Class Id

    $app['db']->insert('course', array(
        'id'    =>  null,
        'title' =>  $title
    ));

    $rep = array(
        'id'        =>  $app['db']->lastInsertId(),
        'status'    =>  OK
    );

    return new Response(json_encode($rep), 201);
});

$app->post('/create/class', function (Silex\Application $app, Request $request) {
    $course_id = $request->get('course_id'); // Class Id
    $time = date("Y-m-d H:i:s");
    $token = generateRandomString(8);
    
    $app['db']->insert('class', array(
        'id'        =>  null,
        'course_id' =>  $course_id,        
        'time'      =>  $time,
        'token'     =>  $token
    ));

    $rep = array(
        'id'        =>  $app['db']->lastInsertId(),
        'status'    =>  OK
    );

    return new Response(json_encode($rep), 201);
});

$app->post('/create/check', function (Silex\Application $app, Request $request) {
    $sid = $request->get('sid');
    $class_id = $request->get('class_id');
    $name = $request->get('name');
    $time = date("Y-m-d H:i:s");
    $auth_cookie = generateRandomString(4);
    
    $app['db']->insert('class', array(
        'id'            =>  null,
        'sid'           =>  $sid,
        'class_id'      =>  $class_id,
        'name'          =>  $name,
        'time'          =>  $time,
        'auth_cookie'   =>  $token
    ));

    $rep = array(
        'id'        =>  $app['db']->lastInsertId(),
        'status'    =>  OK
    );

    return new Response(json_encode($rep), 201);
});

$app->post('/create/question', function (Silex\Application $app, Request $request) {
    $check_id = $request->get('check_id'); // Class Id
    $content = $request->get('content');
    $time = date("Y-m-d H:i:s");

    $app['db']->insert('question', array(
        'id'        =>  null,
        'check_id'  =>  $check_id,
        'content'   =>  $content,
        'deleted'   =>  0,
        'selected'  =>  0,
        'time'      =>  $time
    ));

    $rep = array(
        'id'        =>  $app['db']->lastInsertId(),
        'status'    =>  OK
    );

    return new Response(json_encode($rep), 201);
});

/* 
 *  PUT methods (UPDATE)
 */

/* 
 *  DELETE methods (DELETE)
 */

$app->run(); 
    