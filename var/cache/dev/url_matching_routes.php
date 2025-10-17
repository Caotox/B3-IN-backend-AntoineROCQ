<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/emprunts/emprunter' => [[['_route' => 'api_emprunt_borrow', '_controller' => 'App\\Controller\\EmpruntController::emprunter'], null, ['POST' => 0], null, false, false, null]],
        '/api/livres' => [
            [['_route' => 'api_livre_list', '_controller' => 'App\\Controller\\LivreController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_livre_create', '_controller' => 'App\\Controller\\LivreController::create'], null, ['POST' => 0], null, false, false, null],
        ],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/(?'
                    .'|emprunts/retourner/(\\d+)(*:74)'
                    .'|livres/(\\d+)(?'
                        .'|(*:96)'
                    .')'
                    .'|utilisateurs/(?'
                        .'|(\\d+)/emprunts(*:134)'
                        .'|auteur/(\\d+)/livres(*:161)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        74 => [[['_route' => 'api_emprunt_return', '_controller' => 'App\\Controller\\EmpruntController::retourner'], ['id'], ['POST' => 0], null, false, true, null]],
        96 => [
            [['_route' => 'api_livre_show', '_controller' => 'App\\Controller\\LivreController::show'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_livre_update', '_controller' => 'App\\Controller\\LivreController::update'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'api_livre_delete', '_controller' => 'App\\Controller\\LivreController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        134 => [[['_route' => 'api_utilisateur_emprunts', '_controller' => 'App\\Controller\\UtilisateurController::getEmprunts'], ['id'], ['GET' => 0], null, false, false, null]],
        161 => [
            [['_route' => 'api_utilisateur_auteur_livres', '_controller' => 'App\\Controller\\UtilisateurController::getLivresAuteurEmpruntes'], ['auteurId'], ['GET' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
