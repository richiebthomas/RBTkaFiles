<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @property \App\Model\Table\FileItemsTable $FileItems
 * @property \App\Model\Table\UsersTable $Users
 */
class PagesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        
        // Load the required tables
        $this->FileItems = $this->fetchTable('FileItems');
        $this->Users = $this->fetchTable('Users');
    }

    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        
        // If this is the about page, load the statistics
        if ($path[0] === 'about') {
            $this->loadAboutPageStats();
        }

        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    /**
     * Load statistics for the about page
     */
    private function loadAboutPageStats(): void
    {
        // Get total files count
        $fileCount = $this->FileItems->find()
            ->where(['type' => 'file'])
            ->count();

        // Get print statistics
        $users = $this->Users->find()
            ->select(['id', 'name', 'roll_number', 'prints'])
            ->all();

        $totalPrints = 0;
        $topPrinters = [];

        foreach ($users as $user) {
            if (!empty($user->prints)) {
                $printCount = count($user->prints);
                $totalPrints += $printCount;

                $topPrinters[] = [
                    'name' => $user->name,
                    'roll_number' => $user->roll_number,
                    'prints' => $user->prints
                ];
            }
        }

        // Sort users by print count
        usort($topPrinters, function($a, $b) {
            return count($b['prints']) - count($a['prints']);
        });

        // Take top 10
        $topPrinters = array_slice($topPrinters, 0, 10);

        $printStats = [
            'totalPrints' => $totalPrints,
            'topPrinters' => $topPrinters
        ];

        // Set the data for the view
        $this->request = $this->request->withAttribute('fileCount', $fileCount);
        $this->request = $this->request->withAttribute('printStats', $printStats);
    }
}
