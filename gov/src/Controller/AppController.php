<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cookie', ['expiry' => '1 day']);
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authorize' => 'Controller',
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'authError' => '请登录。',
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect'=> '/login'
            // 'unauthorizedRedirect' => $this->referer()
        ]);

        // Allow the display action so our pages controller
        // continues to work.
        // $this->Auth->allow(['home']);
        $ww = ["任意天气","晴","多云","阴","阵雨","雷阵雨","雷阵雨伴有冰雹","雨夹雪","小雨","中雨","大雨",
               "暴雨","大暴雨","特大暴雨","阵雪","小雪","中雪","大雪","暴雪","雾","沙尘暴","浮尘",
               "扬沙","强沙尘暴","特强沙尘暴轻雾","浓雾","强浓雾","轻微霾","轻度霾","中度霾","重度霾",
               "特强霾","霰"];
        $this->set('weather_constant',$ww);
    }


    public function isAuthorized($user)
    {
        if ( $user ) {
            return true;
        }else{
            return $this->redirect('/login');
        }
        return false;
    }

    public function isLogin($user)
    {
        if (!isset($user['id'])) {
            return false;
        }
        return $this->request->session()->read('babe.user') == $user['id'];
    }
}
