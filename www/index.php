<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

date_default_timezone_set('Europe/Moscow');
define('BASE_DIR', __DIR__ . '/../');

require BASE_DIR . 'vendor/autoload.php';

$sqlWrapper = new \rms\db\SqliteWrapper(BASE_DIR . '/db/data.sqlite3');
$entityManager = new \rms\db\EntityManager($sqlWrapper);

$app = new \Slim\App();

$app->get('/members', function (Request $request, Response $response) use ($entityManager) {
    /** @var \rms\db\repositories\MembersRepository $membersRepo */
    $membersRepo = $entityManager->getRepository('members');

    $response->getBody()->write(
        json_encode(
            $membersRepo->getAll()->asArray()
        )
    );

    return $response;
});

$app->get('/members/{id}', function (Request $request, Response $response) use ($entityManager) {
    $memberId = $request->getAttribute('id');

    /** @var \rms\db\repositories\MembersRepository $membersRepo */
    $membersRepo = $entityManager->getRepository('members');

    $response->getBody()->write(
        json_encode(
            $membersRepo->findOneById($memberId)->asArray()
        )
    );

    return $response;
});

$app->post('/members', function (Request $request, Response $response) use ($entityManager) {
    /** @var \rms\db\repositories\MembersRepository $membersRepo */
    $membersRepo = $entityManager->getRepository('members');

    $memberData = [];
    $memberFields = ['name', 'eid', 'email', 'slack', 'jenkins', 'jira', 'cellular', 'photo'];
    foreach ($memberFields as $field) {
        $memberData[$field] = $request->getAttribute($field);
    }
    $entity = $membersRepo->createEntity($memberData);
    $entity->save();

    $response
        ->withStatus(201, 'Created')
        ->withHeader('Location', '/members/' . $entity->getId())
        ->getBody()->write(json_encode([]));

    return $response;
});

$app->delete('/members/{id}', function (Request $request, Response $response) use ($entityManager) {
    $memberId = $request->getAttribute('id');

    /** @var \rms\db\repositories\MembersRepository $membersRepo */
    $membersRepo = $entityManager->getRepository('members');
    $membersRepo->delete($memberId);

    $response
        ->withStatus(200)
        ->getBody()->write(json_encode([]));

    return $response;
});


$app->run();
