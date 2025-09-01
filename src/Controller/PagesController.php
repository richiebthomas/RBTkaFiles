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
        
        // If this is the visits page, load visit statistics
        if ($path[0] === 'visits') {
            $this->loadVisitStats();
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
     * Load visit statistics
     */
    private function loadVisitStats(): void
    {
        $totalVisits = $this->VisitTracker->getTotalVisits();
        $todayVisits = $this->VisitTracker->getTodayVisits();
        $thisWeekVisits = $this->VisitTracker->getThisWeekVisits();
        
        // Get weekly visits data for the chart
        $weeklyVisitsData = $this->getWeeklyVisitsData();
        
        $this->set(compact('totalVisits', 'todayVisits', 'thisWeekVisits'));
        $this->request = $this->request->withAttribute('totalVisits', $totalVisits);
        $this->request = $this->request->withAttribute('todayVisits', $todayVisits);
        $this->request = $this->request->withAttribute('thisWeekVisits', $thisWeekVisits);
        $this->request = $this->request->withAttribute('weeklyVisitsData', $weeklyVisitsData);
    }
    
    /**
     * Get weekly visits data for the chart
     */
    private function getWeeklyVisitsData(): array
    {
        $visitsTable = $this->getTableLocator()->get('Visits');
        
        // Get the last 7 days instead of current week
        $endDate = new \DateTime('today');
        $startDate = clone $endDate;
        $startDate->sub(new \DateInterval('P6D')); // Go back 6 days to get 7 total days
        
        // Get visits for each of the last 7 days
        $weeklyData = [];
        $labels = [];
        $data = [];
        
        $currentDate = clone $startDate;
        $dayCount = 0;
        
        while ($currentDate <= $endDate && $dayCount < 7) {
            $nextDate = clone $currentDate;
            $nextDate->add(new \DateInterval('P1D'));
            
            // Count visits for this day
            $dayVisits = $visitsTable->find()
                ->where([
                    'created >=' => $currentDate->format('Y-m-d 00:00:00'),
                    'created <' => $nextDate->format('Y-m-d 00:00:00')
                ])
                ->count();
            
            $labels[] = $currentDate->format('D');
            $data[] = $dayVisits;
            
            $currentDate->add(new \DateInterval('P1D'));
            $dayCount++;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
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

        // Get print statistics from PrintJobs table
        $printJobsTable = $this->getTableLocator()->get('PrintJobs');
        
        // Get total prints count
        $totalPrints = $printJobsTable->find()->count();
        
        // Get top printers with their print counts
        $topPrintersQuery = $printJobsTable->find()
            ->select([
                'user_id',
                'print_count' => 'COUNT(*)'
            ])
            ->group(['user_id'])
            ->order(['print_count' => 'DESC'])
            ->limit(10);
        
        $topPrinters = [];
        foreach ($topPrintersQuery as $row) {
            $user = $this->Users->get($row->user_id);
            $topPrinters[] = [
                'name' => $user->name,
                'roll_number' => $user->roll_number,
                'print_count' => $row->print_count
            ];
        }

        $printStats = [
            'totalPrints' => $totalPrints,
            'topPrinters' => $topPrinters
        ];

        // Set the data for the view
        $this->request = $this->request->withAttribute('fileCount', $fileCount);
        $this->request = $this->request->withAttribute('printStats', $printStats);
        
        // Load visit statistics
        $this->loadVisitStats();
    }
}
