<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

/**
 * Visit Tracker Component
 *
 * This component tracks unique visits to the website by:
 * - Creating a unique session for each visitor
 * - Preventing duplicate visits from the same session
 * - Logging visit information to the database
 * - Killing the session when the browser is closed
 */
class VisitTrackerComponent extends Component
{
    /**
     * Initialize the component
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session before starting it
            ini_set('session.cookie_lifetime', '0');
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', '0'); // Set to 1 if using HTTPS
            
            session_start();
        }
        
        // Track the visit
        $this->trackVisit();
    }

    /**
     * Track a visit if it's a new session
     *
     * @return void
     */
    protected function trackVisit(): void
    {
        $request = $this->getController()->getRequest();
        $sessionId = session_id();
        
        // Check if this session already has a visit recorded
        $visitsTable = TableRegistry::getTableLocator()->get('Visits');
        
        if (!$visitsTable->hasVisitForSession($sessionId)) {
            // This is a new visit, log it
            $visitData = [
                'ip_address' => $this->getClientIp($request),
                'user_agent' => $request->getHeaderLine('User-Agent'),
                'referer' => $request->getHeaderLine('Referer'),
                'session_id' => $sessionId,
            ];
            
            $visit = $visitsTable->newEntity($visitData);
            $visitsTable->save($visit);
        }
    }

    /**
     * Get the client's IP address
     *
     * @param \Cake\Http\ServerRequest $request
     * @return string
     */
    protected function getClientIp(ServerRequest $request): string
    {
        // Check for forwarded IP (when behind proxy/load balancer)
        $forwardedFor = $request->getHeaderLine('X-Forwarded-For');
        if (!empty($forwardedFor)) {
            $ips = explode(',', $forwardedFor);
            return trim($ips[0]);
        }
        
        $realIp = $request->getHeaderLine('X-Real-IP');
        if (!empty($realIp)) {
            return $realIp;
        }
        
        // Use the correct method to get client IP
        return $request->getAttribute('client_ip') ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Get total visit count
     *
     * @return int
     */
    public function getTotalVisits(): int
    {
        $visitsTable = TableRegistry::getTableLocator()->get('Visits');
        return $visitsTable->getTotalVisits();
    }

    /**
     * Get visits for a specific date range
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Cake\ORM\Query
     */
    public function getVisitsInDateRange(\DateTime $startDate, \DateTime $endDate)
    {
        $visitsTable = TableRegistry::getTableLocator()->get('Visits');
        return $visitsTable->getVisitsInDateRange($startDate, $endDate);
    }

    /**
     * Get today's visit count
     *
     * @return int
     */
    public function getTodayVisits(): int
    {
        $today = new \DateTime('today');
        $tomorrow = new \DateTime('tomorrow');
        
        $visitsTable = TableRegistry::getTableLocator()->get('Visits');
        return $visitsTable->getVisitsInDateRange($today, $tomorrow)->count();
    }

    /**
     * Get this week's visit count
     *
     * @return int
     */
    public function getThisWeekVisits(): int
    {
        $startOfWeek = new \DateTime('monday this week');
        $endOfWeek = new \DateTime('sunday this week 23:59:59');
        
        $visitsTable = TableRegistry::getTableLocator()->get('Visits');
        return $visitsTable->getVisitsInDateRange($startOfWeek, $endOfWeek)->count();
    }

    /**
     * Get this month's visit count
     *
     * @return int
     */
    public function getThisMonthVisits(): int
    {
        $startOfMonth = new \DateTime('first day of this month');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');
        
        $visitsTable = TableRegistry::getTableLocator()->get('Visits');
        return $visitsTable->getVisitsInDateRange($startOfMonth, $endOfMonth)->count();
    }
}
