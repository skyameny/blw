<?php
namespace  core\includes\session;

use core\includes\session\PHPSession;
use core\exception\CommonException;
use think\Session as tp_session;

/**
 * session 管理器
 * 
 * @author keepwin100
 *
 */
abstract class SessionManagement
{

    const PHPSESSION_SESSION_KEY = 'bl_session_Session';

    private static $session = null;

    /**
     * Retrurns the current session
     * 
     * @throws Error
     * @return Session
     */
    public static function getSession()
    {
        if (is_null(self::$session)) {
           // if (PHPSession::singleton()->hasAttribute(self::PHPSESSION_SESSION_KEY)) {
            if(tp_session::get(self::PHPSESSION_SESSION_KEY)){
                //$session = PHPSession::singleton()->getAttribute(self::PHPSESSION_SESSION_KEY);
                $session = tp_session::get(self::PHPSESSION_SESSION_KEY);
                if (! $session instanceof Session) {
                    throw new CommonException('Non session stored in php-session');
                }
                self::$session = $session;
            } else {
                self::$session = new AnonymousSession();
            }
        }
        return self::$session;
    } 
    
    /**
     * Starts a new session and stores it in the session if stateful
     * 
     * @param common_session_Session $session
     * @return boolean
     */
    public static function startSession(Session $session) {

        self::$session = $session;
        // do not start session in cli mode (testcase script)
        if(PHP_SAPI != 'cli'){
            if ($session instanceof BasicSession) {
                
                // start session if not yet started
                if (session_id() === '') {
                    session_name(GENERIS_SESSION_NAME);
                    session_start();
                } else {
                    // prevent session fixation.
                    session_regenerate_id();
                }
                //
                tp_session::set(self::PHPSESSION_SESSION_KEY, $session);
              // PHPSession::singleton()->setAttribute(self::PHPSESSION_SESSION_KEY, $session);
            }
        }
        return true;
    }
    
    /**
     * Ends the session by replacing it with an anonymous session
     * 
     * @return boolean
     */
    public static function endSession() {

        // clean session data.
        if (session_id() != ''){
            session_destroy();
        }
        tp_session::delete(self::PHPSESSION_SESSION_KEY);
        
        return self::startSession(new AnonymousSession());
    }
    
    /**
     * Is the current session anonymous or associated to a user?
     * 
     * @return boolean
     */
    public static function isAnonymous() {
        return is_null(self::getSession()->getUserId());
    }    
    
}
