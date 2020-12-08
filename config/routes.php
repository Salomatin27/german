<?php

declare(strict_types=1);

use App\Handler\LoginPageHandler;
use App\Middleware\PrgMiddleware;
use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/', App\Handler\CalendarHandler::class, 'home');
    //$app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');
    //$app->get('/doctrine', \App\Handler\DoctrineHandler::class, 'doctrine.test');

    //region auth
    $app->route(
        '/login',
        [
            CsrfMiddleware::class,
            PrgMiddleware::class,
            LoginPageHandler::class,
            AuthenticationMiddleware::class,
        ],
        [
            'get',
            'post',
        ],
        'login'
    );
    $app->get('/logout', \App\Handler\LogoutPageHandler::class, 'logout');
    //endregion

    //region patient
    $app->get(
        '/patients[/{page}]',
        [
            \App\Middleware\StoreParametersMiddleware::class,
            \Patient\Handler\ListHandler::class,
        ],
        'patients'
    );
    $app->route(
        '/patient/{id}',
        [
            CsrfMiddleware::class,
            PrgMiddleware::class,
            \Patient\Handler\EditHandler::class
        ],
        ['GET'],
        'patient.edit'
    );
    $app->route(
        '/patient',
        Patient\Handler\CreateHandler::class,
        ['post'],
        'patient.create'
    );
    $app->route(
        '/patient/{id}',
        [
            CsrfMiddleware::class,
            \Patient\Handler\PatchHandler::class
        ],
        [ 'post'],
        'patient.patch'
    );
    $app->delete(
        '/patient/{id}',
        \Patient\Handler\DeleteHandler::class,
        'patient.delete'
    );
    $app->get(
        '/patient-images/{id}',
        \Patient\Handler\ListImagesHandler::class,
        'patient-images.list'
    );
    $app->post(
        '/patient-images/{id}',
        \Patient\Handler\SaveImageHandler::class,
        'patient-images.save'
    );
    $app->post(
        '/patient-photo/{id}',
        \Patient\Handler\SavePhotoHandler::class,
        'patient-photo.save'
    );
    $app->get(
        '/patient-photo/{id}',
        \Patient\Handler\ViewPhotoHandler::class,
        'patient-photo.get'
    );
    $app->get(
        '/image/{id}',
        \Patient\Handler\ViewImageHandler::class,
        'image.view'
    );
    $app->delete(
        '/image/{id}',
        \Patient\Handler\DeleteImageHandler::class,
        'image.delete'
    );
    $app->post(
        '/operation/{id}',
        Patient\Handler\CreateOperationHandler::class,
        'operation.create'
    );
    $app->delete(
        '/operation/{id}',
        Patient\Handler\DeleteOperationHandler::class,
        'operation.delete'
    );
    $app->get(
        '/patient-print/{id}',
        \Patient\Handler\PrintPatientHandler::class,
        'patient.print'
    );
    //endregion

    //region reference
    $app->get(
        '/reference/{item}',
        \Patient\Reference\OpenHandler::class,
        'reference.open'
    );
    $app->route(
        '/reference/{item}',
        \Patient\Reference\UpdateHandler::class,
        ['POST', 'PATCH'],
        'reference.save'
    );
    $app->get(
        '/reference-options/{item}',
        \Patient\Reference\OptionsHandler::class,
        'reference.options'
    );
    $app->delete(
        '/reference/{item}/{id}',
        \Patient\Reference\DeleteHandler::class,
        'reference.delete'
    );
    //endregion

    //region operation-implant
    $app->post(
        '/operation-implant-create/{id}',
        Patient\Implant\CreateHandler::class,
        'operation-implant.create'
    );
    $app->route(
        '/operation-implant/{id}',
        [
            CsrfMiddleware::class,
            PrgMiddleware::class,
            \Patient\Implant\EditHandler::class
        ],
        ['GET', 'PATCH', 'POST'],
        'operation-implant.edit'
    );
    $app->delete(
        '/operation-implant/{id}',
        \Patient\Implant\DeleteHandler::class,
        'operation-implant.delete'
    );
    //endregion

    //region user
    $app->get('/users', \User\Action\ListUsersAction::class, 'users');
    $app->route(
        '/user/edit[/{id}]',
        [
            CsrfMiddleware::class,
            PrgMiddleware::class,
            \User\Action\EditUserAction::class,
        ],
        ['GET','POST','PATCH'],
        'user.edit'
    );
    $app->route(
        '/user/changePassword/{id}',
        [
            CsrfMiddleware::class,
            PrgMiddleware::class,
            \User\Action\ChangePasswordAction::class,
        ],
        ['GET','POST'],
        'user.change-password'
    );
    $app->route(
        '/user/resetPassword',
        \User\Action\ResetPasswordAction::class,
        ['GET','POST'],
        'user.reset-password'
    );
    $app->get(
        '/user/message/{id}',
        \User\Action\MessageAction::class,
        'user.message'
    );
    $app->route(
        '/set-password',
        \User\Action\SetPasswordAction::class,
        ['GET','POST'],
        'user.set-password'
    );
    //endregion
};
